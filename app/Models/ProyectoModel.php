<?php
declare(strict_types=1);

final class ProyectoModel
{
	public function __construct(private readonly PDO $pdo)
	{
	}

	public function getProjects(): array
	{
		$sql = <<<'SQL'
SELECT
	p.id,
	p.nombre AS name,
	p.ubicacion AS location,
	p.descripcion AS description,
	p.fecha_inicio AS startDate,
	p.fecha_entrega AS deliveryDate,
	p.estado AS status,
	(SELECT COUNT(*) FROM unidades u WHERE u.id_proyecto = p.id AND u.activo = 1) AS unitsCount,
	(SELECT COUNT(*) FROM etapas_proyecto e WHERE e.id_proyecto = p.id AND e.activo = 1) AS stagesCount
FROM proyectos p
ORDER BY p.id DESC
SQL;
		return $this->pdo->query($sql)->fetchAll();
	}

	public function getStages(): array
	{
		$sql = <<<'SQL'
SELECT e.id, e.id_proyecto AS projectId, p.nombre AS project, e.nombre AS name,
	e.orden AS position, e.activo AS active
FROM etapas_proyecto e
JOIN proyectos p ON p.id = e.id_proyecto
ORDER BY p.nombre, e.orden, e.id
SQL;
		return $this->pdo->query($sql)->fetchAll();
	}

	public function getStatuses(string $category): array
	{
		$statement = $this->pdo->prepare('SELECT codigo AS code, etiqueta AS label FROM catalogo_valores WHERE categoria = :category AND activo = 1 ORDER BY orden, etiqueta');
		$statement->execute(['category' => $category]);
		return $statement->fetchAll();
	}

	public function getProject(int $id): ?array
	{
		$statement = $this->pdo->prepare('SELECT id, nombre AS name, ubicacion AS location, descripcion AS description, fecha_inicio AS startDate, fecha_entrega AS deliveryDate, estado AS status FROM proyectos WHERE id = :id');
		$statement->execute(['id' => $id]);
		$row = $statement->fetch();
		return $row === false ? null : $row;
	}

	public function saveProject(?int $id, array $data): int
	{
		$this->assertCatalogValue('estado_proyecto', $data['status']);
		if ($id === null) {
			$statement = $this->pdo->prepare('INSERT INTO proyectos (nombre, ubicacion, descripcion, fecha_inicio, fecha_entrega, estado) VALUES (:name, :location, :description, :start_date, :delivery_date, :status)');
		} else {
			$statement = $this->pdo->prepare('UPDATE proyectos SET nombre = :name, ubicacion = :location, descripcion = :description, fecha_inicio = :start_date, fecha_entrega = :delivery_date, estado = :status WHERE id = :id');
		}
		$params = [
			'name' => $data['name'],
			'location' => $data['location'],
			'description' => $data['description'],
			'start_date' => $data['startDate'],
			'delivery_date' => $data['deliveryDate'],
			'status' => $data['status'],
		];
		if ($id !== null) {
			$params['id'] = $id;
		}
		$statement->execute($params);
		if ($id !== null && $statement->rowCount() === 0 && $this->getProject($id) === null) {
			throw new InvalidArgumentException('No se encontró el proyecto solicitado.');
		}
		return $id ?? (int) $this->pdo->lastInsertId();
	}

	public function saveStage(?int $id, array $data): int
	{
		$project = $this->pdo->prepare('SELECT estado FROM proyectos WHERE id = :id');
		$project->execute(['id' => $data['projectId']]);
		if ($project->fetchColumn() === false) {
			throw new InvalidArgumentException('Selecciona un proyecto válido.');
		}
		if ($id !== null) {
			$current = $this->pdo->prepare('SELECT id_proyecto FROM etapas_proyecto WHERE id = :id');
			$current->execute(['id' => $id]);
			$currentProjectId = $current->fetchColumn();
			if ($currentProjectId === false) {
				throw new InvalidArgumentException('No se encontró la etapa solicitada.');
			}
			if ((int) $currentProjectId !== $data['projectId']) {
				$used = $this->pdo->prepare('SELECT COUNT(*) FROM unidades WHERE id_etapa = :id');
				$used->execute(['id' => $id]);
				if ((int) $used->fetchColumn() > 0) {
					throw new InvalidArgumentException('No se puede mover una etapa que ya tiene unidades vinculadas a otro proyecto.');
				}
			}
		}
		if ($id === null) {
			$statement = $this->pdo->prepare('INSERT INTO etapas_proyecto (id_proyecto, nombre, orden) VALUES (:project_id, :name, :position)');
		} else {
			$statement = $this->pdo->prepare('UPDATE etapas_proyecto SET id_proyecto = :project_id, nombre = :name, orden = :position WHERE id = :id');
		}
		$params = ['project_id' => $data['projectId'], 'name' => $data['name'], 'position' => $data['position']];
		if ($id !== null) {
			$params['id'] = $id;
		}
		$statement->execute($params);
		if ($id !== null && $statement->rowCount() === 0) {
			$check = $this->pdo->prepare('SELECT 1 FROM etapas_proyecto WHERE id = :id');
			$check->execute(['id' => $id]);
			if ($check->fetchColumn() === false) {
				throw new InvalidArgumentException('No se encontró la etapa solicitada.');
			}
		}
		return $id ?? (int) $this->pdo->lastInsertId();
	}

	public function setStageActive(int $id, bool $active): void
	{
		if (!$active) {
			$used = $this->pdo->prepare('SELECT COUNT(*) FROM unidades WHERE id_etapa = :id AND activo = 1');
			$used->execute(['id' => $id]);
			if ((int) $used->fetchColumn() > 0) {
				throw new InvalidArgumentException('No se puede desactivar una etapa con unidades activas asignadas.');
			}
		}
		$statement = $this->pdo->prepare('UPDATE etapas_proyecto SET activo = :active WHERE id = :id');
		$statement->execute(['active' => $active ? 1 : 0, 'id' => $id]);
		if ($statement->rowCount() === 0) {
			$check = $this->pdo->prepare('SELECT 1 FROM etapas_proyecto WHERE id = :id');
			$check->execute(['id' => $id]);
			if ($check->fetchColumn() === false) {
				throw new InvalidArgumentException('No se encontró la etapa solicitada.');
			}
		}
	}

	private function assertCatalogValue(string $category, string $value): void
	{
		$statement = $this->pdo->prepare('SELECT 1 FROM catalogo_valores WHERE categoria = :category AND etiqueta = :value AND activo = 1 LIMIT 1');
		$statement->execute(['category' => $category, 'value' => $value]);
		if ($statement->fetchColumn() === false) {
			throw new InvalidArgumentException('Selecciona un estado activo del catálogo.');
		}
	}
}