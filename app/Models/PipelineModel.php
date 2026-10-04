<?php
declare(strict_types=1);

final class PipelineModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $totalLeads = (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
            $oportunidades = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa NOT IN ('Venta Cerrada', 'Rechazado')")->fetchColumn();
            $cerradas = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa = 'Venta Cerrada'")->fetchColumn();
            $ventasMes = (float) $this->pdo->query("SELECT COALESCE(SUM(u.precio), 0) FROM ventas v LEFT JOIN unidades u ON u.id = v.id_unidad WHERE DATE_FORMAT(v.fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn();
            $conversion = $totalLeads > 0 ? ($cerradas / $totalLeads) * 100 : 0;
        } catch (PDOException $exception) {
            error_log('[PipelineModel] ' . $exception->getMessage());
            return [
                'total_leads' => 0,
                'oportunidades' => 0,
                'cerradas' => 0,
                'ventas_mes' => 0.0,
                'conversion' => 0.0,
            ];
        }

        return [
            'total_leads' => $totalLeads,
            'oportunidades' => $oportunidades,
            'cerradas' => $cerradas,
            'ventas_mes' => $ventasMes,
            'conversion' => $conversion,
        ];
    }

    public function getPipeline(): array
    {
        $items = [];

        try {
            $rows = $this->pdo->query(<<<'SQL'
SELECT
    l.id,
    l.nombre,
    l.etapa,
    l.nivel_interes,
    COALESCE(u.nombres, 'Sin asignar') AS asesor,
    COALESCE(ca.nombre, 'Sin campaña') AS campaña,
    COALESCE(ch.nombre, 'Sin canal') AS canal
FROM leads l
LEFT JOIN usuarios u ON u.id_usuario = l.id_usuario_asignado
LEFT JOIN campanas ca ON ca.id_campana = l.id_campana
LEFT JOIN canales ch ON ch.id_canal = ca.id_canal
ORDER BY
    CASE l.etapa
        WHEN 'Nuevo Lead' THEN 1
        WHEN 'Contactado' THEN 2
        WHEN 'Visita Realizada' THEN 3
        WHEN 'Negociación' THEN 4
        WHEN 'Venta Cerrada' THEN 5
        ELSE 6
    END,
    l.id DESC
LIMIT 12
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $items[] = [
                    'id' => (int) ($row['id'] ?? 0),
                    'titulo' => (string) ($row['nombre'] ?? 'Lead sin nombre'),
                    'etapa' => (string) ($row['etapa'] ?? 'Nuevo Lead'),
                    'nivel' => (string) ($row['nivel_interes'] ?? 'Bajo'),
                    'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                    'campana' => (string) ($row['campaña'] ?? 'Sin campaña'),
                    'canal' => (string) ($row['canal'] ?? 'Sin canal'),
                    'prioridad' => strtolower((string) ($row['nivel_interes'] ?? 'bajo')),
                ];
            }
        } catch (PDOException $exception) {
            error_log('[PipelineModel] ' . $exception->getMessage());
            return [];
        }

        return $items;
    }
}
