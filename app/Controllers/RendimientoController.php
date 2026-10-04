<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/RendimientoModel.php';

final class RendimientoController
{
    public function __construct(private readonly RendimientoModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'rendimiento' => $this->model->getRendimiento(),
        ];
    }
}
