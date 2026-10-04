<?php
declare(strict_types=1);

final class ProspeccionModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        try {
            $nuevos = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa = 'Nuevo Lead' OR etapa IS NULL")->fetchColumn();
            $contactados = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa = 'Contactado'")->fetchColumn();
            $calientes = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE LOWER(COALESCE(NULLIF(TRIM(nivel_interes), ''), 'Bajo')) IN ('alto', 'urgente')")->fetchColumn();
            $recuperacion = (int) $this->pdo->query("SELECT COUNT(*) FROM leads WHERE etapa = 'Visita Realizada' OR etapa = 'Negociación'")->fetchColumn();
            $ratio = $nuevos + $contactados > 0 ? (($calientes / ($nuevos + $contactados)) * 100) : 0;
        } catch (PDOException $exception) {
            error_log('[ProspeccionModel] ' . $exception->getMessage());
            return [
                'nuevos' => 0,
                'contactados' => 0,
                'calientes' => 0,
                'recuperacion' => 0,
                'ratio' => 0.0,
            ];
        }

        return [
            'nuevos' => $nuevos,
            'contactados' => $contactados,
            'calientes' => $calientes,
            'recuperacion' => $recuperacion,
            'ratio' => $ratio,
        ];
    }

    public function getProspeccion(): array
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
    COALESCE(ca.nombre, 'Sin campaña') AS campana,
    COALESCE(ch.nombre, 'Sin canal') AS canal
FROM leads l
LEFT JOIN usuarios u ON u.id_usuario = l.id_usuario_asignado
LEFT JOIN campanas ca ON ca.id_campana = l.id_campana
LEFT JOIN canales ch ON ch.id_canal = ca.id_canal
ORDER BY l.id DESC
LIMIT 12
SQL)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $items[] = [
                    'id' => (int) ($row['id'] ?? 0),
                    'titulo' => (string) ($row['nombre'] ?? 'Lead sin nombre'),
                    'etapa' => (string) ($row['etapa'] ?? 'Nuevo Lead'),
                    'nivel' => (string) ($row['nivel_interes'] ?? 'Bajo'),
                    'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                    'campana' => (string) ($row['campana'] ?? 'Sin campaña'),
                    'canal' => (string) ($row['canal'] ?? 'Sin canal'),
                    'prioridad' => strtolower((string) ($row['nivel_interes'] ?? 'bajo')),
                ];
            }
        } catch (PDOException $exception) {
            error_log('[ProspeccionModel] ' . $exception->getMessage());
            return [];
        }

        return $items;
    }
}
