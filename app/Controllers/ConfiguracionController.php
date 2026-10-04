<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ConfiguracionModel.php';

final class ConfiguracionController
{
	public function __construct(private readonly ConfiguracionModel $model)
	{
	}

	public function index(bool $includeAudit = false): array
	{
		return $this->model->getData($includeAudit);
	}

	public function auditLogs(): array
	{
		return $this->model->getAuditLogs(200);
	}

	public function updateSettings(array $data, int $userId): void
	{
		$this->model->updateSettings($data, $userId);
	}

	public function updateCatalog(mixed $id, mixed $active): bool
	{
		$catalogId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($catalogId === false || !in_array($active, [true, false, 0, 1, '0', '1'], true)) {
			throw new InvalidArgumentException('El valor del catálogo no es válido.');
		}
		return $this->model->setCatalogStatus($catalogId, (bool) $active);
	}

	public function createCatalog(array $data): int
	{
		return $this->model->createCatalogValue(
			strtolower(trim((string) ($data['category'] ?? ''))),
			strtolower(trim((string) ($data['code'] ?? ''))),
			trim((string) ($data['label'] ?? ''))
		);
	}

	public function updatePermission(array $data): void
	{
		$roleId = filter_var($data['role_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		$permissionId = filter_var($data['permission_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		$allowed = filter_var($data['allowed'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if ($roleId === false || $permissionId === false || $allowed === null) {
			throw new InvalidArgumentException('La asignación de permiso no es válida.');
		}
		$this->model->setRolePermission($roleId, $permissionId, $allowed);
	}

	public function createUser(array $data): int
	{
		$roleId = filter_var($data['role_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($roleId === false) {
			throw new InvalidArgumentException('Selecciona un rol válido.');
		}
		return $this->model->createUser(trim((string) ($data['name'] ?? '')), trim((string) ($data['email'] ?? '')), (string) ($data['password'] ?? ''), $roleId);
	}

	public function toggleUserStatus(int $userId, bool $active): bool
	{
		return $this->model->setUserStatus($userId, $active);
	}

	public function updateProfile(array $data, int $userId): array
	{
		return $this->model->updateProfile(
			$userId,
			trim((string) ($data['name'] ?? '')),
			trim((string) ($data['email'] ?? '')),
			array_key_exists('password', $data) ? (string) $data['password'] : null,
			isset($data['avatar']) && is_string($data['avatar']) ? $data['avatar'] : null
		);
	}
}