<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/InventarioModel.php';

final class InventarioController
{
    public array $data = [];

    public function __construct(private readonly InventarioModel $inventarioModel)
    {
    }

    public function index(): array
    {
        $this->data = [
            'units' => $this->inventarioModel->getUnidades(),
            'totalResults' => $this->inventarioModel->countUnidades(),
            'statuses' => $this->inventarioModel->getStatuses(),
        ];

        return $this->data;
    }

    public function saveUnit(array $input): int
    {
        $id = $this->optionalId($input['id'] ?? null);
        $projectId = filter_var($input['project_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stageId = $this->optionalId($input['stage_id'] ?? null);
        $area = filter_var($input['area'] ?? null, FILTER_VALIDATE_FLOAT);
        $price = filter_var($input['price'] ?? null, FILTER_VALIDATE_FLOAT);
        $code = trim((string) ($input['code'] ?? ''));
        $type = trim((string) ($input['type'] ?? ''));
        $status = trim((string) ($input['status'] ?? ''));

        if ($projectId === false || $area === false || $price === false || !preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{1,29}$/', $code) || mb_strlen($type) < 2 || mb_strlen($type) > 30 || $area <= 0 || $area > 100000 || $price <= 0 || $price > 9999999999 || $status === '') {
            throw new InvalidArgumentException('Revisa código, proyecto, tipo, área, precio y estado de la unidad.');
        }

        return $this->inventarioModel->saveUnit($id, [
            'projectId' => $projectId,
            'stageId' => $stageId,
            'code' => $code,
            'type' => $type,
            'area' => $area,
            'price' => $price,
            'status' => $status,
        ]);
    }

    public function archiveUnit(mixed $id): void
    {
        $unitId = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($unitId === false) {
            throw new InvalidArgumentException('El identificador de unidad no es válido.');
        }
        $this->inventarioModel->archiveUnit($unitId);
    }

    private function optionalId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            throw new InvalidArgumentException('El identificador no es válido.');
        }
        return $id;
    }
}
