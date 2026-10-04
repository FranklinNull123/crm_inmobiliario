<?php
declare(strict_types=1);

final class CajaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getCuotas(): array
    {
        $sql = <<<'SQL'
SELECT
    cu.id,
    cu.id_cliente,
    cu.id_venta,
    c.nombre AS cliente,
    u.codigo AS unidad,
    p.nombre AS proyecto,
    cu.numero_cuota,
    CAST(cu.monto AS DECIMAL(10,2)) AS monto,
    cu.fecha_vencimiento,
    cu.pagada,
    CASE
        WHEN cu.pagada = 1 THEN 'Pagada'
        WHEN cu.fecha_vencimiento < CURDATE() THEN 'Vencida'
        ELSE 'Pendiente'
    END AS estado
FROM cuotas AS cu
LEFT JOIN clientes AS c ON c.id = cu.id_cliente
LEFT JOIN ventas AS v ON v.id = cu.id_venta
LEFT JOIN unidades AS u ON u.id = v.id_unidad
LEFT JOIN proyectos AS p ON p.id = v.id_proyecto
ORDER BY cu.fecha_vencimiento ASC, cu.id ASC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[CajaModel] ' . $exception->getMessage());
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
                'pagada' => (bool) ((int) ($row['pagada'] ?? 0)),
                'estado' => (string) ($row['estado'] ?? 'Pendiente'),
            ];
        }, $rows);
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    SUM(CASE WHEN pagada = 0 THEN monto ELSE 0 END) AS pendiente_total,
    COUNT(CASE WHEN pagada = 0 THEN 1 END) AS cuotas_pendientes,
    COUNT(CASE WHEN pagada = 1 THEN 1 END) AS cuotas_pagadas,
    SUM(CASE WHEN pagada = 0 AND fecha_vencimiento < CURDATE() THEN monto ELSE 0 END) AS vencido_total
FROM cuotas
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            error_log('[CajaModel] ' . $exception->getMessage());
            return ['pendiente_total' => 0.0, 'cuotas_pendientes' => 0, 'cuotas_pagadas' => 0, 'vencido_total' => 0.0];
        }

        return [
            'pendiente_total' => (float) ($row['pendiente_total'] ?? 0),
            'cuotas_pendientes' => (int) ($row['cuotas_pendientes'] ?? 0),
            'cuotas_pagadas' => (int) ($row['cuotas_pagadas'] ?? 0),
            'vencido_total' => (float) ($row['vencido_total'] ?? 0),
        ];
    }

    public function registrarPago(int $cuotaId): bool
    {
        if ($cuotaId <= 0) {
            return false;
        }

        try {
            $statement = $this->pdo->prepare('UPDATE cuotas SET pagada = 1 WHERE id = :id AND pagada = 0');
            $statement->execute([':id' => $cuotaId]);
            return $statement->rowCount() > 0;
        } catch (PDOException $exception) {
            error_log('[CajaModel] ' . $exception->getMessage());
            return false;
        }
    }
}
