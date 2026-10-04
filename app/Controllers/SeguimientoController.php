<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/SeguimientoModel.php';

final class SeguimientoController
{
    public function __construct(private readonly SeguimientoModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'seguimiento' => $this->model->getSeguimiento(),
        ];
    }
}
