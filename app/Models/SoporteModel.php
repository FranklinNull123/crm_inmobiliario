<?php
declare(strict_types=1);

final class SoporteModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    COUNT(*) AS total_eventos,
    SUM(CASE WHEN DATE(creado_en) = CURDATE() THEN 1 ELSE 0 END) AS eventos_hoy,
    COUNT(DISTINCT id_usuario) AS usuarios_activos,
    SUM(CASE WHEN evento LIKE '%fallido%' OR evento LIKE '%error%' OR evento LIKE '%rechazado%' THEN 1 ELSE 0 END) AS eventos_criticos
FROM auditoria
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $row = $statement->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            error_log('[SoporteModel] ' . $exception->getMessage());
            return ['total_eventos' => 0, 'eventos_hoy' => 0, 'usuarios_activos' => 0, 'eventos_criticos' => 0];
        }

        return [
            'total_eventos' => (int) ($row['total_eventos'] ?? 0),
            'eventos_hoy' => (int) ($row['eventos_hoy'] ?? 0),
            'usuarios_activos' => (int) ($row['usuarios_activos'] ?? 0),
            'eventos_criticos' => (int) ($row['eventos_criticos'] ?? 0),
        ];
    }

    public function getEventos(): array
    {
        $sql = <<<'SQL'
SELECT
    a.id,
    a.evento,
    a.entidad,
    a.entidad_id,
    u.nombres AS usuario,
    a.ip,
    a.dispositivo,
    a.creado_en
FROM auditoria AS a
LEFT JOIN usuarios AS u ON u.id_usuario = a.id_usuario
ORDER BY a.creado_en DESC
LIMIT 20
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[SoporteModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'evento' => (string) ($row['evento'] ?? 'Evento'),
                'entidad' => (string) ($row['entidad'] ?? 'Sistema'),
                'entidad_id' => (string) ($row['entidad_id'] ?? ''),
                'usuario' => (string) ($row['usuario'] ?? 'Sistema'),
                'ip' => (string) ($row['ip'] ?? ''),
                'dispositivo' => (string) ($row['dispositivo'] ?? 'N/D'),
                'creado_en' => (string) ($row['creado_en'] ?? ''),
            ];
        }, $rows);
    }
}
