<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/DashboardModel.php';

final class DashboardController
{
    public function __construct(private readonly DashboardModel $dashboardModel)
    {
    }

    public function index(): array
    {
        return [
            'salesData' => $this->dashboardModel->getVentasVsMetas(),
            'recentSales' => $this->dashboardModel->getUltimasVentas(),
            'alerts' => $this->dashboardModel->getAlertas(),
        ];
    }
}
