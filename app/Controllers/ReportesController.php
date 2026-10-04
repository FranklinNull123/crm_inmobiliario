<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/ReportesModel.php';

final class ReportesController
{
    public function __construct(private readonly ReportesModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'ventas_mes' => $this->model->getVentasPorMes(),
            'inventario' => $this->model->getInventarioPorEstado(),
            'top_proyectos' => $this->model->getTopProyectos(),
        ];
    }
}
