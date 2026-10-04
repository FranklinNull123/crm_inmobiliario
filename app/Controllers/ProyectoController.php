<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ProyectoModel.php';

final class ProyectoController
{
	public function __construct(private readonly ProyectoModel $model)
	{
	}

	public function index(): array
	{
		return [
			'projects' => $this->model->getProjects(),
			'stages' => $this->model->getStages(),
			'statuses' => $this->model->getStatuses('estado_proyecto'),
		];
	}

	public function saveProject(array $input): int
	{
		$id = $this->optionalId($input['id'] ?? null);
		$name = trim((string) ($input['name'] ?? ''));
		$location = trim((string) ($input['location'] ?? ''));
		$description = trim((string) ($input['description'] ?? ''));
		$status = trim((string) ($input['status'] ?? ''));
		$startDate = $this->optionalDate($input['start_date'] ?? null);
		$deliveryDate = $this->optionalDate($input['delivery_date'] ?? null);
		if (mb_strlen($name) < 2 || mb_strlen($name) > 120 || mb_strlen($location) > 200 || mb_strlen($description) > 4000 || $status === '') {
			throw new InvalidArgumentException('Completa un nombre de proyecto válido y revisa sus campos.');
		}
		if ($startDate !== null && $deliveryDate !== null && $deliveryDate < $startDate) {
			throw new InvalidArgumentException('La fecha de entrega debe ser posterior al inicio.');
		}
		return $this->model->saveProject($id, compact('name', 'location', 'description', 'status') + ['startDate' => $startDate, 'deliveryDate' => $deliveryDate]);
	}

	public function saveStage(array $input): int
	{
		$id = $this->optionalId($input['id'] ?? null);
		$projectId = filter_var($input['project_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		$position = filter_var($input['position'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 999]]);
		$name = trim((string) ($input['name'] ?? ''));
		if ($projectId === false || $position === false || mb_strlen($name) < 2 || mb_strlen($name) > 100) {
			throw new InvalidArgumentException('Indica un proyecto, nombre de etapa y orden válidos.');
		}
		return $this->model->saveStage($id, ['projectId' => $projectId, 'name' => $name, 'position' => $position]);
	}

	public function setStageActive(mixed $id, mixed $active): void
	{
		$stageId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		$isActive = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if ($stageId === false || $isActive === null) {
			throw new InvalidArgumentException('La etapa solicitada no es válida.');
		}
		$this->model->setStageActive($stageId, $isActive);
	}

	private function optionalId(mixed $value): ?int
	{
		if ($value === null || $value === '') {
			return null;
		}
		$id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($id === false) {
			throw new InvalidArgumentException('El identificador no es válido.');
		}
		return $id;
	}

	private function optionalDate(mixed $value): ?string
	{
		if ($value === null || $value === '') {
			return null;
		}
		$date = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);
		$errors = DateTimeImmutable::getLastErrors();
		if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d') !== $value) {
			throw new InvalidArgumentException('Una fecha del proyecto no es válida.');
		}
		return $date->format('Y-m-d');
	}
}