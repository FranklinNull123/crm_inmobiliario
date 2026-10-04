<?php
declare(strict_types=1);

final class DashboardModel
{
    public function __construct(private readonly PDO $pdo)
    {
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

    public function getUltimasVentas(): array
    {
        $sql = <<<'SQL'
SELECT
    c.nombre AS cliente,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    v.estado AS estado,
    DATE_FORMAT(v.fecha, '%d %b %Y') AS fecha
FROM ventas AS v
LEFT JOIN clientes AS c ON c.id = v.id_cliente
LEFT JOIN proyectos AS p ON p.id = v.id_proyecto
LEFT JOIN unidades AS u ON u.id = v.id_unidad
ORDER BY v.fecha DESC, v.id DESC
LIMIT 5
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[DashboardModel] ' . $exception->getMessage());
            return [
                ['Cliente', 'Proyecto', 'Unidad', 'Estado', 'Fecha'],
            ];
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
