<?php
declare(strict_types=1);

final class SeparacionModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getUnidadesDisponibles(): array
    {
        $sql = <<<'SQL'
SELECT
    u.id,
    u.codigo AS code,
    p.nombre AS project,
    u.tipo AS type,
    CAST(u.area_m2 AS DECIMAL(10,2)) AS area,
    CAST(u.precio AS DECIMAL(12,2)) AS price,
    u.estado AS status
FROM unidades AS u
LEFT JOIN proyectos AS p ON p.id = u.id_proyecto
WHERE u.activo = 1 AND u.estado = 'Disponible'
ORDER BY u.id DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[SeparacionModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'code' => (string) ($row['code'] ?? ''),
                'project' => (string) ($row['project'] ?? ''),
                'type' => (string) ($row['type'] ?? 'Dpto.'),
                'area' => (float) ($row['area'] ?? 0),
                'price' => (float) ($row['price'] ?? 0),
                'status' => (string) ($row['status'] ?? 'Disponible'),
            ];
        }, $rows);
    }

    public function registrar(array $datos): bool
    {
        $clienteId = (int) ($datos['id_cliente'] ?? 0);
        $unidadId = (int) ($datos['id_unidad'] ?? 0);
        $monto = (float) ($datos['monto'] ?? 0);
        $metodoPago = trim((string) ($datos['metodo_pago'] ?? ''));
        $fechaVencimiento = trim((string) ($datos['fecha_vencimiento'] ?? ''));
        $observaciones = trim((string) ($datos['observaciones'] ?? ''));

        if ($clienteId <= 0 || $unidadId <= 0 || $monto <= 0 || $metodoPago === '' || $fechaVencimiento === '') {
            throw new InvalidArgumentException('Faltan datos obligatorios para registrar la separación.');
        }

        $this->pdo->beginTransaction();
        try {
            $cliente = $this->pdo->prepare('SELECT 1 FROM clientes WHERE id = :id LIMIT 1 FOR UPDATE');
            $cliente->execute([':id' => $clienteId]);
            if ($cliente->fetchColumn() === false) {
                throw new InvalidArgumentException('El cliente seleccionado no existe.');
            }

            $unidad = $this->pdo->prepare('SELECT id, id_proyecto, estado, activo FROM unidades WHERE id = :id AND activo = 1 FOR UPDATE');
            $unidad->execute([':id' => $unidadId]);
            $unitRow = $unidad->fetch(PDO::FETCH_ASSOC);
            if ($unitRow === false) {
                throw new InvalidArgumentException('La unidad seleccionada no existe o está inactiva.');
            }

            $duplicado = $this->pdo->prepare('SELECT 1 FROM separaciones WHERE id_unidad = :id_unidad AND id_cliente = :id_cliente LIMIT 1 FOR UPDATE');
            $duplicado->execute([':id_unidad' => $unidadId, ':id_cliente' => $clienteId]);
            if ($duplicado->fetchColumn() !== false) {
                $this->pdo->rollBack();
                return false;
            }

            if ((string) ($unitRow['estado'] ?? '') !== 'Disponible') {
                throw new InvalidArgumentException('La unidad ya no está disponible para separar.');
            }

            $insert = $this->pdo->prepare('INSERT INTO separaciones (id_cliente, id_unidad, monto, metodo_pago, fecha_vencimiento, observaciones, creado_en) VALUES (:id_cliente, :id_unidad, :monto, :metodo_pago, :fecha_vencimiento, :observaciones, NOW())');
            $insert->execute([
                ':id_cliente' => $clienteId,
                ':id_unidad' => $unidadId,
                ':monto' => $monto,
                ':metodo_pago' => $metodoPago,
                ':fecha_vencimiento' => $fechaVencimiento,
                ':observaciones' => $observaciones,
            ]);

            $propiedad = $this->pdo->prepare('SELECT 1 FROM cliente_propiedad WHERE id_cliente = :id_cliente AND id_unidad = :id_unidad LIMIT 1');
            $propiedad->execute([':id_cliente' => $clienteId, ':id_unidad' => $unidadId]);
            if ($propiedad->fetchColumn() === false) {
                $insertPropiedad = $this->pdo->prepare('INSERT INTO cliente_propiedad (id_cliente, id_unidad, estado) VALUES (:id_cliente, :id_unidad, :estado)');
                $insertPropiedad->execute([
                    ':id_cliente' => $clienteId,
                    ':id_unidad' => $unidadId,
                    ':estado' => 'Separado',
                ]);
            }

            $update = $this->pdo->prepare('UPDATE unidades SET estado = :estado WHERE id = :id');
            $update->execute([
                ':estado' => 'Separado',
                ':id' => $unidadId,
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
