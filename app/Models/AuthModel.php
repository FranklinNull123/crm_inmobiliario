<?php
declare(strict_types=1);

final class AuthModel
{
	public function __construct(private readonly PDO $pdo)
	{
	}

	public function bootstrapRequired(): bool
	{
		try {
			$statement = $this->pdo->query("SELECT COUNT(*) FROM usuario_roles ur JOIN roles r ON r.id = ur.id_rol WHERE r.nombre = 'superadmin'");
			return (int) $statement->fetchColumn() === 0;
		} catch (PDOException $exception) {
			if (str_contains($exception->getMessage(), 'Base table or view not found') || str_contains($exception->getMessage(), '42S02')) {
				return true;
			}
			throw $exception;
		}
	}

	public function createInitialAdmin(string $name, string $email, string $password): void
	{
		$lockAcquired = (int) $this->pdo->query("SELECT GET_LOCK('cms_initial_superadmin_setup', 10)")->fetchColumn() === 1;
		if (!$lockAcquired) {
			throw new RuntimeException('No se pudo bloquear la configuración inicial. Inténtalo de nuevo.');
		}
		try {
			if (!$this->bootstrapRequired()) {
				throw new RuntimeException('La configuración inicial ya fue completada.');
			}
			$this->pdo->beginTransaction();
			$role = $this->pdo->query("SELECT id FROM roles WHERE nombre = 'superadmin' AND activo = 1 FOR UPDATE")->fetchColumn();
			if ($role === false) {
				throw new RuntimeException('Falta ejecutar la migración de roles.');
			}
			$statement = $this->pdo->prepare('INSERT INTO usuarios (nombres, email, password_hash, estado) VALUES (:name, :email, :password_hash, 1)');
			$statement->execute([
				'name' => $name,
				'email' => $email,
				'password_hash' => password_hash($password, PASSWORD_DEFAULT),
			]);
			$userId = (int) $this->pdo->lastInsertId();
			$assignment = $this->pdo->prepare('INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (:user_id, :role_id)');
			$assignment->execute(['user_id' => $userId, 'role_id' => (int) $role]);
			$this->audit(null, 'superadmin_inicial_creado', 'usuarios', (string) $userId, null, ['email' => $email]);
			$this->pdo->commit();
		} catch (Throwable $exception) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			throw $exception;
		} finally {
			$this->pdo->query("SELECT RELEASE_LOCK('cms_initial_superadmin_setup')");
		}
	}

	public function authenticate(string $email, string $password): ?array
	{
		$statement = $this->pdo->prepare('SELECT id_usuario, nombres, email, avatar_url, password_hash, estado, failed_login_attempts, locked_until FROM usuarios WHERE email = :email LIMIT 1');
		$statement->execute(['email' => $email]);
		$user = $statement->fetch();
		$device = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
		$ip = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);

		if (!$user || (int) $user['estado'] !== 1 || empty($user['password_hash'])) {
			$this->recordAttempt($email, $ip, $device, false);
			$this->audit(null, 'login_fallido', 'usuarios', null, null, ['email' => $email]);
			return null;
		}

		if ($user['locked_until'] !== null && strtotime((string) $user['locked_until']) > time()) {
			$this->recordAttempt($email, $ip, $device, false);
			return null;
		}

		if (!password_verify($password, (string) $user['password_hash'])) {
			$attempts = (int) $user['failed_login_attempts'] + 1;
			$lockUntil = $attempts >= 5 ? date('Y-m-d H:i:s', time() + 900) : null;
			$update = $this->pdo->prepare('UPDATE usuarios SET failed_login_attempts = :attempts, locked_until = :locked_until WHERE id_usuario = :id');
			$update->execute(['attempts' => $attempts, 'locked_until' => $lockUntil, 'id' => (int) $user['id_usuario']]);
			$this->recordAttempt($email, $ip, $device, false);
			$this->audit((int) $user['id_usuario'], 'login_fallido', 'usuarios', (string) $user['id_usuario'], null, ['intentos' => $attempts]);
			return null;
		}

		$roles = $this->pdo->prepare('SELECT r.nombre FROM usuario_roles ur JOIN roles r ON r.id = ur.id_rol WHERE ur.id_usuario = :id AND r.activo = 1');
		$roles->execute(['id' => (int) $user['id_usuario']]);
		$permissions = $this->pdo->prepare("SELECT DISTINCT CONCAT(p.modulo, '.', p.accion) FROM usuario_roles ur JOIN roles r ON r.id = ur.id_rol JOIN rol_permisos rp ON rp.id_rol = r.id JOIN permisos p ON p.id = rp.id_permiso WHERE ur.id_usuario = :id AND r.activo = 1 AND rp.permitido = 1");
		$permissions->execute(['id' => (int) $user['id_usuario']]);
		$update = $this->pdo->prepare('UPDATE usuarios SET failed_login_attempts = 0, locked_until = NULL, last_login_at = NOW() WHERE id_usuario = :id');
		$update->execute(['id' => (int) $user['id_usuario']]);
		$this->recordAttempt($email, $ip, $device, true);
		$this->audit((int) $user['id_usuario'], 'login_correcto', 'usuarios', (string) $user['id_usuario'], null, null);

		return [
			'id' => (int) $user['id_usuario'],
			'name' => (string) $user['nombres'],
			'email' => (string) $user['email'],
			'avatar' => (string) ($user['avatar_url'] ?? ''),
			'roles' => $roles->fetchAll(PDO::FETCH_COLUMN),
			'permissions' => $permissions->fetchAll(PDO::FETCH_COLUMN),
		];
	}

	private function recordAttempt(string $email, string $ip, string $device, bool $success): void
	{
		$statement = $this->pdo->prepare('INSERT INTO intentos_acceso (email, ip, dispositivo, correcto) VALUES (:email, :ip, :device, :success)');
		$statement->execute(['email' => $email, 'ip' => $ip, 'device' => $device, 'success' => $success ? 1 : 0]);
	}

	private function audit(?int $userId, string $event, string $entity, ?string $entityId, ?array $old, ?array $new): void
	{
		$statement = $this->pdo->prepare('INSERT INTO auditoria (id_usuario, evento, entidad, entidad_id, datos_anteriores, datos_nuevos, ip, dispositivo) VALUES (:user_id, :event, :entity, :entity_id, :old_data, :new_data, :ip, :device)');
		$statement->execute([
			'user_id' => $userId,
			'event' => $event,
			'entity' => $entity,
			'entity_id' => $entityId,
			'old_data' => $old === null ? null : json_encode($old, JSON_THROW_ON_ERROR),
			'new_data' => $new === null ? null : json_encode($new, JSON_THROW_ON_ERROR),
			'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
			'device' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
		]);
	}
}