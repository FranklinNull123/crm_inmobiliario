<?php
declare(strict_types=1);

function cms_current_user(): ?array
{
	$user = $_SESSION['user'] ?? null;
	return is_array($user) ? $user : null;
}

function cms_has_permission(PDO $pdo, string $module, string $action): bool
{
	$user = cms_current_user();
	if ($user === null) {
		return false;
	}
	$statement = $pdo->prepare("SELECT 1 FROM usuario_roles ur JOIN roles r ON r.id = ur.id_rol JOIN rol_permisos rp ON rp.id_rol = r.id JOIN permisos p ON p.id = rp.id_permiso WHERE ur.id_usuario = :user_id AND r.activo = 1 AND p.modulo = :module AND p.accion = :action AND rp.permitido = 1 LIMIT 1");
	$statement->execute(['user_id' => (int) $user['id'], 'module' => $module, 'action' => $action]);
	return $statement->fetchColumn() !== false;
}

function cms_audit(PDO $pdo, string $event, string $entity, ?string $entityId = null, ?array $old = null, ?array $new = null): void
{
	$user = cms_current_user();
	$statement = $pdo->prepare('INSERT INTO auditoria (id_usuario, evento, entidad, entidad_id, datos_anteriores, datos_nuevos, ip, dispositivo) VALUES (:user_id, :event, :entity, :entity_id, :old_data, :new_data, :ip, :device)');
	$statement->execute([
		'user_id' => $user['id'] ?? null,
		'event' => $event,
		'entity' => $entity,
		'entity_id' => $entityId,
		'old_data' => $old === null ? null : json_encode($old, JSON_THROW_ON_ERROR),
		'new_data' => $new === null ? null : json_encode($new, JSON_THROW_ON_ERROR),
		'ip' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
		'device' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
	]);
}

function cms_valid_csrf(mixed $submitted): bool
{
	$expected = $_SESSION['csrf_token'] ?? '';
	return is_string($submitted) && is_string($expected) && $expected !== '' && hash_equals($expected, $submitted);
}