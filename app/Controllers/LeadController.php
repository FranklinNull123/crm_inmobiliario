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

		$phone = trim((string) ($input['phone'] ?? ''));
		$email = trim((string) ($input['email'] ?? ''));
		$projectId = $this->optionalPositiveId($input['project_id'] ?? null, 'proyecto');
		$interest = (string) ($input['interest'] ?? 'Bajo');
		if (!in_array($interest, ['Bajo', 'Medio', 'Alto', 'Urgente'], true)) {
			$interest = 'Bajo';
		}
		if (mb_strlen($phone, 'UTF-8') > 30 || mb_strlen($email, 'UTF-8') > 120) {
			throw new InvalidArgumentException('Los datos de contacto exceden la longitud permitida.');
		}
		if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('El correo electrónico no es válido.');
		}

		$campaignId = $this->optionalPositiveId($input['campaign_id'] ?? null, 'campaña');
		$advisorId = $forcedAdvisorId ?? $this->optionalPositiveId($input['advisor_id'] ?? null, 'asesor');

		return $this->leadModel->create($name, $phone, $email, $projectId, $interest, $campaignId, $advisorId);
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

	public function convertToClient(array $input, ?int $userId = null): int
	{
		$leadId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($leadId === false) {
			throw new InvalidArgumentException('El identificador del lead no es válido.');
		}

		$data = [
			'dni' => trim((string) ($input['dni'] ?? '')),
			'email' => trim((string) ($input['email'] ?? '')),
			'phone' => trim((string) ($input['phone'] ?? '')),
			'address' => trim((string) ($input['address'] ?? '')),
			'occupation' => trim((string) ($input['occupation'] ?? '')),
			'civil_status' => trim((string) ($input['civil_status'] ?? '')),
		];
		if ($data['dni'] === '' || mb_strlen($data['dni'], 'UTF-8') > 20) {
			throw new InvalidArgumentException('El DNI o documento es obligatorio y debe tener hasta 20 caracteres.');
		}
		if ($data['email'] !== '' && (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($data['email'], 'UTF-8') > 120)) {
			throw new InvalidArgumentException('El correo electrónico no es válido.');
		}
		if (mb_strlen($data['phone'], 'UTF-8') > 30 || mb_strlen($data['address'], 'UTF-8') > 200 || mb_strlen($data['occupation'], 'UTF-8') > 100 || mb_strlen($data['civil_status'], 'UTF-8') > 50) {
			throw new InvalidArgumentException('Uno de los datos del cliente excede la longitud permitida.');
		}

		return $this->leadModel->convertToClient($leadId, $data, $userId);
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