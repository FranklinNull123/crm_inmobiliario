<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/NotificacionModel.php';

final class NotificacionController
{
    public function __construct(private readonly NotificacionModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'notificaciones' => $this->model->getNotificaciones(),
        ];
    }
}
