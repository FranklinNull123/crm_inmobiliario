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
}
