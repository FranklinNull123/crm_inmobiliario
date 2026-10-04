<?php
declare(strict_types=1);

if (!defined('BASE_PATH')) {
	define('BASE_PATH', dirname(__DIR__));
}

try {
	$pdo = require BASE_PATH . '/app/Config/conexion.php';
	return $pdo;
} catch (Throwable $exception) {
	error_log('[Bootstrap] Database initialization failed: ' . $exception->getMessage());
	http_response_code(503);
	exit('No se pudo conectar a MySQL. Inicia MySQL en WAMP y verifica la configuración y la base de datos actual.');
}
