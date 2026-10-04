<?php
declare(strict_types=1);

final class AgendaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $hoy = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE DATE(fecha) = CURDATE() OR estado = 'Pendiente'")->fetchColumn();
            $semana = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE DATE(fecha) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetchColumn();
            $visitas = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa = 'Visita Realizada' OR etapa = 'Contactado'")->fetchColumn();
            $citas = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE tipo = 'Cita' OR titulo LIKE '%cita%' ")->fetchColumn();
            $proximas = (int) $this->pdo->query("SELECT COUNT(*) FROM recordatorios WHERE DATE(fecha) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
        } catch (PDOException $exception) {
            error_log('[AgendaModel] ' . $exception->getMessage());
            return [
                'hoy' => 0,
                'semana' => 0,
                'visitas' => 0,
                'citas' => 0,
                'proximas' => 0,
            ];
        }

        return [
            'hoy' => $hoy,
            'semana' => $semana,
            'visitas' => $visitas,
            'citas' => $citas,
            'proximas' => $proximas,
        ];
    }

    public function getAgenda(): array
    {
        $items = [];

        try {
            $recordatorios = $this->pdo->query(<<<'SQL'
SELECT id, titulo, descripcion, fecha, prioridad, estado, tipo
FROM recordatorios
ORDER BY DATE(fecha) ASC, id DESC
LIMIT 10
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recordatorios as $item) {
                $items[] = [
                    'tipo' => (string) ($item['tipo'] ?? 'Recordatorio'),
                    'titulo' => (string) ($item['titulo'] ?? 'Recordatorio'),
                    'detalle' => (string) ($item['descripcion'] ?? 'Sin descripción'),
                    'fecha' => (string) ($item['fecha'] ?? date('Y-m-d')),
                    'prioridad' => strtolower((string) ($item['prioridad'] ?? 'media')),
                    'estado' => (string) ($item['estado'] ?? 'Pendiente'),
                ];
            }

            $visitas = $this->pdo->query(<<<'SQL'
SELECT l.id, l.nombre, l.etapa, l.nivel_interes, l.id_usuario_asignado, u.nombres AS asesor
FROM leads l
LEFT JOIN usuarios u ON u.id_usuario = l.id_usuario_asignado
WHERE l.etapa IN ('Contactado', 'Visita Realizada', 'Negociación')
ORDER BY l.id DESC
LIMIT 8
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($visitas as $item) {
                $items[] = [
                    'tipo' => 'Visita',
                    'titulo' => (string) ($item['nombre'] ?? 'Lead sin nombre'),
                    'detalle' => 'Etapa: ' . (string) ($item['etapa'] ?? 'Sin etapa') . ' · Asesor: ' . (string) ($item['asesor'] ?? 'Sin asignar'),
                    'fecha' => date('Y-m-d'),
                    'prioridad' => strtolower((string) ($item['nivel_interes'] ?? 'Bajo')),
                    'estado' => (string) ($item['etapa'] ?? 'Contactado'),
                ];
            }
        } catch (PDOException $exception) {
            error_log('[AgendaModel] ' . $exception->getMessage());
            return [];
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp((string) ($a['fecha'] ?? ''), (string) ($b['fecha'] ?? ''));
        });

        return array_slice($items, 0, 18);
    }
}
