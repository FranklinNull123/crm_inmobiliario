<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/LeadModel.php';
require_once __DIR__ . '/../Models/ProyectoModel.php';

final class CrmController
{
    public array $data = [];

    public function __construct(private readonly LeadModel $leadModel, private readonly ProyectoModel $proyectoModel)
    {
    }

    public function index(?int $userId = null): array
    {
        $this->data = [
            'leads' => $this->leadModel->getLeads($userId),
            'leadError' => null,
            'campaigns' => $this->leadModel->getCampaigns(),
            'advisors' => $this->leadModel->getAdvisors(),
            'projects' => $this->proyectoModel->getProjects(),
        ];

        return $this->data;
    }
}
