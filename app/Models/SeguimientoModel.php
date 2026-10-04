<?php
declare(strict_types=1);

final class SeguimientoModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $leadsActivos = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa IS NOT NULL AND etapa NOT IN ('Venta Cerrada', 'Rechazado')")->fetchColumn();
            $leadsCalientes = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE LOWER(COALESCE(NULLIF(TRIM(nivel_interes), ''), 'Bajo')) IN ('alto', 'urgente') OR etapa IN ('Negociación', 'Visita Realizada')")->fetchColumn();
            $recordatoriosProximos = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE DATE(fecha) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
            $ventasMes = (float) $this->pdo->query("SELECT COALESCE(SUM(u.precio), 0) FROM ventas v LEFT JOIN unidades u ON u.id = v.id_unidad WHERE DATE_FORMAT(v.fecha, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetchColumn();
            $proyectosActivos = (int) $this->pdo->query("SELECT COUNT(*) FROM proyectos WHERE estado <> 'Cerrado'")->fetchColumn();
        } catch (PDOException $exception) {
            error_log('[SeguimientoModel] ' . $exception->getMessage());
            return [
                'leads_activos' => 0,
                'leads_calientes' => 0,
                'recordatorios_proximos' => 0,
                'ventas_mes' => 0.0,
                'proyectos_activos' => 0,
            ];
        }

        return [
            'leads_activos' => $leadsActivos,
            'leads_calientes' => $leadsCalientes,
            'recordatorios_proximos' => $recordatoriosProximos,
            'ventas_mes' => $ventasMes,
            'proyectos_activos' => $proyectosActivos,
        ];
    }

    public function getSeguimiento(): array
    {
        $items = [];

        try {
            $leadRows = $this->pdo->query(<<<'SQL'
SELECT
    l.id,
    l.nombre,
    l.etapa,
    l.nivel_interes,
    u.nombres AS asesor
FROM leads l
LEFT JOIN usuarios u ON u.id_usuario = l.id_usuario_asignado
WHERE l.etapa IS NOT NULL
  AND l.etapa NOT IN ('Venta Cerrada', 'Rechazado')
ORDER BY
    CASE WHEN LOWER(COALESCE(NULLIF(TRIM(l.nivel_interes), ''), 'Bajo')) IN ('alto', 'urgente') THEN 0 ELSE 1 END,
    l.id DESC
LIMIT 6
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($leadRows as $row) {
                $prioridad = strtolower((string) ($row['nivel_interes'] ?? 'Bajo'));
                $nivel = $prioridad === 'alto' || $prioridad === 'urgente' ? 'alta' : ($prioridad === 'medio' ? 'media' : 'normal');

                $items[] = [
                    'tipo' => 'Lead',
                    'titulo' => (string) ($row['nombre'] ?? 'Lead sin nombre'),
                    'detalle' => 'Etapa: ' . (string) ($row['etapa'] ?? 'Nuevo Lead') . ' · Asesor: ' . (string) ($row['asesor'] ?? 'Sin asignar'),
                    'prioridad' => $nivel,
                    'fecha' => date('Y-m-d'),
                    'estado' => (string) ($row['etapa'] ?? 'Nuevo Lead'),
                ];
            }

            $recordatorioRows = $this->pdo->query(<<<'SQL'
SELECT id, titulo, descripcion, fecha, prioridad
FROM recordatorios
ORDER BY DATE(fecha) ASC
LIMIT 6
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recordatorioRows as $row) {
                $items[] = [
                    'tipo' => 'Recordatorio',
                    'titulo' => (string) ($row['titulo'] ?? 'Recordatorio'),
                    'detalle' => (string) ($row['descripcion'] ?? 'Sin descripción'),
                    'prioridad' => strtolower((string) ($row['prioridad'] ?? 'media')),
                    'fecha' => (string) ($row['fecha'] ?? date('Y-m-d')),
                    'estado' => 'Pendiente',
                ];
            }

            $ventaRows = $this->pdo->query(<<<'SQL'
SELECT v.id, c.nombre AS cliente, v.fecha, u.precio
FROM ventas v
LEFT JOIN clientes c ON c.id = v.id_cliente
LEFT JOIN unidades u ON u.id = v.id_unidad
ORDER BY v.fecha DESC
LIMIT 5
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($ventaRows as $row) {
                $items[] = [
                    'tipo' => 'Venta',
                    'titulo' => 'Venta de ' . (string) ($row['cliente'] ?? 'cliente'),
                    'detalle' => 'Monto: S/ ' . number_format((float) ($row['precio'] ?? 0), 2),
                    'prioridad' => 'normal',
                    'fecha' => (string) ($row['fecha'] ?? date('Y-m-d')),
                    'estado' => 'Cerrada',
                ];
            }
        } catch (PDOException $exception) {
            error_log('[SeguimientoModel] ' . $exception->getMessage());
            return [];
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
        });

        return array_slice($items, 0, 15);
    }
}
