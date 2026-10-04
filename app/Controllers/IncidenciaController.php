<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/IncidenciaModel.php';

final class IncidenciaController
{
    public function __construct(private readonly IncidenciaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'incidencias' => $this->model->getIncidencias(),
        ];
    }
}
