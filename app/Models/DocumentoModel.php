<?php
declare(strict_types=1);

final class DocumentoModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getByCliente(int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT nombre, tipo_archivo AS type, tamano AS size
FROM documentos
WHERE id_cliente = :cliente_id
ORDER BY id DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([':cliente_id' => $clienteId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[DocumentoModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static fn (array $row): array => [
            'nombre' => (string) ($row['nombre'] ?? 'Contrato.pdf'),
            'tipo_archivo' => (string) ($row['type'] ?? 'file-signature'),
            'tamano' => (string) ($row['size'] ?? '2.4 MB'),
        ], $rows);
    }

    public function crear(int $clienteId, string $nombre, string $tipoArchivo, string $tamano): int
    {
        $nombre = trim($nombre);
        if ($clienteId <= 0 || $nombre === '') {
            throw new InvalidArgumentException('Cliente y nombre del documento son obligatorios.');
        }

        $statement = $this->pdo->prepare('INSERT INTO documentos (id_cliente, nombre, tipo_archivo, tamano) VALUES (:cliente_id, :nombre, :tipo_archivo, :tamano)');
        $statement->execute([
            ':cliente_id' => $clienteId,
            ':nombre' => $nombre,
            ':tipo_archivo' => $tipoArchivo !== '' ? $tipoArchivo : 'file-signature',
            ':tamano' => $tamano !== '' ? $tamano : '2.4 MB',
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
