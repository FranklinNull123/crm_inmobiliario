<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ExpedienteModel.php';

final class ExpedienteController
{
    public function __construct(private readonly ExpedienteModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'expedientes' => $this->model->getExpedientes(),
        ];
    }
}
