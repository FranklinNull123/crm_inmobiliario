<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/SoporteModel.php';

final class SoporteController
{
    public function __construct(private readonly SoporteModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'eventos' => $this->model->getEventos(),
        ];
    }
}
