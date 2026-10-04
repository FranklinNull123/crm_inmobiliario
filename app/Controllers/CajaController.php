<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/CajaModel.php';

final class CajaController
{
    public function __construct(private readonly CajaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'cuotas' => $this->model->getCuotas(),
            'summary' => $this->model->getResumen(),
        ];
    }

    public function registrarPago(array $input): bool
    {
        $cuotaId = filter_var($input['cuota_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($cuotaId === false) {
            throw new InvalidArgumentException('Selecciona una cuota válida.');
        }

        return $this->model->registrarPago($cuotaId);
    }
}
