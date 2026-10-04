<?php
declare(strict_types=1);

final class NotificacionModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $alertas = $this->pdo->query('SELECT COUNT(*) FROM alertas')->fetchColumn();
            $recordatorios = $this->pdo->query('SELECT COUNT(*) FROM recordatorios WHERE fecha <= CURDATE() OR estado = "Pendiente"')->fetchColumn();
            $campanas = $this->pdo->query('SELECT COUNT(*) FROM campanas WHERE activo = 1')->fetchColumn();
            $pendientes = $this->pdo->query('SELECT COUNT(*) FROM cuotas WHERE pagada = 0')->fetchColumn();
        } catch (PDOException $exception) {
            error_log('[NotificacionModel] ' . $exception->getMessage());
            return ['alertas' => 0, 'recordatorios' => 0, 'campanas' => 0, 'pendientes' => 0, 'total' => 0];
        }

        $total = (int) ($alertas ?? 0) + (int) ($recordatorios ?? 0) + (int) ($campanas ?? 0) + (int) ($pendientes ?? 0);

        return [
            'alertas' => (int) ($alertas ?? 0),
            'recordatorios' => (int) ($recordatorios ?? 0),
            'campanas' => (int) ($campanas ?? 0),
            'pendientes' => (int) ($pendientes ?? 0),
            'total' => $total,
        ];
    }

    public function getNotificaciones(): array
    {
        $notificaciones = [];

        try {
            $alertas = $this->pdo->query('SELECT id, titulo, detalle, prioridad, fecha_creacion FROM alertas ORDER BY fecha_creacion DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($alertas as $item) {
                $notificaciones[] = [
                    'tipo' => 'Alerta',
                    'titulo' => (string) ($item['titulo'] ?? 'Alerta'),
                    'detalle' => (string) ($item['detalle'] ?? ''),
                    'prioridad' => (string) ($item['prioridad'] ?? 'Media'),
                    'fecha' => (string) ($item['fecha_creacion'] ?? date('Y-m-d')),
                ];
            }

            $recordatorios = $this->pdo->query('SELECT id, titulo, descripcion, fecha, prioridad FROM recordatorios ORDER BY fecha ASC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($recordatorios as $item) {
                $notificaciones[] = [
                    'tipo' => 'Recordatorio',
                    'titulo' => (string) ($item['titulo'] ?? 'Recordatorio'),
                    'detalle' => (string) ($item['descripcion'] ?? ''),
                    'prioridad' => (string) ($item['prioridad'] ?? 'Media'),
                    'fecha' => (string) ($item['fecha'] ?? date('Y-m-d')),
                ];
            }

            $campanas = $this->pdo->query('SELECT id, nombre, fecha_fin, estado FROM campanas WHERE activo = 1 ORDER BY fecha_fin ASC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($campanas as $item) {
                $notificaciones[] = [
                    'tipo' => 'Campaña',
                    'titulo' => (string) ($item['nombre'] ?? 'Campaña'),
                    'detalle' => 'Estado: ' . (string) ($item['estado'] ?? 'Activa'),
                    'prioridad' => 'Normal',
                    'fecha' => (string) ($item['fecha_fin'] ?? date('Y-m-d')),
                ];
            }

            $cuotas = $this->pdo->query('SELECT cu.id, c.nombre, cu.monto, cu.fecha_vencimiento FROM cuotas cu LEFT JOIN clientes c ON c.id = cu.id_cliente WHERE cu.pagada = 0 ORDER BY cu.fecha_vencimiento ASC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cuotas as $item) {
                $notificaciones[] = [
                    'tipo' => 'Cobranza',
                    'titulo' => 'Cuota pendiente',
                    'detalle' => (string) ($item['nombre'] ?? 'Cliente') . ' · S/ ' . number_format((float) ($item['monto'] ?? 0), 2),
                    'prioridad' => 'Alta',
                    'fecha' => (string) ($item['fecha_vencimiento'] ?? date('Y-m-d')),
                ];
            }
        } catch (PDOException $exception) {
            error_log('[NotificacionModel] ' . $exception->getMessage());
            return [];
        }

        usort($notificaciones, static function (array $a, array $b): int {
            return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
        });

        return array_slice($notificaciones, 0, 20);
    }
}
