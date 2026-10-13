<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ClienteModel.php';

final class ClienteController
{
    public array $data = [];

    public function __construct(private readonly ClienteModel $clienteModel)
    {
    }

    public function index(?int $userId = null): array
    {
        $this->data = [
            'clients' => $this->clienteModel->getClientes($userId),
        ];

        return $this->data;
    }

    public function detalle(int $id, ?int $userId = null): array
    {
        $cliente = $this->clienteModel->getCliente($id, $userId);
        $this->data = [
            'clients' => $cliente === null ? [] : [$cliente],
            'clientDocs' => $cliente === null ? [] : $this->clienteModel->getDocumentos($id),
            'clientContacts' => $cliente === null ? [] : $this->clienteModel->getContactos($id),
        ];

        return $this->data;
    }

    public function guardarContacto(array $payload): int
    {
        return $this->clienteModel->guardarContacto($payload);
    }

    public function contactos(int $clienteId): array
    {
        return $this->clienteModel->getContactos($clienteId);
    }

    public function actualizar(mixed $id, array $input, ?int $userId = null): bool
    {
        $clientId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($clientId === false) {
            throw new InvalidArgumentException('El identificador del cliente no es válido.');
        }

        $name = trim((string) ($input['name'] ?? ''));
        $dni = trim((string) ($input['dni'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $status = trim((string) ($input['status'] ?? ''));
        $data = [
            'name' => $name,
            'dni' => $dni,
            'email' => $email,
            'phone' => trim((string) ($input['phone'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'occupation' => trim((string) ($input['occupation'] ?? '')),
            'civil_status' => trim((string) ($input['civil_status'] ?? '')),
            'status' => $status,
        ];

        if ($name === '' || mb_strlen($name, 'UTF-8') > 120) {
            throw new InvalidArgumentException('El nombre es obligatorio y debe tener hasta 120 caracteres.');
        }
        if ($dni === '' || mb_strlen($dni, 'UTF-8') > 20) {
            throw new InvalidArgumentException('El DNI es obligatorio y debe tener hasta 20 caracteres.');
        }
        if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email, 'UTF-8') > 120)) {
            throw new InvalidArgumentException('El correo electrónico no es válido.');
        }
        if (!in_array($status, ['Cliente Activo', 'En Mora', 'Cancelado'], true)) {
            throw new InvalidArgumentException('El estado del cliente no es válido.');
        }
        if (mb_strlen($data['phone'], 'UTF-8') > 30 || mb_strlen($data['address'], 'UTF-8') > 200 || mb_strlen($data['occupation'], 'UTF-8') > 100 || mb_strlen($data['civil_status'], 'UTF-8') > 50) {
            throw new InvalidArgumentException('Uno de los datos del cliente excede la longitud permitida.');
        }

        return $this->clienteModel->actualizar($clientId, $data, $userId);
    }
}
