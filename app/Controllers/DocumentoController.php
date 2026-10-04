<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/DocumentoModel.php';

final class DocumentoController
{
    public function __construct(private readonly DocumentoModel $model)
    {
    }

    public function index(int $clienteId): array
    {
        return ['documents' => $this->model->getByCliente($clienteId)];
    }

    public function crear(array $input): int
    {
        $clienteId = filter_var($input['cliente_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $nombre = trim((string) ($input['nombre'] ?? ''));
        $tipoArchivo = trim((string) ($input['tipo_archivo'] ?? 'file-signature'));
        $tamano = trim((string) ($input['tamano'] ?? '2.4 MB'));

        if ($clienteId === false || $nombre === '') {
            throw new InvalidArgumentException('El documento no tiene cliente o nombre válidos.');
        }

        return $this->model->crear($clienteId, $nombre, $tipoArchivo, $tamano);
    }
}
