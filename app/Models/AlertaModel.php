<?php
declare(strict_types=1);

final class AlertaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    (SELECT COUNT(*) FROM clientes_leads WHERE estado_lead NOT IN ('Venta Cerrada')) AS leads_activos,
    (SELECT COUNT(*) FROM cuotas WHERE pagada = 0 AND fecha_vencimiento < CURDATE()) AS cuotas_vencidas,
    (SELECT COUNT(*) FROM separaciones WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AS separaciones_proximas,
    (SELECT COUNT(*) FROM unidades WHERE estado IN ('Reservado', 'Separado')) AS unidades_bloqueadas
SQL;

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'leads_activos' => (int) ($row['leads_activos'] ?? 0),
            'cuotas_vencidas' => (int) ($row['cuotas_vencidas'] ?? 0),
            'separaciones_proximas' => (int) ($row['separaciones_proximas'] ?? 0),
            'unidades_bloqueadas' => (int) ($row['unidades_bloqueadas'] ?? 0),
        ];
    }

    public function getAlertas(): array
    {
        $items = [];

        $pipelineRows = $this->pdo->query(
            "SELECT estado_lead, COUNT(*) AS total FROM clientes_leads GROUP BY estado_lead ORDER BY total DESC LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pipelineRows as $row) {
            $items[] = [
                'tipo' => 'pipeline',
                'titulo' => (string) ($row['estado_lead'] ?? 'Lead'),
                'detalle' => 'Seguimiento activo del CRM',
                'valor' => (int) ($row['total'] ?? 0),
                'prioridad' => $this->prioridadPipeline((string) ($row['estado_lead'] ?? 'Lead')),
            ];
        }

        $overdueRows = $this->pdo->query(
            <<<'SQL'
SELECT
    c.id AS cuota_id,
    cli.nombre AS cliente,
    c.numero_cuota,
    c.monto,
    c.fecha_vencimiento
FROM cuotas c
LEFT JOIN clientes cli ON cli.id = c.id_cliente
WHERE c.pagada = 0 AND c.fecha_vencimiento < CURDATE()
ORDER BY c.fecha_vencimiento ASC
LIMIT 5
SQL
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($overdueRows as $row) {
            $items[] = [
                'tipo' => 'cuota',
                'titulo' => 'Cuota vencida',
                'detalle' => (string) ($row['cliente'] ?? 'Cliente sin nombre') . ' · cuota ' . ($row['numero_cuota'] ?? 0),
                'valor' => (float) ($row['monto'] ?? 0),
                'prioridad' => 'alta',
            ];
        }

        $soonRows = $this->pdo->query(
            <<<'SQL'
SELECT
    s.id,
    c.nombre AS cliente,
    u.codigo,
    s.fecha_vencimiento,
    s.monto
FROM separaciones s
LEFT JOIN clientes c ON c.id = s.id_cliente
LEFT JOIN unidades u ON u.id = s.id_unidad
WHERE s.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY s.fecha_vencimiento ASC
LIMIT 5
SQL
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($soonRows as $row) {
            $items[] = [
                'tipo' => 'separacion',
                'titulo' => 'Separación próxima',
                'detalle' => (string) ($row['cliente'] ?? 'Cliente sin nombre') . ' · ' . ($row['codigo'] ?? 'Sin unidad'),
                'valor' => (float) ($row['monto'] ?? 0),
                'prioridad' => 'media',
            ];
        }

        return $items;
    }

    private function prioridadPipeline(string $estado): string
    {
        return match ($estado) {
            'Nuevo', 'Contactado' => 'media',
            'Visita Realizada', 'Negociación' => 'alta',
            'Venta Cerrada' => 'baja',
            default => 'media',
        };
    }
}
