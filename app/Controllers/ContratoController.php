<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ContratoModel.php';

final class ContratoController
{
    public function __construct(private readonly ContratoModel $model)
    {
    }

    public function index(): array
    {
        return [
            'contracts' => $this->model->getContratos(),
        ];
    }

    public function crear(array $input): int
    {
        $clienteId = filter_var($input['cliente_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $nombre = trim((string) ($input['nombre'] ?? ''));

        if ($clienteId === false || $nombre === '') {
            throw new InvalidArgumentException('El contrato necesita cliente y nombre válidos.');
        }

        return $this->model->crear([
            'cliente_id' => $clienteId,
            'nombre' => $nombre,
        ]);
    }
}
