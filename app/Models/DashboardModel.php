<?php
declare(strict_types=1);

final class DashboardModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getFilterOptions(): array
    {
        return [
            'projects' => $this->pdo->query('SELECT id, nombre FROM proyectos ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC),
            'sites' => $this->pdo->query('SELECT DISTINCT ubicacion AS nombre FROM proyectos WHERE ubicacion IS NOT NULL AND ubicacion <> "" ORDER BY ubicacion')->fetchAll(PDO::FETCH_ASSOC),
            'advisors' => $this->pdo->query('SELECT id, nombre FROM asesores WHERE estado = 1 ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    private function filterSql(array $filters, string $dateColumn = 'v.fecha', string $projectColumn = 'v.id_proyecto', ?string $advisorColumn = 'c.id_asesor'): array
    {
        $where = [];
        $params = [];
        $projectId = filter_var($filters['project'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $advisorId = filter_var($filters['advisor'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $site = trim((string) ($filters['site'] ?? ''));

        if ($projectId !== false) {
            $where[] = $projectColumn . ' = :project_id';
            $params['project_id'] = $projectId;
        }
        if ($advisorId !== false && $advisorColumn !== null) {
            $where[] = $advisorColumn . ' = :advisor_id';
            $params['advisor_id'] = $advisorId;
        }
        if ($site !== '') {
            $where[] = 'p.ubicacion = :site';
            $params['site'] = $site;
        }

        $range = (string) ($filters['range'] ?? '6m');
        $interval = match ($range) {
            '7d' => '7 DAY',
            '30d' => '30 DAY',
            'year' => '1 YEAR',
            default => '6 MONTH',
        };
        $where[] = sprintf('%s >= DATE_SUB(CURDATE(), INTERVAL %s)', $dateColumn, $interval);

        return [$where === [] ? '1 = 1' : implode(' AND ', $where), $params];
    }

    public function getVentasVsMetas(): array
    {
        $sql = <<<'SQL'
SELECT
    mes AS m,
    venta AS v,
    meta AS g
FROM metas_ventas
ORDER BY orden ASC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[DashboardModel] ' . $exception->getMessage());
            return [
                ['m' => 'Abr', 'v' => 1.8, 'g' => 2.0],
                ['m' => 'May', 'v' => 2.1, 'g' => 2.4],
                ['m' => 'Jun', 'v' => 2.7, 'g' => 2.8],
                ['m' => 'Jul', 'v' => 2.4, 'g' => 2.9],
                ['m' => 'Ago', 'v' => 2.9, 'g' => 3.0],
                ['m' => 'Set', 'v' => 3.2, 'g' => 3.1],
            ];
        }

        return array_map(static function (array $row): array {
            return [
                'm' => (string) ($row['m'] ?? ''),
                'v' => (float) ($row['v'] ?? 0),
                'g' => (float) ($row['g'] ?? 0),
            ];
        }, $rows);
    }

    public function getKpis(array $filters = []): array
    {
        [$where, $params] = $this->filterSql($filters, 'v.fecha', 'v.id_proyecto');
        $queries = [
            'ventas_cerradas' => ['SELECT COUNT(*) FROM ventas v JOIN clientes c ON c.id = v.id_cliente JOIN proyectos p ON p.id = v.id_proyecto WHERE v.estado = "Vendido" AND ' . $where, $params],
            'ingresos_cobrados' => ['SELECT COALESCE(SUM(cu.monto), 0) FROM cuotas cu JOIN ventas v ON v.id = cu.id_venta JOIN clientes c ON c.id = v.id_cliente JOIN proyectos p ON p.id = v.id_proyecto WHERE cu.pagada = 1 AND ' . $where, $params],
            'unidades_disponibles' => ['SELECT COUNT(*) FROM unidades u JOIN proyectos p ON p.id = u.id_proyecto WHERE u.activo = 1 AND u.estado = "Disponible" AND ' . $this->filterSql($filters, 'CURDATE()', 'u.id_proyecto', null)[0], $this->filterSql($filters, 'CURDATE()', 'u.id_proyecto', null)[1]],
            'leads_nuevos' => ['SELECT COUNT(*) FROM leads l LEFT JOIN proyectos p ON p.nombre = l.proyecto_interes LEFT JOIN asesores a ON a.id_usuario = l.id_usuario_asignado WHERE l.etapa = "Nuevo Lead" AND ' . $this->filterSql($filters, 'CURDATE()', 'p.id', 'a.id')[0], $this->filterSql($filters, 'CURDATE()', 'p.id', 'a.id')[1]],
        ];
        $kpis = [];

        foreach ($queries as $key => [$sql, $queryParams]) {
            try {
                $statement = $this->pdo->prepare($sql);
                $statement->execute($queryParams);
                $kpis[$key] = (float) $statement->fetchColumn();
            } catch (PDOException $exception) {
                error_log('[DashboardModel] ' . $exception->getMessage());
                $kpis[$key] = 0;
            }
        }

        return $kpis;
    }

    public function getUltimasVentas(array $filters = []): array
    {
        [$where, $params] = $this->filterSql($filters);
        $sql = <<<'SQL'
SELECT
    c.nombre AS cliente,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    v.estado AS estado,
    DATE_FORMAT(v.fecha, '%%d %%b %%Y') AS fecha
FROM ventas AS v
LEFT JOIN clientes AS c ON c.id = v.id_cliente
LEFT JOIN proyectos AS p ON p.id = v.id_proyecto
LEFT JOIN unidades AS u ON u.id = v.id_unidad
LEFT JOIN asesores AS a ON a.id = c.id_asesor
WHERE %s
ORDER BY v.fecha DESC, v.id DESC
LIMIT 5
SQL;
        $sql = sprintf($sql, $where);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[DashboardModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                (string) ($row['cliente'] ?? 'Cliente'),
                (string) ($row['proyecto'] ?? 'Proyecto'),
                (string) ($row['unidad'] ?? 'Unidad'),
                (string) ($row['estado'] ?? 'Vendido'),
                (string) ($row['fecha'] ?? 'Hoy'),
            ];
        }, $rows);
    }

    public function getAlertas(): array
    {
        $sqls = [
            'cuotas_vencidas' => 'SELECT COUNT(*) AS total, COALESCE(SUM(monto), 0) AS monto FROM cuotas WHERE pagada = 0 AND fecha_vencimiento < CURDATE()',
            'leads_pendientes' => "SELECT COUNT(*) AS total FROM leads WHERE etapa NOT IN ('Venta Cerrada', 'Cancelado', 'Descartado')",
            'unidades_disponibles' => "SELECT COUNT(*) AS total FROM unidades WHERE estado = 'Disponible'",
        ];

        $alertas = [];

        foreach ($sqls as $key => $sql) {
            try {
                $statement = $this->pdo->query($sql);
                $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
                $value = (int) ($row['total'] ?? 0);
                $amount = (float) ($row['monto'] ?? 0);

                if ($key === 'cuotas_vencidas' && $value > 0) {
                    $alertas[] = [
                        'type' => 'warning',
                        'title' => 'Cuotas vencidas',
                        'message' => sprintf('%d cuotas vencidas por S/ %s', $value, number_format($amount, 2, ',', '.')),
                        'icon' => 'alert-triangle',
                    ];
                }

                if ($key === 'leads_pendientes' && $value > 0) {
                    $alertas[] = [
                        'type' => 'info',
                        'title' => 'Leads en seguimiento',
                        'message' => sprintf('%d leads requieren atención comercial.', $value),
                        'icon' => 'sparkles',
                    ];
                }

                if ($key === 'unidades_disponibles' && $value > 0) {
                    $alertas[] = [
                        'type' => 'success',
                        'title' => 'Inventario disponible',
                        'message' => sprintf('%d unidades disponibles para vender.', $value),
                        'icon' => 'home',
                    ];
                }
            } catch (PDOException $exception) {
                error_log('[DashboardModel] ' . $exception->getMessage());
            }
        }

        return $alertas;
    }
}
