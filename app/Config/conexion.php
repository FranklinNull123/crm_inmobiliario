<?php
declare(strict_types=1);

$environmentValue = static function (string $key, string $default): string {
	$value = getenv($key);

	return $value === false ? $default : $value;
};

$port = filter_var(
	$environmentValue('DB_PORT', '3306'),
	FILTER_VALIDATE_INT,
	['options' => ['min_range' => 1, 'max_range' => 65535]]
);

if ($port === false) {
	throw new RuntimeException('DB_PORT debe ser un puerto válido entre 1 y 65535.');
}

$configuration = [
	'host' => $environmentValue('DB_HOST', '127.0.0.1'),
	'port' => $port,
	'database' => $environmentValue('DB_NAME', 'crm_inmobiliario'),
	'username' => $environmentValue('DB_USER', 'root'),
	'password' => $environmentValue('DB_PASSWORD', ''),
	'charset' => 'utf8mb4',
];

$dsn = sprintf(
	'mysql:host=%s;port=%d;dbname=%s;charset=%s',
	$configuration['host'],
	$configuration['port'],
	$configuration['database'],
	$configuration['charset']
);

return new PDO(
	$dsn,
	$configuration['username'],
	$configuration['password'],
	[
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]
);