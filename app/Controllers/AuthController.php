<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/AuthModel.php';

final class AuthController
{
	public function __construct(private readonly AuthModel $authModel)
	{
	}

	public function login(array $input): ?array
	{
		$email = strtolower(trim((string) ($input['email'] ?? '')));
		$password = (string) ($input['password'] ?? '');
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 4096) {
			return null;
		}
		return $this->authModel->authenticate($email, $password);
	}

	public function createInitialAdmin(array $input): void
	{
		$name = trim((string) ($input['name'] ?? ''));
		$email = strtolower(trim((string) ($input['email'] ?? '')));
		$password = (string) ($input['password'] ?? '');
		if (mb_strlen($name) < 2 || mb_strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12 || strlen($password) > 4096) {
			throw new InvalidArgumentException('Ingresa nombre, email válido y una contraseña de al menos 12 caracteres.');
		}
		$this->authModel->createInitialAdmin($name, $email, $password);
	}
}