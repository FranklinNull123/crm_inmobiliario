<?php
declare(strict_types=1);

final class ContratoModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getContratos(): array
    {
        $sql = <<<'SQL'
SELECT
    d.id,
    c.nombre AS cliente,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    d.nombre AS contrato,
    d.tipo_archivo AS tipo,
    d.tamano AS tamano,
    DATE_FORMAT(v.fecha, '%Y-%m-%d') AS fecha_venta
FROM documentos d
LEFT JOIN clientes c ON c.id = d.id_cliente
LEFT JOIN ventas v ON v.id_cliente = d.id_cliente
LEFT JOIN unidades u ON u.id = v.id_unidad
LEFT JOIN proyectos p ON p.id = v.id_proyecto
WHERE d.nombre LIKE '%Contrato%' OR d.tipo_archivo = 'contract'
GROUP BY d.id, c.nombre, p.nombre, u.codigo, d.nombre, d.tipo_archivo, d.tamano, v.fecha
ORDER BY d.id DESC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ContratoModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static fn (array $row): array => [
            'id' => (int) ($row['id'] ?? 0),
            'cliente' => (string) ($row['cliente'] ?? 'Cliente'),
            'proyecto' => (string) ($row['proyecto'] ?? '—'),
            'unidad' => (string) ($row['unidad'] ?? '—'),
            'contrato' => (string) ($row['contrato'] ?? 'Contrato'),
            'tipo' => (string) ($row['tipo'] ?? 'contract'),
            'tamano' => (string) ($row['tamano'] ?? '2.4 MB'),
            'fecha_venta' => (string) ($row['fecha_venta'] ?? date('Y-m-d')),
        ], $rows);
    }

    public function crear(array $datos): int
    {
        $clienteId = (int) ($datos['cliente_id'] ?? 0);
        $nombre = trim((string) ($datos['nombre'] ?? ''));
        if ($clienteId <= 0 || $nombre === '') {
            throw new InvalidArgumentException('Cliente y nombre del contrato son obligatorios.');
        }

        $statement = $this->pdo->prepare('INSERT INTO documentos (id_cliente, nombre, tipo_archivo, tamano) VALUES (:cliente_id, :nombre, :tipo_archivo, :tamano)');
        $statement->execute([
            ':cliente_id' => $clienteId,
            ':nombre' => $nombre,
            ':tipo_archivo' => 'contract',
            ':tamano' => '2.4 MB',
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
