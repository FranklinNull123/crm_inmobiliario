<?php
declare(strict_types=1);

final class VentaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getVentas(): array
    {
        $sql = <<<'SQL'
SELECT
    v.id,
    c.nombre AS cliente,
    p.nombre AS proyecto,
    u.codigo AS unidad,
    CAST(u.precio AS DECIMAL(12,2)) AS precio,
    v.fecha,
    v.estado,
    COALESCE(SUM(cu.monto), 0) AS total_pagado,
    COUNT(cu.id) AS cuotas
FROM ventas v
JOIN clientes c ON c.id = v.id_cliente
JOIN proyectos p ON p.id = v.id_proyecto
JOIN unidades u ON u.id = v.id_unidad
LEFT JOIN cuotas cu ON cu.id_venta = v.id
GROUP BY v.id, c.nombre, p.nombre, u.codigo, u.precio, v.fecha, v.estado
ORDER BY v.fecha DESC, v.id DESC
SQL;

        $statement = $this->pdo->query($sql);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClientesActivos(): array
    {
        $statement = $this->pdo->query("SELECT id, nombre, dni FROM clientes WHERE estado IN ('Cliente Activo', 'Cliente Potencial') ORDER BY nombre");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnidadesVendibles(): array
    {
        $sql = <<<'SQL'
SELECT
    u.id,
    u.codigo,
    p.nombre AS proyecto,
    u.tipo,
    CAST(u.precio AS DECIMAL(12,2)) AS precio,
    u.estado
FROM unidades u
JOIN proyectos p ON p.id = u.id_proyecto
WHERE u.activo = 1 AND u.estado IN ('Disponible', 'Separado')
ORDER BY p.nombre, u.codigo
SQL;

        $statement = $this->pdo->query($sql);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarVenta(array $datos): int
    {
        $clientId = (int) ($datos['cliente_id'] ?? 0);
        $unitId = (int) ($datos['unidad_id'] ?? 0);
        $montoTotal = (float) ($datos['monto_total'] ?? 0);
        $cuotasCount = (int) ($datos['cuotas'] ?? 12);
        $fecha = (string) ($datos['fecha'] ?? date('Y-m-d'));

        if ($clientId <= 0 || $unitId <= 0 || $montoTotal <= 0 || $cuotasCount <= 0) {
            throw new InvalidArgumentException('Cliente, unidad, monto y cuotas son obligatorios.');
        }

        $this->pdo->beginTransaction();
        try {
            $unit = $this->pdo->prepare('SELECT id_proyecto, precio, estado FROM unidades WHERE id = :id AND activo = 1 FOR UPDATE');
            $unit->execute(['id' => $unitId]);
            $row = $unit->fetch(PDO::FETCH_ASSOC);
            if ($row === false) {
                throw new InvalidArgumentException('La unidad seleccionada no existe o no está activa.');
            }
            if (in_array((string) ($row['estado'] ?? ''), ['Vendido', 'Vendida'], true)) {
                throw new InvalidArgumentException('La unidad ya fue vendida.');
            }

            $client = $this->pdo->prepare('SELECT id FROM clientes WHERE id = :id');
            $client->execute(['id' => $clientId]);
            if ($client->fetchColumn() === false) {
                throw new InvalidArgumentException('El cliente no existe.');
            }

            $insert = $this->pdo->prepare('INSERT INTO ventas (id_cliente, id_proyecto, id_unidad, estado, fecha) VALUES (:cliente_id, :project_id, :unit_id, :estado, :fecha)');
            $insert->execute([
                'cliente_id' => $clientId,
                'project_id' => (int) $row['id_proyecto'],
                'unit_id' => $unitId,
                'estado' => 'Vendido',
                'fecha' => $fecha,
            ]);
            $saleId = (int) $this->pdo->lastInsertId();

            $existente = $this->pdo->prepare('SELECT 1 FROM cliente_propiedad WHERE id_cliente = :cliente_id AND id_unidad = :unidad_id LIMIT 1');
            $existente->execute(['cliente_id' => $clientId, 'unidad_id' => $unitId]);
            if ($existente->fetchColumn() === false) {
                $propiedad = $this->pdo->prepare('INSERT INTO cliente_propiedad (id_cliente, id_unidad, estado) VALUES (:cliente_id, :unidad_id, :estado)');
                $propiedad->execute(['cliente_id' => $clientId, 'unidad_id' => $unitId, 'estado' => 'Vendido']);
            }

            $update = $this->pdo->prepare('UPDATE unidades SET estado = :estado WHERE id = :id');
            $update->execute(['estado' => 'Vendido', 'id' => $unitId]);

            $baseMonto = (float) $montoTotal;
            $montoCuota = round($baseMonto / $cuotasCount, 2);
            $totalGenerado = 0.0;
            for ($i = 1; $i <= $cuotasCount; $i++) {
                $montoCuotaActual = $i === $cuotasCount ? round($baseMonto - $totalGenerado, 2) : $montoCuota;
                $vencimiento = date('Y-m-d', strtotime("+$i months", strtotime($fecha)));
                $cuotaInsert = $this->pdo->prepare('INSERT INTO cuotas (id_cliente, id_venta, numero_cuota, monto, fecha_vencimiento, pagada) VALUES (:cliente_id, :venta_id, :numero_cuota, :monto, :fecha_vencimiento, 0)');
                $cuotaInsert->execute([
                    'cliente_id' => $clientId,
                    'venta_id' => $saleId,
                    'numero_cuota' => $i,
                    'monto' => $montoCuotaActual,
                    'fecha_vencimiento' => $vencimiento,
                ]);
                $totalGenerado += $montoCuotaActual;
            }

            $this->pdo->commit();
            return $saleId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
