<?php
declare(strict_types=1);

final class TareaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $pendientes = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa IS NOT NULL AND etapa NOT IN ('Venta Cerrada', 'Rechazado')")->fetchColumn();
            $hoy = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE DATE(fecha) = CURDATE() OR estado = 'Pendiente'")->fetchColumn();
            $alertas = (int) $this->pdo->query("SELECT COUNT(*) FROM alertas WHERE estado = 'Activa' OR estado IS NULL")->fetchColumn();
            $separaciones = (int) $this->pdo->query("SELECT COUNT(*) FROM separaciones WHERE estado = 'Pendiente' OR estado = 'En revisión'")->fetchColumn();
        } catch (PDOException $exception) {
            error_log('[TareaModel] ' . $exception->getMessage());
            return [
                'pendientes' => 0,
                'hoy' => 0,
                'alertas' => 0,
                'separaciones' => 0,
                'total' => 0,
            ];
        }

        $total = $pendientes + $hoy + $alertas + $separaciones;

        return [
            'pendientes' => $pendientes,
            'hoy' => $hoy,
            'alertas' => $alertas,
            'separaciones' => $separaciones,
            'total' => $total,
        ];
    }

    public function getTareas(): array
    {
        $items = [];

        try {
            $leadRows = $this->pdo->query(<<<'SQL'
SELECT l.id, l.nombre, l.etapa, l.nivel_interes, u.nombres AS asesor
FROM leads l
LEFT JOIN usuarios u ON u.id_usuario = l.id_usuario_asignado
WHERE l.etapa IS NOT NULL
  AND l.etapa NOT IN ('Venta Cerrada', 'Rechazado')
ORDER BY CASE WHEN LOWER(COALESCE(NULLIF(TRIM(l.nivel_interes), ''), 'Bajo')) IN ('alto', 'urgente') THEN 0 ELSE 1 END,
         l.id DESC
LIMIT 5
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($leadRows as $row) {
                $items[] = [
                    'tipo' => 'Lead',
                    'titulo' => (string) ($row['nombre'] ?? 'Lead sin nombre'),
                    'detalle' => 'Etapa: ' . (string) ($row['etapa'] ?? 'Nuevo Lead') . ' · Asesor: ' . (string) ($row['asesor'] ?? 'Sin asignar'),
                    'prioridad' => strtolower((string) ($row['nivel_interes'] ?? 'Bajo')),
                    'fecha' => date('Y-m-d'),
                    'estado' => (string) ($row['etapa'] ?? 'Activo'),
                ];
            }

            $recordatorioRows = $this->pdo->query(<<<'SQL'
SELECT id, titulo, descripcion, fecha, prioridad
FROM recordatorios
WHERE estado = 'Pendiente' OR DATE(fecha) <= CURDATE()
ORDER BY DATE(fecha) ASC
LIMIT 5
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

            $alertaRows = $this->pdo->query(<<<'SQL'
SELECT id, titulo, detalle, prioridad, fecha_creacion
FROM alertas
WHERE estado = 'Activa' OR estado IS NULL
ORDER BY fecha_creacion DESC
LIMIT 5
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($alertaRows as $row) {
                $items[] = [
                    'tipo' => 'Alerta',
                    'titulo' => (string) ($row['titulo'] ?? 'Alerta'),
                    'detalle' => (string) ($row['detalle'] ?? 'Sin detalle'),
                    'prioridad' => strtolower((string) ($row['prioridad'] ?? 'media')),
                    'fecha' => (string) ($row['fecha_creacion'] ?? date('Y-m-d')),
                    'estado' => 'Activa',
                ];
            }

            $separacionRows = $this->pdo->query(<<<'SQL'
SELECT s.id, u.codigo, c.nombre, s.estado, s.fecha_reserva
FROM separaciones s
LEFT JOIN unidades u ON u.id = s.id_unidad
LEFT JOIN clientes c ON c.id = s.id_cliente
WHERE s.estado IN ('Pendiente', 'En revisión')
ORDER BY s.fecha_reserva DESC
LIMIT 5
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($separacionRows as $row) {
                $items[] = [
                    'tipo' => 'Separación',
                    'titulo' => 'Separación de ' . (string) ($row['codigo'] ?? 'unidad'),
                    'detalle' => (string) ($row['nombre'] ?? 'Cliente') . ' · Estado: ' . (string) ($row['estado'] ?? 'Pendiente'),
                    'prioridad' => 'media',
                    'fecha' => (string) ($row['fecha_reserva'] ?? date('Y-m-d')),
                    'estado' => (string) ($row['estado'] ?? 'Pendiente'),
                ];
            }
        } catch (PDOException $exception) {
            error_log('[TareaModel] ' . $exception->getMessage());
            return [];
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
        });

        return array_slice($items, 0, 18);
    }
}
