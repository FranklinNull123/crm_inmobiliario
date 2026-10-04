<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/AgendaModel.php';

final class AgendaController
{
    public function __construct(private readonly AgendaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'agenda' => $this->model->getAgenda(),
        ];
    }
}
