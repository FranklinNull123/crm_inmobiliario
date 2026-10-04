<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/CarteraModel.php';

final class CarteraController
{
    public function __construct(private readonly CarteraModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'cartera' => $this->model->getCartera(),
        ];
    }
}
