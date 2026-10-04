<?php
declare(strict_types=1);

final class IncidenciaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    (SELECT COUNT(*) FROM unidades WHERE estado IN ('Reservado', 'Separado', 'Vendido')) AS incidencias_totales,
    (SELECT COUNT(*) FROM unidades WHERE estado IN ('Reservado', 'Separado')) AS bloqueadas,
    (SELECT COUNT(*) FROM cuotas WHERE pagada = 0 AND fecha_vencimiento < CURDATE()) AS vencidas,
    (SELECT COUNT(*) FROM separaciones WHERE fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) AS proximas,
    (SELECT COUNT(*) FROM clientes_leads WHERE estado_lead IN ('Negociación', 'Visita Realizada')) AS seguimiento_activo
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            error_log('[IncidenciaModel] ' . $exception->getMessage());
            return ['incidencias_totales' => 0, 'bloqueadas' => 0, 'vencidas' => 0, 'proximas' => 0, 'seguimiento_activo' => 0, 'resolucion' => 0];
        }

        return [
            'incidencias_totales' => (int) ($row['incidencias_totales'] ?? 0),
            'bloqueadas' => (int) ($row['bloqueadas'] ?? 0),
            'vencidas' => (int) ($row['vencidas'] ?? 0),
            'proximas' => (int) ($row['proximas'] ?? 0),
            'seguimiento_activo' => (int) ($row['seguimiento_activo'] ?? 0),
            'resolucion' => 0,
        ];
    }

    public function getIncidencias(): array
    {
        $items = [];

        $blockRows = $this->pdo->query(
            <<<'SQL'
SELECT
    u.id,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    u.estado,
    u.tipo,
    u.precio,
    u.actualizado_en,
    'Unidad bloqueada' AS tipo_incidente,
    'Revisión de operación' AS detalle
FROM unidades u
LEFT JOIN proyectos p ON p.id = u.id_proyecto
WHERE u.estado IN ('Reservado', 'Separado')
ORDER BY u.actualizado_en DESC
LIMIT 5
SQL
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($blockRows as $row) {
            $items[] = [
                'categoria' => 'Operación',
                'titulo' => (string) ($row['unidad'] ?? 'Unidad'),
                'detalle' => (string) ($row['proyecto'] ?? 'Proyecto') . ' · ' . (string) ($row['estado'] ?? 'Bloqueada'),
                'prioridad' => 'alta',
                'estado' => (string) ($row['estado'] ?? 'Bloqueada'),
                'valor' => (float) ($row['precio'] ?? 0),
                'fecha' => (string) ($row['actualizado_en'] ?? date('Y-m-d')),
            ];
        }

        $dueRows = $this->pdo->query(
            <<<'SQL'
SELECT
    c.id AS cuota_id,
    cli.nombre AS cliente,
    u.codigo AS unidad,
    c.monto,
    c.fecha_vencimiento,
    'Cuota vencida' AS tipo_incidente,
    'Cobranza pendiente' AS detalle
FROM cuotas c
LEFT JOIN clientes cli ON cli.id = c.id_cliente
LEFT JOIN ventas v ON v.id = c.id_venta
LEFT JOIN unidades u ON u.id = v.id_unidad
WHERE c.pagada = 0 AND c.fecha_vencimiento < CURDATE()
ORDER BY c.fecha_vencimiento ASC
LIMIT 5
SQL
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dueRows as $row) {
            $items[] = [
                'categoria' => 'Cobranza',
                'titulo' => (string) ($row['cliente'] ?? 'Cliente'),
                'detalle' => 'Cuota vencida · ' . (string) ($row['unidad'] ?? 'Sin unidad'),
                'prioridad' => 'alta',
                'estado' => 'Vencida',
                'valor' => (float) ($row['monto'] ?? 0),
                'fecha' => (string) ($row['fecha_vencimiento'] ?? date('Y-m-d')),
            ];
        }

        $leadRows = $this->pdo->query(
            <<<'SQL'
SELECT
    cl.id_cliente AS id,
    CONCAT(cl.nombres, ' / ', cl.estado_lead) AS titulo,
    cl.estado_lead,
    CURRENT_DATE AS fecha,
    'Seguimiento activo' AS detalle
FROM clientes_leads cl
WHERE cl.estado_lead IN ('Negociación', 'Visita Realizada', 'Contactado', 'Visita Agendada')
ORDER BY cl.id_cliente DESC
LIMIT 5
SQL
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($leadRows as $row) {
            $items[] = [
                'categoria' => 'CRM',
                'titulo' => (string) ($row['titulo'] ?? 'Lead'),
                'detalle' => (string) ($row['detalle'] ?? 'Seguimiento activo'),
                'prioridad' => 'media',
                'estado' => (string) ($row['estado_lead'] ?? 'Seguimiento'),
                'valor' => 0.0,
                'fecha' => (string) ($row['fecha'] ?? date('Y-m-d')),
            ];
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
        });

        return array_slice($items, 0, 15);
    }
}
