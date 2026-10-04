<?php
declare(strict_types=1);

final class ExpedienteModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getResumen(): array
    {
        $sql = <<<'SQL'
SELECT
    (SELECT COUNT(DISTINCT id_cliente) FROM documentos) AS expedientes_totales,
    (SELECT COUNT(DISTINCT v.id_cliente)
     FROM ventas v
     LEFT JOIN documentos d ON d.id_cliente = v.id_cliente
     WHERE d.id IS NOT NULL) AS con_documentos,
    (SELECT COUNT(DISTINCT v.id_cliente)
     FROM ventas v
     LEFT JOIN documentos d ON d.id_cliente = v.id_cliente
     WHERE d.id IS NULL) AS pendientes_firma,
    (SELECT COUNT(*) FROM documentos WHERE nombre LIKE '%Contrato%' OR tipo_archivo = 'contract') AS contratos_activos
SQL;

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'expedientes_totales' => (int) ($row['expedientes_totales'] ?? 0),
            'con_documentos' => (int) ($row['con_documentos'] ?? 0),
            'pendientes_firma' => (int) ($row['pendientes_firma'] ?? 0),
            'contratos_activos' => (int) ($row['contratos_activos'] ?? 0),
        ];
    }

    public function getExpedientes(): array
    {
        $sql = <<<'SQL'
SELECT
    c.id,
    c.nombre AS cliente,
    a.nombre AS asesor,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    COUNT(d.id) AS documentos,
    SUM(CASE WHEN d.nombre LIKE '%Contrato%' OR d.tipo_archivo = 'contract' THEN 1 ELSE 0 END) AS contratos,
    MAX(CASE WHEN d.nombre LIKE '%Contrato%' OR d.tipo_archivo = 'contract' THEN d.nombre END) AS contrato_actual,
    MIN(v.fecha) AS fecha_venta
FROM clientes c
LEFT JOIN asesores a ON a.id = c.id_asesor
LEFT JOIN ventas v ON v.id_cliente = c.id
LEFT JOIN proyectos p ON p.id = v.id_proyecto
LEFT JOIN unidades u ON u.id = v.id_unidad
LEFT JOIN documentos d ON d.id_cliente = c.id
GROUP BY c.id, c.nombre, a.nombre, p.nombre, u.codigo
ORDER BY documentos DESC, c.nombre ASC
SQL;

        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static function (array $row): array {
            $documentos = (int) ($row['documentos'] ?? 0);
            $contratos = (int) ($row['contratos'] ?? 0);

            return [
                'id' => (int) ($row['id'] ?? 0),
                'cliente' => (string) ($row['cliente'] ?? 'Cliente'),
                'asesor' => (string) ($row['asesor'] ?? 'Sin asignar'),
                'proyecto' => (string) ($row['proyecto'] ?? '—'),
                'unidad' => (string) ($row['unidad'] ?? '—'),
                'documentos' => $documentos,
                'contratos' => $contratos,
                'contrato_actual' => (string) ($row['contrato_actual'] ?? 'Sin contrato'),
                'fecha_venta' => $row['fecha_venta'] ?? null,
                'estado' => $documentos >= 2 ? 'Listo para firma' : ($contratos > 0 ? 'En revisión' : 'Pendiente'),
            ];
        }, $rows);
    }
}
