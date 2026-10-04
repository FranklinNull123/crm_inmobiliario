<?php
require __DIR__ . '/../app/bootstrap.php';
$pdo = require __DIR__ . '/../app/Config/conexion.php';
require __DIR__ . '/../app/Models/SeparacionModel.php';

$clientId = (int) $pdo->query('SELECT id FROM clientes ORDER BY id LIMIT 1')->fetchColumn();
$unitId = (int) $pdo->query("SELECT id FROM unidades WHERE activo = 1 AND estado = 'Disponible' ORDER BY id LIMIT 1")->fetchColumn();

if ($clientId <= 0 || $unitId <= 0) {
    fwrite(STDOUT, "NO_DATA\n");
    exit(0);
}

$model = new SeparacionModel($pdo);
$payload = [
    'id_cliente' => $clientId,
    'id_unidad' => $unitId,
    'monto' => 100,
    'metodo_pago' => 'Transferencia bancaria',
    'fecha_vencimiento' => date('Y-m-d', strtotime('+7 days')),
    'observaciones' => 'test duplicate repro',
];

try {
    $first = $model->registrar($payload);
    $second = $model->registrar($payload);
    $count = (int) $pdo->query('SELECT COUNT(*) FROM separaciones WHERE id_unidad = ' . $unitId)->fetchColumn();
    fwrite(STDOUT, "FIRST={$first} SECOND={$second} COUNT={$count}\n");
} catch (Throwable $exception) {
    fwrite(STDOUT, "EXCEPTION={$exception->getMessage()}\n");
}
