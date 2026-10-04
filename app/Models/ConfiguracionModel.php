<?php
declare(strict_types=1);

final class ConfiguracionModel
{
	public function __construct(private readonly PDO $pdo)
	{
	}

	public function getData(bool $includeAudit = false): array
	{
		$settings = $this->pdo->query('SELECT clave, valor FROM configuracion_sistema ORDER BY clave')->fetchAll(PDO::FETCH_KEY_PAIR);
		$catalogs = $this->pdo->query('SELECT id, categoria, codigo, etiqueta, activo, orden FROM catalogo_valores ORDER BY categoria, orden, etiqueta')->fetchAll();
		$roles = $this->pdo->query('SELECT id, nombre, etiqueta FROM roles WHERE activo = 1 ORDER BY id')->fetchAll();
		$permissions = $this->pdo->query('SELECT id, modulo, accion, etiqueta FROM permisos ORDER BY modulo, FIELD(accion, "ver", "crear", "editar", "eliminar", "aprobar", "exportar", "imprimir", "descargar")')->fetchAll();
		$assignments = $this->pdo->query('SELECT id_rol, id_permiso, permitido FROM rol_permisos')->fetchAll();
		$users = $this->pdo->query('SELECT u.id_usuario AS id, u.nombres AS nombre, u.email, u.avatar_url, u.estado, GROUP_CONCAT(r.etiqueta ORDER BY r.id SEPARATOR ", ") AS roles FROM usuarios u LEFT JOIN usuario_roles ur ON ur.id_usuario = u.id_usuario LEFT JOIN roles r ON r.id = ur.id_rol GROUP BY u.id_usuario ORDER BY u.nombres')->fetchAll();
		$auditLogs = $includeAudit ? $this->getAuditLogs(100) : [];

		return compact('settings', 'catalogs', 'roles', 'permissions', 'assignments', 'users', 'auditLogs');
	}

	public function getAuditLogs(int $limit = 100): array
	{
		$limit = max(1, min($limit, 200));
		return $this->pdo->query('SELECT a.evento, a.entidad, a.entidad_id, a.ip, a.dispositivo, a.creado_en, u.nombres AS usuario FROM auditoria a LEFT JOIN usuarios u ON u.id_usuario = a.id_usuario ORDER BY a.id DESC LIMIT ' . $limit)->fetchAll();
	}

	public function updateSettings(array $settings, int $userId): void
	{
		$allowed = ['empresa_nombre', 'moneda_principal', 'zona_horaria'];
		$statement = $this->pdo->prepare('INSERT INTO configuracion_sistema (clave, valor, actualizado_por) VALUES (:key, :value, :user_id) ON DUPLICATE KEY UPDATE valor = VALUES(valor), actualizado_por = VALUES(actualizado_por)');
		foreach ($allowed as $key) {
			if (!array_key_exists($key, $settings)) {
				continue;
			}
			$value = trim((string) $settings[$key]);
			if ($value === '' || mb_strlen($value) > 120) {
				throw new InvalidArgumentException('Completa valores válidos de configuración (máximo 120 caracteres).');
			}
			$statement->execute(['key' => $key, 'value' => $value, 'user_id' => $userId]);
		}
	}

	public function setCatalogStatus(int $id, bool $active): bool
	{
		$statement = $this->pdo->prepare('UPDATE catalogo_valores SET activo = :active WHERE id = :id');
		$statement->execute(['active' => $active ? 1 : 0, 'id' => $id]);
		return $statement->rowCount() > 0;
	}

	public function createCatalogValue(string $category, string $code, string $label): int
	{
		if (!preg_match('/^[a-z][a-z0-9_]{1,59}$/', $category) || !preg_match('/^[a-z0-9][a-z0-9_-]{0,59}$/', $code) || mb_strlen($label) < 1 || mb_strlen($label) > 120) {
			throw new InvalidArgumentException('Categoría, código o etiqueta de catálogo no válidos.');
		}
		$statement = $this->pdo->prepare('INSERT INTO catalogo_valores (categoria, codigo, etiqueta) VALUES (:category, :code, :label)');
		$statement->execute(['category' => $category, 'code' => $code, 'label' => $label]);
		return (int) $this->pdo->lastInsertId();
	}

	public function setRolePermission(int $roleId, int $permissionId, bool $allowed): void
	{
		$role = $this->pdo->prepare('SELECT nombre FROM roles WHERE id = :id');
		$role->execute(['id' => $roleId]);
		if ($role->fetchColumn() === 'superadmin') {
			throw new InvalidArgumentException('Los permisos del superadministrador no se modifican desde esta matriz.');
		}
		$statement = $this->pdo->prepare('INSERT INTO rol_permisos (id_rol, id_permiso, permitido) VALUES (:role_id, :permission_id, :allowed) ON DUPLICATE KEY UPDATE permitido = VALUES(permitido)');
		$statement->execute(['role_id' => $roleId, 'permission_id' => $permissionId, 'allowed' => $allowed ? 1 : 0]);
	}

	public function createUser(string $name, string $email, string $password, int $roleId): int
	{
		if (mb_strlen($name) < 2 || mb_strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
			throw new InvalidArgumentException('Usa un nombre válido, un email y una contraseña de al menos 12 caracteres.');
		}
		$roleCheck = $this->pdo->prepare('SELECT nombre FROM roles WHERE id = :id AND activo = 1');
		$roleCheck->execute(['id' => $roleId]);
		$roleName = $roleCheck->fetchColumn();
		if ($roleName === false || $roleName === 'superadmin') {
			throw new InvalidArgumentException('Selecciona un rol activo que no sea superadministrador.');
		}
		$this->pdo->beginTransaction();
		try {
			$insert = $this->pdo->prepare('INSERT INTO usuarios (nombres, email, password_hash, estado) VALUES (:name, :email, :hash, 1)');
			$insert->execute(['name' => $name, 'email' => strtolower($email), 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
			$userId = (int) $this->pdo->lastInsertId();
			$assignment = $this->pdo->prepare('INSERT INTO usuario_roles (id_usuario, id_rol) VALUES (:user_id, :role_id)');
			$assignment->execute(['user_id' => $userId, 'role_id' => $roleId]);
			if ($roleName === 'asesor') {
				$advisor = $this->pdo->prepare('INSERT INTO asesores (nombre, email, estado, id_usuario) VALUES (:name, :email, 1, :user_id)');
				$advisor->execute(['name' => $name, 'email' => strtolower($email), 'user_id' => $userId]);
			}
			$this->pdo->commit();
			return $userId;
		} catch (Throwable $exception) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			throw $exception;
		}
	}

	public function setUserStatus(int $userId, bool $active): bool
	{
		if ($userId < 1) {
			throw new InvalidArgumentException('El usuario no es válido.');
		}
		$statement = $this->pdo->prepare('UPDATE usuarios SET estado = :active WHERE id_usuario = :id');
		$statement->execute(['active' => $active ? 1 : 0, 'id' => $userId]);
		return $statement->rowCount() > 0;
	}

	public function updateProfile(int $userId, string $name, string $email, ?string $password, ?string $avatarDataUrl): array
	{
		if (mb_strlen(trim($name)) < 2 || mb_strlen(trim($name)) > 100) {
			throw new InvalidArgumentException('El nombre del perfil debe tener entre 2 y 100 caracteres.');
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException('El correo del perfil no es válido.');
		}
		if ($password !== null && $password !== '' && strlen($password) < 12) {
			throw new InvalidArgumentException('La contraseña debe tener al menos 12 caracteres.');
		}
		if ($avatarDataUrl !== null && trim($avatarDataUrl) !== '' && !preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/', $avatarDataUrl)) {
			throw new InvalidArgumentException('La foto de perfil no tiene un formato válido.');
		}
		if ($avatarDataUrl !== null && trim($avatarDataUrl) !== '' && strlen($avatarDataUrl) > 50000) {
			throw new InvalidArgumentException('La foto de perfil es demasiado grande. Elige una imagen más pequeña.');
		}

		$emailNormalized = strtolower(trim($email));
		$duplicate = $this->pdo->prepare('SELECT id_usuario FROM usuarios WHERE email = :email AND id_usuario != :id LIMIT 1');
		$duplicate->execute(['email' => $emailNormalized, 'id' => $userId]);
		if ($duplicate->fetchColumn() !== false) {
			throw new InvalidArgumentException('Ya existe otro usuario con ese correo electrónico.');
		}

		$sql = 'UPDATE usuarios SET nombres = :name, email = :email';
		$params = ['name' => trim($name), 'email' => strtolower(trim($email)), 'id' => $userId];
		if ($avatarDataUrl !== null && trim($avatarDataUrl) !== '') {
			$sql .= ', avatar_url = :avatar_url';
			$params['avatar_url'] = $avatarDataUrl;
		}
		if ($password !== null && trim($password) !== '') {
			$sql .= ', password_hash = :password_hash';
			$params['password_hash'] = password_hash(trim($password), PASSWORD_DEFAULT);
		}
		$sql .= ' WHERE id_usuario = :id';

		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);
		$current = $this->pdo->prepare('SELECT id_usuario AS id, nombres AS name, email, avatar_url AS avatar FROM usuarios WHERE id_usuario = :id LIMIT 1');
		$current->execute(['id' => $userId]);
		$row = $current->fetch(PDO::FETCH_ASSOC);
		if ($row === false) {
			throw new RuntimeException('No se pudo cargar el perfil actualizado.');
		}
		return $row;
	}
}