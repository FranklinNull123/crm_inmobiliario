<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/RecordatorioModel.php';

final class RecordatorioController
{
    public function __construct(private readonly RecordatorioModel $model)
    {
    }

    public function index(): array
    {
        return [
            'recordatorios' => $this->model->getRecordatorios(),
        ];
    }

    public function crear(array $input): int
    {
        return $this->model->crear($input);
    }
}
