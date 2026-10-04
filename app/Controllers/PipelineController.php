<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/PipelineModel.php';

final class PipelineController
{
    public function __construct(private readonly PipelineModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'pipeline' => $this->model->getPipeline(),
        ];
    }
}
