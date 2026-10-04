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
	u.nombres AS advisor
FROM clientes_leads AS cl
LEFT JOIN campanas AS ca ON ca.id_campana = cl.id_campana
LEFT JOIN canales AS ch ON ch.id_canal = ca.id_canal
LEFT JOIN usuarios AS u ON u.id_usuario = cl.id_asesor
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
	COALESCE(NULLIF(TRIM(l.etapa), ''), 'Nuevo Lead') AS status,
	COALESCE(NULLIF(TRIM(l.etapa), ''), 'nuevo') AS stage,
	COALESCE(NULLIF(TRIM(l.proyecto_interes), ''), '') AS project,
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
LEFT JOIN campanas AS ca ON ca.id_campana = l.id_campana
LEFT JOIN canales AS ch ON ch.id_canal = ca.id_canal
LEFT JOIN usuarios AS u ON u.id_usuario = l.id_usuario_asignado
ORDER BY l.id DESC
SQL;

		try {
			if ($userId !== null) {
				$sql = str_replace('ORDER BY l.id DESC', 'WHERE l.id_usuario_asignado = :user_id ORDER BY l.id DESC', $sql);
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
				'status' => $status,
				'stage' => $stage,
				'name' => (string) ($row['name'] ?? ''),
				'project' => (string) ($row['project'] ?? ''),
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
		$sql = "SELECT u.id_usuario AS id, u.nombres AS name FROM usuarios u JOIN usuario_roles ur ON ur.id_usuario = u.id_usuario JOIN roles r ON r.id = ur.id_rol WHERE u.estado = 1 AND r.activo = 1 AND r.nombre = 'asesor' ORDER BY u.nombres";

		return $this->pdo->query($sql)->fetchAll();
	}

	public function create(string $name, ?int $campaignId, ?int $advisorId): int
	{
		$sql = <<<'SQL'
		INSERT INTO leads (id_campana, id_usuario_asignado, nombre, nivel_interes, etapa)
		VALUES (:campaign_id, :advisor_id, :name, 'Bajo', 'Nuevo Lead')
SQL;
		$statement = $this->pdo->prepare($sql);
		$statement->bindValue(':advisor_id', $advisorId, $advisorId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$statement->bindValue(':campaign_id', $campaignId, $campaignId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$statement->bindValue(':name', $name, PDO::PARAM_STR);
		$statement->execute();

		return (int) $this->pdo->lastInsertId();
	}

	public function updateStatus(int $id, string $status, ?int $userId = null): bool
	{
		$sql = 'UPDATE leads SET etapa = :status WHERE id = :id';
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

		$checkSql = 'SELECT 1 FROM leads WHERE id = :id';
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
		$sql = 'UPDATE leads SET etapa = :status WHERE id = :id';
		$params = ['status' => $status, 'id' => $id];
		if ($userId !== null) {
			$sql .= ' AND id_usuario_asignado = :user_id';
			$params['user_id'] = $userId;
		}
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement->rowCount() > 0;
	}
}