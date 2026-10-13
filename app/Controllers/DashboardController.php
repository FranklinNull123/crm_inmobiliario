<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/DashboardModel.php';

final class DashboardController
{
    public function __construct(private readonly DashboardModel $dashboardModel)
    {
    }

    public function index(array $filters = []): array
    {
        return [
            'filters' => $this->dashboardModel->getFilterOptions(),
            'kpis' => $this->dashboardModel->getKpis($filters),
            'salesData' => $this->dashboardModel->getVentasVsMetas(),
            'recentSales' => $this->dashboardModel->getUltimasVentas($filters),
            'alerts' => $this->dashboardModel->getAlertas(),
        ];
    }
}
