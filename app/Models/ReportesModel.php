<?php
declare(strict_types=1);

final class ReportesModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $queries = [
            'ventas_totales' => 'SELECT COALESCE(SUM(u.precio), 0) AS total FROM ventas v LEFT JOIN unidades u ON u.id = v.id_unidad',
            'leads_activos' => 'SELECT COUNT(*) AS total FROM leads',
            'unidades_disponibles' => "SELECT COUNT(*) AS total FROM unidades WHERE estado = 'Disponible'",
            'cuotas_pendientes' => 'SELECT COALESCE(SUM(monto), 0) AS total FROM cuotas WHERE pagada = 0',
            'ventas_count' => 'SELECT COUNT(*) AS total FROM ventas',
        ];

        $result = [];
        foreach ($queries as $key => $sql) {
            try {
                $statement = $this->pdo->query($sql);
                $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
                $result[$key] = (float) ($row['total'] ?? 0);
            } catch (PDOException $exception) {
                error_log('[ReportesModel] ' . $exception->getMessage());
                $result[$key] = 0.0;
            }
        }

        return [
            'ventas_totales' => $result['ventas_totales'],
            'ventas_count' => (int) $result['ventas_count'],
            'leads_activos' => (int) $result['leads_activos'],
            'unidades_disponibles' => (int) $result['unidades_disponibles'],
            'cuotas_pendientes' => $result['cuotas_pendientes'],
        ];
    }

    public function getVentasPorMes(): array
    {
        $sql = <<<'SQL'
SELECT
    DATE_FORMAT(v.fecha, '%b') AS mes,
    SUM(u.precio) AS total_ventas
FROM ventas AS v
LEFT JOIN unidades AS u ON u.id = v.id_unidad
GROUP BY MONTH(v.fecha), DATE_FORMAT(v.fecha, '%b')
ORDER BY MONTH(v.fecha) ASC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ReportesModel] ' . $exception->getMessage());
            $rows = [];
        }

        if ($rows === []) {
            $fallback = [
                ['mes' => 'Ene', 'total_ventas' => 0],
            ];
            $rows = $fallback;
        }

        return array_map(static function (array $row): array {
            return [
                'mes' => (string) ($row['mes'] ?? 'Mes'),
                'total_ventas' => (float) ($row['total_ventas'] ?? 0),
            ];
        }, $rows);
    }

    public function getInventarioPorEstado(): array
    {
        $sql = "SELECT estado, COUNT(*) AS total FROM unidades GROUP BY estado ORDER BY total DESC";

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ReportesModel] ' . $exception->getMessage());
            $rows = [];
        }

        return array_map(static function (array $row): array {
            return [
                'estado' => (string) ($row['estado'] ?? 'Sin estado'),
                'total' => (int) ($row['total'] ?? 0),
            ];
        }, $rows);
    }

    public function getTopProyectos(): array
    {
        $sql = <<<'SQL'
SELECT
    p.nombre AS proyecto,
    COUNT(v.id) AS ventas,
    COALESCE(SUM(u.precio), 0) AS total_ventas
FROM proyectos AS p
LEFT JOIN ventas AS v ON v.id_proyecto = p.id
LEFT JOIN unidades AS u ON u.id = v.id_unidad
GROUP BY p.id, p.nombre
ORDER BY total_ventas DESC, ventas DESC
LIMIT 5
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ReportesModel] ' . $exception->getMessage());
            $rows = [];
        }

        return array_map(static function (array $row): array {
            return [
                'proyecto' => (string) ($row['proyecto'] ?? 'Proyecto'),
                'ventas' => (int) ($row['ventas'] ?? 0),
                'total_ventas' => (float) ($row['total_ventas'] ?? 0),
            ];
        }, $rows);
    }
}
