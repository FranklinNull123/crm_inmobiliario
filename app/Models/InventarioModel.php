<?php
declare(strict_types=1);

final class InventarioModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getUnidades(): array
    {
        $sql = <<<'SQL'
SELECT
    u.id,
    u.id_proyecto AS projectId,
    u.id_etapa AS stageId,
    COALESCE(e.nombre, '') AS stage,
    u.codigo AS code,
    p.nombre AS project,
    u.tipo AS type,
    CAST(u.area_m2 AS DECIMAL(10,2)) AS area,
    CAST(u.precio AS DECIMAL(12,2)) AS price,
    u.estado AS status
FROM unidades AS u
JOIN proyectos AS p ON p.id = u.id_proyecto
LEFT JOIN etapas_proyecto AS e ON e.id = u.id_etapa
WHERE u.activo = 1
ORDER BY u.id DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[InventarioModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'projectId' => (int) ($row['projectId'] ?? 0),
                'stageId' => $row['stageId'] === null ? null : (int) $row['stageId'],
                'stage' => (string) ($row['stage'] ?? ''),
                'code' => (string) ($row['code'] ?? ''),
                'project' => (string) ($row['project'] ?? ''),
                'type' => (string) ($row['type'] ?? 'Dpto.'),
                'area' => (float) ($row['area'] ?? 0),
                'price' => (float) ($row['price'] ?? 0),
                'status' => (string) ($row['status'] ?? 'Disponible'),
            ];
        }, $rows);
    }

    public function countUnidades(): int
    {
        $sql = 'SELECT COUNT(*) FROM unidades WHERE activo = 1';

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return (int) $statement->fetchColumn();
        } catch (PDOException $exception) {
            error_log('[InventarioModel] ' . $exception->getMessage());
            return 0;
        }
    }

    public function getStatuses(): array
    {
        $statement = $this->pdo->prepare('SELECT etiqueta FROM catalogo_valores WHERE categoria = :category AND activo = 1 ORDER BY orden');
        $statement->execute(['category' => 'estado_unidad']);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUnit(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT id, id_proyecto AS project_id, id_etapa AS stage_id, codigo AS code, tipo AS type, area_m2 AS area, precio AS price, estado AS status FROM unidades WHERE id = :id AND activo = 1');
        $statement->execute(['id' => $id]);
        $unit = $statement->fetch();
        return $unit === false ? null : $unit;
    }

    public function saveUnit(?int $id, array $data): int
    {
        $this->assertStatus($data['status']);
        $this->assertProjectAndStage($data['projectId'], $data['stageId']);
        $businessStatuses = ['Reservado', 'Separado', 'Vendido'];
        if (in_array($data['status'], $businessStatuses, true) && ($id === null || ($this->getUnit($id)['status'] ?? null) !== $data['status'])) {
            throw new InvalidArgumentException('Los estados reservado, separado y vendido se actualizan desde sus procesos comerciales.');
        }
        if ($id !== null) {
            $references = $this->pdo->prepare('SELECT (SELECT COUNT(*) FROM ventas WHERE id_unidad = :sales_id) + (SELECT COUNT(*) FROM separaciones WHERE id_unidad = :separation_id) + (SELECT COUNT(*) FROM cliente_propiedad WHERE id_unidad = :property_id)');
            $references->execute(['sales_id' => $id, 'separation_id' => $id, 'property_id' => $id]);
            if ((int) $references->fetchColumn() > 0) {
                $current = $this->getUnit($id);
                if ($current !== null && ((int) $current['project_id'] !== $data['projectId'] || (int) ($current['stage_id'] ?? 0) !== (int) ($data['stageId'] ?? 0))) {
                    throw new InvalidArgumentException('No se puede mover de proyecto o etapa una unidad con historial comercial.');
                }
            }
        }
        if ($id === null) {
            $sql = 'INSERT INTO unidades (id_proyecto, id_etapa, codigo, tipo, area_m2, precio, estado) VALUES (:project_id, :stage_id, :code, :type, :area, :price, :status)';
        } else {
            $sql = 'UPDATE unidades SET id_proyecto = :project_id, id_etapa = :stage_id, codigo = :code, tipo = :type, area_m2 = :area, precio = :price, estado = :status WHERE id = :id AND activo = 1';
        }
        $params = [
            'project_id' => $data['projectId'],
            'stage_id' => $data['stageId'],
            'code' => $data['code'],
            'type' => $data['type'],
            'area' => $data['area'],
            'price' => $data['price'],
            'status' => $data['status'],
        ];
        if ($id !== null) {
            $params['id'] = $id;
        }
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        if ($id !== null && $statement->rowCount() === 0 && $this->getUnit($id) === null) {
            throw new InvalidArgumentException('No se encontró la unidad activa solicitada.');
        }
        return $id ?? (int) $this->pdo->lastInsertId();
    }

    public function archiveUnit(int $id): void
    {
        $this->pdo->beginTransaction();
        try {
            $unit = $this->pdo->prepare('SELECT estado FROM unidades WHERE id = :id AND activo = 1 FOR UPDATE');
            $unit->execute(['id' => $id]);
            $status = $unit->fetchColumn();
            if ($status === false) {
                throw new InvalidArgumentException('No se encontró la unidad activa solicitada.');
            }
            if (in_array($status, ['Reservado', 'Separado', 'Vendido'], true)) {
                throw new InvalidArgumentException('No se puede archivar una unidad reservada, separada o vendida.');
            }
			$reference = $this->pdo->prepare('SELECT (SELECT COUNT(*) FROM ventas WHERE id_unidad = :sales_id) + (SELECT COUNT(*) FROM separaciones WHERE id_unidad = :separation_id) + (SELECT COUNT(*) FROM cliente_propiedad WHERE id_unidad = :property_id)');
			$reference->execute(['sales_id' => $id, 'separation_id' => $id, 'property_id' => $id]);
			if ((int) $reference->fetchColumn() > 0) {
				throw new InvalidArgumentException('La unidad tiene historial comercial y no se puede archivar.');
            }
            $update = $this->pdo->prepare('UPDATE unidades SET activo = 0 WHERE id = :id');
            $update->execute(['id' => $id]);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function assertStatus(string $status): void
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM catalogo_valores WHERE categoria = :category AND etiqueta = :status AND activo = 1 LIMIT 1');
        $statement->execute(['category' => 'estado_unidad', 'status' => $status]);
        if ($statement->fetchColumn() === false) {
            throw new InvalidArgumentException('Selecciona un estado activo del catálogo.');
        }
    }

    private function assertProjectAndStage(int $projectId, ?int $stageId): void
    {
        $project = $this->pdo->prepare("SELECT estado FROM proyectos WHERE id = :id");
        $project->execute(['id' => $projectId]);
        $projectStatus = $project->fetchColumn();
        if ($projectStatus === false || $projectStatus === 'Archivado') {
            throw new InvalidArgumentException('Selecciona un proyecto activo para comercialización.');
        }
        if ($stageId !== null) {
            $stage = $this->pdo->prepare('SELECT 1 FROM etapas_proyecto WHERE id = :stage_id AND id_proyecto = :project_id AND activo = 1');
            $stage->execute(['stage_id' => $stageId, 'project_id' => $projectId]);
            if ($stage->fetchColumn() === false) {
                throw new InvalidArgumentException('La etapa no pertenece al proyecto seleccionado o está inactiva.');
            }
        }
    }
}
