<?php
declare(strict_types=1);

final class CarteraModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    (SELECT COUNT(*) FROM clientes WHERE estado = 'Cliente Activo') AS clientes_activos,
    (SELECT COUNT(*) FROM clientes WHERE estado IN ('En Mora', 'Cliente Activo')) AS cartera_total,
    (SELECT COALESCE(SUM(monto), 0) FROM cuotas WHERE pagada = 0) AS deuda_pendiente,
    (SELECT COUNT(*) FROM clientes_leads WHERE estado_lead NOT IN ('Venta Cerrada')) AS oportunidades_abiertas
SQL;

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'clientes_activos' => (int) ($row['clientes_activos'] ?? 0),
            'cartera_total' => (int) ($row['cartera_total'] ?? 0),
            'deuda_pendiente' => (float) ($row['deuda_pendiente'] ?? 0),
            'oportunidades_abiertas' => (int) ($row['oportunidades_abiertas'] ?? 0),
        ];
    }

    public function getCartera(): array
    {
        $sql = <<<'SQL'
SELECT
    c.id,
    c.nombre AS cliente,
    a.nombre AS asesor,
    COALESCE(u.codigo, '-') AS unidad,
    COALESCE(u.precio, 0) AS valor_unidad,
    COALESCE(SUM(CASE WHEN cu.pagada = 0 THEN cu.monto ELSE 0 END), 0) AS deuda_total,
    MIN(CASE WHEN cu.pagada = 0 THEN cu.fecha_vencimiento END) AS proximo_vencimiento,
    CASE
        WHEN c.estado = 'En Mora' THEN 'En mora'
        WHEN c.estado = 'Cliente Activo' THEN 'Activa'
        ELSE 'Sin estado'
    END AS estado
FROM clientes c
LEFT JOIN asesores a ON a.id = c.id_asesor
LEFT JOIN cliente_propiedad cp ON cp.id_cliente = c.id
LEFT JOIN unidades u ON u.id = cp.id_unidad
LEFT JOIN cuotas cu ON cu.id_cliente = c.id
GROUP BY c.id, c.nombre, a.nombre, u.codigo, u.precio, c.estado
ORDER BY deuda_total DESC, c.nombre ASC
LIMIT 12
SQL;

        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'cliente' => (string) ($row['cliente'] ?? 'Cliente'),
                'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                'unidad' => (string) ($row['unidad'] ?? '-'),
                'valor_unidad' => (float) ($row['valor_unidad'] ?? 0),
                'deuda_total' => (float) ($row['deuda_total'] ?? 0),
                'proximo_vencimiento' => $row['proximo_vencimiento'] ?? null,
                'estado' => (string) ($row['estado'] ?? 'Activa'),
            ];
        }, $rows);
    }
}
