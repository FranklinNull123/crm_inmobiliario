<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/SeparacionModel.php';

final class SeparacionController
{
    public array $data = [];

    public function __construct(private readonly SeparacionModel $separacionModel)
    {
    }

    public function index(): array
    {
        $this->data = [
            'sepUnits' => $this->separacionModel->getUnidadesDisponibles(),
        ];

        return $this->data;
    }

    public function registrar(array $input): bool
    {
        return $this->separacionModel->registrar($input);
    }
}
