<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/CampanaModel.php';

final class CampanaController
{
    public function __construct(private readonly CampanaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'campanas' => $this->model->getCampanas(),
        ];
    }
}
