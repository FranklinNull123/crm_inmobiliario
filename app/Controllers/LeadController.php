<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/LeadModel.php';

final class LeadController
{
	private const STATUSES = ['Nuevo', 'Contactado', 'Visita Realizada', 'Negociación', 'Venta Cerrada'];

	public function __construct(private readonly \LeadModel $leadModel)
	{
	}

	public function index(): array
	{
		return $this->leadModel->getAll();
	}

	public function getCampaigns(): array
	{
		return $this->leadModel->getCampaigns();
	}

	public function getAdvisors(): array
	{
		return $this->leadModel->getAdvisors();
	}

	public function create(array $input, ?int $forcedAdvisorId = null): int
	{
		$name = trim((string) ($input['name'] ?? ''));
		$nameLength = preg_match_all('/./us', $name);

		if ($nameLength === false || $nameLength < 1 || $nameLength > 100) {
			throw new InvalidArgumentException('El nombre es obligatorio y debe tener hasta 100 caracteres.');
		}

		$campaignId = $this->optionalPositiveId($input['campaign_id'] ?? null, 'campaña');
		$advisorId = $forcedAdvisorId ?? $this->optionalPositiveId($input['advisor_id'] ?? null, 'asesor');

		return $this->leadModel->create($name, $campaignId, $advisorId);
	}

	public function changeStatus(mixed $id, mixed $status, ?int $userId = null): bool
	{
		$leadId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($leadId === false) {
			throw new InvalidArgumentException('El identificador del lead no es válido.');
		}

		if (!is_string($status) || !in_array($status, self::STATUSES, true)) {
			throw new InvalidArgumentException('El estado solicitado no es válido.');
		}

		return $this->leadModel->updateStatus($leadId, $status, $userId);
	}

	private function optionalPositiveId(mixed $value, string $field): ?int
	{
		if ($value === null || $value === '') {
			return null;
		}

		$id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($id === false) {
			throw new InvalidArgumentException('El valor de ' . $field . ' no es válido.');
		}

		return $id;
	}
}