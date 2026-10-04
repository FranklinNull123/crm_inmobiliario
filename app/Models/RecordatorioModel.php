<?php
declare(strict_types=1);

final class RecordatorioModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getRecordatorios(): array
    {
        $sql = <<<'SQL'
SELECT
    id,
    titulo,
    descripcion,
    tipo,
    prioridad,
    fecha_programada,
    estado,
    DATE_FORMAT(creado_en, '%d %b %Y') AS creado_en
FROM recordatorios
ORDER BY fecha_programada ASC, id DESC
SQL;

        try {
            $statement = $this->pdo->query($sql);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[RecordatorioModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'titulo' => (string) ($row['titulo'] ?? 'Recordatorio'),
                'descripcion' => (string) ($row['descripcion'] ?? ''),
                'tipo' => (string) ($row['tipo'] ?? 'General'),
                'prioridad' => (string) ($row['prioridad'] ?? 'media'),
                'fecha_programada' => (string) ($row['fecha_programada'] ?? ''),
                'estado' => (string) ($row['estado'] ?? 'pendiente'),
                'creado_en' => (string) ($row['creado_en'] ?? ''),
            ];
        }, $rows);
    }

    public function crear(array $input): int
    {
        $titulo = trim((string) ($input['titulo'] ?? ''));
        $descripcion = trim((string) ($input['descripcion'] ?? ''));
        $tipo = in_array((string) ($input['tipo'] ?? ''), ['Lead', 'Cliente', 'Venta', 'General'], true) ? (string) $input['tipo'] : 'General';
        $prioridad = in_array((string) ($input['prioridad'] ?? ''), ['alta', 'media', 'baja'], true) ? (string) $input['prioridad'] : 'media';
        $fecha = trim((string) ($input['fecha_programada'] ?? ''));

        if ($titulo === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new InvalidArgumentException('Completa título y fecha válida para el recordatorio.');
        }

        $statement = $this->pdo->prepare('INSERT INTO recordatorios (titulo, descripcion, tipo, prioridad, fecha_programada, estado) VALUES (:titulo, :descripcion, :tipo, :prioridad, :fecha_programada, :estado)');
        $statement->execute([
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'tipo' => $tipo,
            'prioridad' => $prioridad,
            'fecha_programada' => $fecha,
            'estado' => 'pendiente',
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
