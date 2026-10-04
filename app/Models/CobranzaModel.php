<?php
declare(strict_types=1);

final class CobranzaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    SUM(CASE WHEN cu.pagada = 0 THEN cu.monto ELSE 0 END) AS pendiente_total,
    COUNT(CASE WHEN cu.pagada = 0 THEN 1 END) AS cuotas_pendientes,
    COUNT(CASE WHEN cu.pagada = 0 AND cu.fecha_vencimiento < CURDATE() THEN 1 END) AS vencidas,
    SUM(CASE WHEN cu.pagada = 0 AND cu.fecha_vencimiento < CURDATE() THEN cu.monto ELSE 0 END) AS vencido_total,
    COUNT(DISTINCT CASE WHEN cu.pagada = 0 THEN cu.id_cliente END) AS clientes_con_cartera
FROM cuotas AS cu
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            error_log('[CobranzaModel] ' . $exception->getMessage());
            return [
                'pendiente_total' => 0.0,
                'cuotas_pendientes' => 0,
                'vencidas' => 0,
                'vencido_total' => 0.0,
                'clientes_con_cartera' => 0,
            ];
        }

        return [
            'pendiente_total' => (float) ($row['pendiente_total'] ?? 0),
            'cuotas_pendientes' => (int) ($row['cuotas_pendientes'] ?? 0),
            'vencidas' => (int) ($row['vencidas'] ?? 0),
            'vencido_total' => (float) ($row['vencido_total'] ?? 0),
            'clientes_con_cartera' => (int) ($row['clientes_con_cartera'] ?? 0),
        ];
    }

    public function getCobranza(): array
    {
        $sql = <<<'SQL'
SELECT
    cu.id,
    cu.id_cliente,
    c.nombre AS cliente,
    u.codigo AS unidad,
    p.nombre AS proyecto,
    cu.numero_cuota,
    CAST(cu.monto AS DECIMAL(10,2)) AS monto,
    cu.fecha_vencimiento,
    DATEDIFF(CURDATE(), cu.fecha_vencimiento) AS dias_vencidos,
    CASE
        WHEN cu.fecha_vencimiento < CURDATE() THEN 'Vencida'
        ELSE 'Pendiente'
    END AS estado
FROM cuotas AS cu
LEFT JOIN clientes AS c ON c.id = cu.id_cliente
LEFT JOIN ventas AS v ON v.id = cu.id_venta
LEFT JOIN unidades AS u ON u.id = v.id_unidad
LEFT JOIN proyectos AS p ON p.id = v.id_proyecto
WHERE cu.pagada = 0
ORDER BY cu.fecha_vencimiento ASC, cu.id ASC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[CobranzaModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'cliente' => (string) ($row['cliente'] ?? 'Cliente'),
                'unidad' => (string) ($row['unidad'] ?? '—'),
                'proyecto' => (string) ($row['proyecto'] ?? '—'),
                'numero_cuota' => (int) ($row['numero_cuota'] ?? 0),
                'monto' => (float) ($row['monto'] ?? 0),
                'fecha_vencimiento' => (string) ($row['fecha_vencimiento'] ?? ''),
                'dias_vencidos' => max(0, (int) ($row['dias_vencidos'] ?? 0)),
                'estado' => (string) ($row['estado'] ?? 'Pendiente'),
            ];
        }, $rows);
    }
}
