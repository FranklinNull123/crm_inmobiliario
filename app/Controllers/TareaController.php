<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/TareaModel.php';

final class TareaController
{
    public function __construct(private readonly TareaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'tareas' => $this->model->getTareas(),
        ];
    }
}
