<?php
declare(strict_types=1);

final class ComisionModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    COUNT(v.id) AS ventas_count,
    COALESCE(SUM(u.precio), 0) AS volumen_total,
    COALESCE(SUM(u.precio * 0.02), 0) AS comision_total
FROM ventas v
LEFT JOIN unidades u ON u.id = v.id_unidad
SQL;

        $statement = $this->pdo->query($sql);
        $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'ventas_count' => (int) ($row['ventas_count'] ?? 0),
            'volumen_total' => (float) ($row['volumen_total'] ?? 0),
            'comision_total' => (float) ($row['comision_total'] ?? 0),
        ];
    }

    public function getComisiones(): array
    {
        $sql = <<<'SQL'
SELECT
    a.id,
    a.nombre AS asesor,
    COUNT(v.id) AS ventas,
    COALESCE(SUM(u.precio), 0) AS volumen,
    COALESCE(SUM(u.precio * 0.02), 0) AS comision,
    MAX(v.fecha) AS ultima_venta
FROM asesores a
LEFT JOIN clientes c ON c.id_asesor = a.id
LEFT JOIN ventas v ON v.id_cliente = c.id
LEFT JOIN unidades u ON u.id = v.id_unidad
GROUP BY a.id, a.nombre
ORDER BY comision DESC, ventas DESC, a.nombre ASC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ComisionModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                'ventas' => (int) ($row['ventas'] ?? 0),
                'volumen' => (float) ($row['volumen'] ?? 0),
                'comision' => (float) ($row['comision'] ?? 0),
                'ultima_venta' => (string) ($row['ultima_venta'] ?? '-'),
            ];
        }, $rows);
    }
}
