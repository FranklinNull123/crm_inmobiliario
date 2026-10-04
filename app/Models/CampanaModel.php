<?php
declare(strict_types=1);

final class CampanaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    COUNT(*) AS total_campanas,
    SUM(CASE WHEN estado_lead = 'Venta Cerrada' THEN 1 ELSE 0 END) AS ventas_cerradas,
    SUM(CASE WHEN estado_lead IN ('Nuevo', 'Contactado', 'Visita Realizada', 'Negociación', 'Venta Cerrada') THEN 1 ELSE 0 END) AS prospeccion_total,
    COUNT(DISTINCT id_campana) AS campanas_activas
FROM clientes_leads
SQL;

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_campanas' => (int) ($row['total_campanas'] ?? 0),
            'ventas_cerradas' => (int) ($row['ventas_cerradas'] ?? 0),
            'prospeccion_total' => (int) ($row['prospeccion_total'] ?? 0),
            'campanas_activas' => (int) ($row['campanas_activas'] ?? 0),
        ];
    }

    public function getCampanas(): array
    {
        $sql = <<<'SQL'
SELECT
    c.id_campana AS id,
    c.nombre AS nombre,
    ch.nombre AS canal,
    COUNT(cl.id_cliente) AS leads,
    SUM(CASE WHEN cl.estado_lead = 'Venta Cerrada' THEN 1 ELSE 0 END) AS ventas_cerradas,
    SUM(CASE WHEN cl.estado_lead = 'Nuevo' THEN 1 ELSE 0 END) AS nuevos,
    SUM(CASE WHEN cl.estado_lead = 'Contactado' THEN 1 ELSE 0 END) AS contactados,
    SUM(CASE WHEN cl.estado_lead = 'Visita Realizada' THEN 1 ELSE 0 END) AS visitas,
    SUM(CASE WHEN cl.estado_lead = 'Negociación' THEN 1 ELSE 0 END) AS negociacion
FROM campanas c
LEFT JOIN canales ch ON ch.id_canal = c.id_canal
LEFT JOIN clientes_leads cl ON cl.id_campana = c.id_campana
GROUP BY c.id_campana, c.nombre, ch.nombre
ORDER BY leads DESC, c.nombre ASC
SQL;

        try {
            $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[CampanaModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'nombre' => (string) ($row['nombre'] ?? 'Campaña'),
                'canal' => (string) ($row['canal'] ?? 'Sin canal'),
                'leads' => (int) ($row['leads'] ?? 0),
                'ventas_cerradas' => (int) ($row['ventas_cerradas'] ?? 0),
                'nuevos' => (int) ($row['nuevos'] ?? 0),
                'contactados' => (int) ($row['contactados'] ?? 0),
                'visitas' => (int) ($row['visitas'] ?? 0),
                'negociacion' => (int) ($row['negociacion'] ?? 0),
            ];
        }, $rows);
    }
}
