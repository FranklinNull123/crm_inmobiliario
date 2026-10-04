<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/VentaModel.php';

final class VentaController
{
    public function __construct(private readonly VentaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'sales' => $this->model->getVentas(),
            'clients' => $this->model->getClientesActivos(),
            'units' => $this->model->getUnidadesVendibles(),
        ];
    }

    public function registrar(array $input): int
    {
        $clientId = filter_var($input['cliente_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $unitId = filter_var($input['unidad_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $cuotas = filter_var($input['cuotas'] ?? 12, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 120]]);
        $montoTotal = filter_var($input['monto_total'] ?? null, FILTER_VALIDATE_FLOAT);
        $fecha = trim((string) ($input['fecha'] ?? date('Y-m-d')));

        if ($clientId === false || $unitId === false || $cuotas === false || $montoTotal === false || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new InvalidArgumentException('Completa cliente, unidad, monto, cuotas y fecha válidos.');
        }

        return $this->model->registrarVenta([
            'cliente_id' => $clientId,
            'unidad_id' => $unitId,
            'monto_total' => $montoTotal,
            'cuotas' => $cuotas,
            'fecha' => $fecha,
        ]);
    }
}
