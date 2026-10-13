<?php
declare(strict_types=1);

final class LeadModel
{
	public function __construct(private readonly PDO $pdo)
	{
	}

	public function getAll(): array
	{
		$sql = <<<'SQL'
SELECT
	cl.id_cliente AS id,
	cl.nombres AS name,
	COALESCE(NULLIF(TRIM(cl.estado_lead), ''), 'Sin estado') AS status,
	ca.nombre AS campaign,
	ch.nombre AS channel,
	a.nombre AS advisor
FROM clientes_leads AS cl
LEFT JOIN campanas AS ca ON ca.id_campana = cl.id_campana
LEFT JOIN canales AS ch ON ch.id_canal = ca.id_canal
LEFT JOIN asesores AS a ON a.id = cl.id_asesor
ORDER BY cl.id_cliente DESC
SQL;

		return $this->pdo->query($sql)->fetchAll();
	}

	public function getLeads(?int $userId = null): array
	{
		$sql = <<<'SQL'
SELECT
	l.id,
	l.nombre AS name,
	COALESCE(l.telefono, '') AS phone,
	COALESCE(l.email, '') AS email,
	COALESCE(NULLIF(TRIM(l.etapa), ''), 'Nuevo Lead') AS status,
	COALESCE(NULLIF(TRIM(l.etapa), ''), 'nuevo') AS stage,
	l.id_cliente_convertido AS client_id,
	COALESCE(NULLIF(TRIM(p.nombre), ''), NULLIF(TRIM(l.proyecto_interes), ''), '') AS project,
	l.id_proyecto AS project_id,
	COALESCE(NULLIF(TRIM(l.nivel_interes), ''), 'Bajo') AS level,
	'' AS next,
	CASE
		WHEN LOWER(COALESCE(NULLIF(TRIM(l.nivel_interes), ''), 'Bajo')) IN ('alto', 'urgente') THEN 1
		ELSE 0
	END AS urgent,
	COALESCE(ca.nombre, '') AS campaign,
	COALESCE(ch.nombre, '') AS channel,
	COALESCE(u.nombres, '') AS advisor
FROM leads AS l
LEFT JOIN proyectos AS p ON p.id = l.id_proyecto
LEFT JOIN campanas AS ca ON ca.id_campana = l.id_campana
LEFT JOIN canales AS ch ON ch.id_canal = ca.id_canal
LEFT JOIN usuarios AS u ON u.id_usuario = l.id_usuario_asignado
WHERE l.id_cliente_convertido IS NULL
ORDER BY l.id DESC
SQL;

		try {
			if ($userId !== null) {
				$sql = str_replace('ORDER BY l.id DESC', 'AND l.id_usuario_asignado = :user_id ORDER BY l.id DESC', $sql);
			}
			$statement = $this->pdo->prepare($sql);
			$statement->execute($userId === null ? [] : ['user_id' => $userId]);
			$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $exception) {
			error_log('[LeadModel] ' . $exception->getMessage());
			return [];
		}

		return array_map(static function (array $row): array {
			$status = (string) ($row['status'] ?? 'Nuevo Lead');
			$stage = strtolower((string) ($row['stage'] ?? 'nuevo'));
			return [
				'id' => (int) ($row['id'] ?? 0),
				'client_id' => $row['client_id'] === null ? null : (int) $row['client_id'],
				'status' => $status,
				'stage' => $stage,
				'name' => (string) ($row['name'] ?? ''),
				'phone' => (string) ($row['phone'] ?? ''),
				'email' => (string) ($row['email'] ?? ''),
				'project' => (string) ($row['project'] ?? ''),
				'project_id' => $row['project_id'] === null ? null : (int) $row['project_id'],
				'level' => (string) ($row['level'] ?? 'Bajo'),
				'channel' => (string) ($row['channel'] ?? 'whatsapp'),
				'next' => (string) ($row['next'] ?? ''),
				'urgent' => (bool) ($row['urgent'] ?? false),
				'campaign' => (string) ($row['campaign'] ?? ''),
				'advisor' => (string) ($row['advisor'] ?? ''),
			];
		}, $rows);
	}

	public function getCampaigns(): array
	{
		$sql = 'SELECT id_campana AS id, nombre FROM campanas ORDER BY nombre';

		return $this->pdo->query($sql)->fetchAll();
	}

	public function getAdvisors(): array
	{
		$sql = 'SELECT u.id_usuario AS id, u.nombres AS name
			FROM asesores a
			JOIN usuarios u ON u.id_usuario = a.id_usuario
			WHERE a.estado = 1 AND u.estado = 1
			ORDER BY u.nombres';

		return $this->pdo->query($sql)->fetchAll();
	}

	public function create(string $name, string $phone, string $email, ?int $projectId, string $interest, ?int $campaignId, ?int $advisorId): int
	{
		if ($projectId !== null) {
			$project = $this->pdo->prepare('SELECT 1 FROM proyectos WHERE id = :id AND estado <> :archived LIMIT 1');
			$project->execute(['id' => $projectId, 'archived' => 'Archivado']);
			if ($project->fetchColumn() === false) {
				throw new InvalidArgumentException('Selecciona un proyecto activo válido.');
			}
		}

		$sql = <<<'SQL'
		INSERT INTO leads (id_campana, id_usuario_asignado, nombre, telefono, email, id_proyecto, nivel_interes, etapa)
		VALUES (:campaign_id, :advisor_id, :name, :phone, :email, :project_id, :interest, 'Nuevo Lead')
SQL;
		$statement = $this->pdo->prepare($sql);
		$statement->bindValue(':advisor_id', $advisorId, $advisorId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$statement->bindValue(':campaign_id', $campaignId, $campaignId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$statement->bindValue(':name', $name, PDO::PARAM_STR);
		$statement->bindValue(':phone', $phone, PDO::PARAM_STR);
		$statement->bindValue(':email', $email, PDO::PARAM_STR);
		$statement->bindValue(':project_id', $projectId, $projectId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$statement->bindValue(':interest', $interest, PDO::PARAM_STR);
		$statement->execute();

		return (int) $this->pdo->lastInsertId();
	}

	public function updateStatus(int $id, string $status, ?int $userId = null): bool
	{
		$sql = 'UPDATE leads SET etapa = :status WHERE id = :id AND id_cliente_convertido IS NULL';
		$params = ['status' => $status, 'id' => $id];
		if ($userId !== null) {
			$sql .= ' AND id_usuario_asignado = :user_id';
			$params['user_id'] = $userId;
		}
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		if ($statement->rowCount() > 0) {
			return true;
		}

		$checkSql = 'SELECT 1 FROM leads WHERE id = :id AND id_cliente_convertido IS NULL';
		$checkParams = ['id' => $id];
		if ($userId !== null) {
			$checkSql .= ' AND id_usuario_asignado = :user_id';
			$checkParams['user_id'] = $userId;
		}
		$check = $this->pdo->prepare($checkSql);
		$check->execute($checkParams);

		return $check->fetchColumn() !== false;
	}

	public function updateStage(int $id, string $stage, ?int $userId = null): bool
	{
		$map = [
			'nuevo' => 'Nuevo Lead',
			'contactado' => 'Contactado',
			'visita' => 'Visita Realizada',
			'negociacion' => 'Negociación',
			'cerrada' => 'Venta Cerrada',
		];

		$normalized = strtolower(trim($stage));
		$status = $map[$normalized] ?? $map['nuevo'];
		$sql = 'UPDATE leads SET etapa = :status WHERE id = :id AND id_cliente_convertido IS NULL';
		$params = ['status' => $status, 'id' => $id];
		if ($userId !== null) {
			$sql .= ' AND id_usuario_asignado = :user_id';
			$params['user_id'] = $userId;
		}
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement->rowCount() > 0;
	}

	public function convertToClient(int $leadId, array $clientData, ?int $userId = null): int
	{
		$this->pdo->beginTransaction();
		try {
			$sql = 'SELECT id, nombre, id_usuario_asignado, id_cliente_convertido FROM leads WHERE id = :id';
			$params = ['id' => $leadId];
			if ($userId !== null) {
				$sql .= ' AND id_usuario_asignado = :user_id';
				$params['user_id'] = $userId;
			}
			$sql .= ' FOR UPDATE';
			$leadStatement = $this->pdo->prepare($sql);
			$leadStatement->execute($params);
			$lead = $leadStatement->fetch(PDO::FETCH_ASSOC);

			if ($lead === false) {
				throw new InvalidArgumentException('No se encontró ese lead o no tienes acceso.');
			}
			if ($lead['id_cliente_convertido'] !== null) {
				throw new InvalidArgumentException('Este lead ya fue convertido en cliente.');
			}

			$existingClient = $this->pdo->prepare('SELECT id FROM clientes WHERE dni = :dni LIMIT 1');
			$existingClient->execute(['dni' => $clientData['dni']]);
			if ($existingClient->fetchColumn() !== false) {
				throw new InvalidArgumentException('Ya existe un cliente registrado con ese DNI.');
			}

			$advisorId = null;
			if ($lead['id_usuario_asignado'] !== null) {
				$advisor = $this->pdo->prepare('SELECT id FROM asesores WHERE id_usuario = :user_id LIMIT 1');
				$advisor->execute(['user_id' => (int) $lead['id_usuario_asignado']]);
				$advisorId = $advisor->fetchColumn();
				if ($advisorId === false) {
					$advisorId = null;
				}
			}

			$insertClient = $this->pdo->prepare(
				'INSERT INTO clientes (id_asesor, nombre, dni, email, telefono, direccion, ocupacion, estado_civil, estado, fecha_registro) '
				. 'VALUES (:advisor_id, :name, :dni, :email, :phone, :address, :occupation, :civil_status, :status, CURDATE())'
			);
			$insertClient->execute([
				'advisor_id' => $advisorId,
				'name' => $lead['nombre'],
				'dni' => $clientData['dni'],
				'email' => $clientData['email'],
				'phone' => $clientData['phone'],
				'address' => $clientData['address'],
				'occupation' => $clientData['occupation'],
				'civil_status' => $clientData['civil_status'],
				'status' => 'Cliente Activo',
			]);
			$clientId = (int) $this->pdo->lastInsertId();

			if ($clientData['phone'] !== '') {
				$insertContact = $this->pdo->prepare(
					'INSERT INTO contactos (id_cliente, tipo_contacto, telefono, es_principal) VALUES (:client_id, :type, :phone, 1)'
				);
				$insertContact->execute(['client_id' => $clientId, 'type' => 'Teléfono', 'phone' => $clientData['phone']]);
			}

			$updateLead = $this->pdo->prepare('UPDATE leads SET etapa = :status, id_cliente_convertido = :client_id WHERE id = :id');
			$updateLead->execute(['status' => 'Cliente', 'client_id' => $clientId, 'id' => $leadId]);

			$this->pdo->commit();
			return $clientId;
		} catch (Throwable $exception) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			throw $exception;
		}
	}
}