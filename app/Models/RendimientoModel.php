<?php
declare(strict_types=1);

final class RendimientoModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $metaMensual = $this->pdo->query(
            'SELECT COALESCE(meta, 0) AS meta FROM metas_ventas ORDER BY orden DESC LIMIT 1'
        )->fetch(PDO::FETCH_ASSOC);

        $ventasActuales = $this->pdo->query(
            <<<'SQL'
SELECT COALESCE(SUM(u.precio), 0) AS ventas_actuales,
       COUNT(DISTINCT v.id) AS ventas_count
FROM ventas v
LEFT JOIN clientes c ON c.id = v.id_cliente
LEFT JOIN unidades u ON u.id = v.id_unidad
WHERE DATE_FORMAT(v.fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
SQL
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'meta_mensual' => (float) ($metaMensual['meta'] ?? 0),
            'ventas_actuales' => (float) ($ventasActuales['ventas_actuales'] ?? 0),
            'ventas_count' => (int) ($ventasActuales['ventas_count'] ?? 0),
            'cumplimiento' => (float) ((($ventasActuales['ventas_actuales'] ?? 0) > 0 && ($metaMensual['meta'] ?? 0) > 0)
                ? ((float) ($ventasActuales['ventas_actuales'] ?? 0) / (float) ($metaMensual['meta'] ?? 0)) * 100
                : 0),
        ];
    }

    public function getRendimiento(): array
    {
        $metaMensual = $this->pdo->query(
            'SELECT COALESCE(meta, 0) AS meta FROM metas_ventas ORDER BY orden DESC LIMIT 1'
        )->fetch(PDO::FETCH_ASSOC);

        $metaBase = (float) ($metaMensual['meta'] ?? 0);

        $sql = <<<'SQL'
SELECT
    a.id,
    a.nombre AS asesor,
    COUNT(DISTINCT c.id) AS clientes,
    COUNT(DISTINCT v.id) AS ventas,
    COALESCE(SUM(u.precio), 0) AS volumen
FROM asesores a
LEFT JOIN clientes c ON c.id_asesor = a.id
LEFT JOIN ventas v ON v.id_cliente = c.id
LEFT JOIN unidades u ON u.id = v.id_unidad
GROUP BY a.id, a.nombre
ORDER BY volumen DESC, a.nombre ASC
SQL;

        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row) use ($metaBase): array {
            $volumen = (float) ($row['volumen'] ?? 0);
            $cumplimiento = $metaBase > 0 ? ($volumen / $metaBase) * 100 : 0.0;

            return [
                'id' => (int) ($row['id'] ?? 0),
                'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                'clientes' => (int) ($row['clientes'] ?? 0),
                'ventas' => (int) ($row['ventas'] ?? 0),
                'volumen' => $volumen,
                'cumplimiento' => round($cumplimiento, 2),
            ];
        }, $rows);
    }
}
