<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/LeadModel.php';

final class CrmController
{
    public array $data = [];

    public function __construct(private readonly LeadModel $leadModel)
    {
    }

    public function index(?int $userId = null): array
    {
        $this->data = [
            'leads' => $this->leadModel->getLeads($userId),
            'leadError' => null,
            'campaigns' => $this->leadModel->getCampaigns(),
            'advisors' => $this->leadModel->getAdvisors(),
        ];

        return $this->data;
    }
}
