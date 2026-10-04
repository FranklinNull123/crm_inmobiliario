<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ComisionModel.php';

final class ComisionController
{
    public function __construct(private readonly ComisionModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'comisiones' => $this->model->getComisiones(),
        ];
    }
}
