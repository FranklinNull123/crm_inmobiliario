<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/CobranzaModel.php';

final class CobranzaController
{
    public function __construct(private readonly CobranzaModel $model)
    {
    }

    public function index(): array
    {
        return [
            'resumen' => $this->model->getResumen(),
            'cobranzas' => $this->model->getCobranza(),
        ];
    }
}
