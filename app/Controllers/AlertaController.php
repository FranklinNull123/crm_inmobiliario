<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/AlertaModel.php';

final class AlertaController
{
    public function __construct(private readonly AlertaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'alertas' => $this->model->getAlertas(),
        ];
    }
}
