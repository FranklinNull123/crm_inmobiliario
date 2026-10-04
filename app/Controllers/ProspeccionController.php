<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ProspeccionModel.php';

final class ProspeccionController
{
    public function __construct(private readonly ProspeccionModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'prospeccion' => $this->model->getProspeccion(),
        ];
    }
}
