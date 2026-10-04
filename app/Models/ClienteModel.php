<?php
declare(strict_types=1);

final class ClienteModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getClientes(?int $userId = null): array
    {
        $sql = <<<'SQL'
SELECT
    c.id,
    c.nombre AS name,
    c.dni,
    COALESCE(cp.telefono, '') AS phone,
    COALESCE(c.email, '') AS email,
    COALESCE(c.direccion, '') AS address,
    COALESCE(c.ocupacion, '') AS occupation,
    COALESCE(c.estado_civil, '') AS civil,
    c.estado AS status,
    COALESCE(a.nombre, 'Sin asesor') AS advisor,
    DATE_FORMAT(c.fecha_registro, '%b %Y') AS since,
    COALESCE(
        (
            SELECT cu.monto
            FROM cuotas AS cu
            WHERE cu.id_cliente = c.id
            ORDER BY cu.fecha_vencimiento ASC
            LIMIT 1
        ),
        0
    ) AS installment,
    COALESCE(
        (
            SELECT DATEDIFF(fecha_vencimiento, CURDATE())
            FROM cuotas AS cu
            WHERE cu.id_cliente = c.id
            ORDER BY fecha_vencimiento ASC
            LIMIT 1
        ),
        NULL
    ) AS dueInDays,
    COALESCE(
        (
            SELECT COUNT(*)
            FROM cuotas AS cu
            WHERE cu.id_cliente = c.id AND cu.pagada = 1
        ),
        0
    ) AS paidCount,
    COALESCE(
        (
            SELECT COUNT(*)
            FROM cuotas AS cu
            WHERE cu.id_cliente = c.id
        ),
        0
    ) AS totalCount
FROM clientes AS c
LEFT JOIN asesores AS a ON a.id = c.id_asesor
LEFT JOIN contactos AS cp ON cp.id_cliente = c.id AND cp.es_principal = 1
ORDER BY c.id DESC
SQL;

        try {
			if ($userId !== null) {
				$sql = str_replace('ORDER BY c.id DESC', 'WHERE a.id_usuario = :user_id ORDER BY c.id DESC', $sql);
			}
            $statement = $this->pdo->prepare($sql);
            $statement->execute($userId === null ? [] : ['user_id' => $userId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ClienteModel] ' . $exception->getMessage());
            return [];
        }

        $clients = [];
        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $clients[] = [
                'id' => $id,
                'name' => (string) ($row['name'] ?? ''),
                'dni' => (string) ($row['dni'] ?? ''),
                'phone' => (string) ($row['phone'] ?? ''),
                'email' => (string) ($row['email'] ?? ''),
                'address' => (string) ($row['address'] ?? ''),
                'occupation' => (string) ($row['occupation'] ?? ''),
                'civil' => (string) ($row['civil'] ?? ''),
                'status' => (string) ($row['status'] ?? 'Cliente Activo'),
                'advisor' => (string) ($row['advisor'] ?? 'Sin asesor'),
                'since' => (string) ($row['since'] ?? 'Mar 2025'),
                'installment' => (float) ($row['installment'] ?? 0),
                'dueInDays' => $row['dueInDays'] === null ? null : (int) $row['dueInDays'],
                'paidCount' => (int) ($row['paidCount'] ?? 0),
                'totalCount' => (int) ($row['totalCount'] ?? 0),
                'properties' => $this->getPropiedadesCliente($id),
                'contactos' => $this->getContactos($id),
            ];
        }

        return $clients;
    }

    public function getCliente(int $id, ?int $userId = null): ?array
    {
        $clientes = $this->getClientes($userId);
        foreach ($clientes as $cliente) {
            if ((int) $cliente['id'] === $id) {
                return $cliente;
            }
        }

        return null;
    }

    public function getDocumentos(int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT nombre, tipo_archivo AS type, tamano AS size
FROM documentos
WHERE id_cliente = :id_cliente
ORDER BY id DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([':id_cliente' => $clienteId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ClienteModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                (string) ($row['nombre'] ?? 'Contrato.pdf'),
                (string) ($row['type'] ?? 'file-signature'),
                (string) ($row['size'] ?? '2.4 MB'),
            ];
        }, $rows);
    }

    public function getContactos(int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT
    id_contacto AS id,
    tipo_contacto AS tipo,
    telefono AS valor,
    es_principal AS principal
FROM contactos
WHERE id_cliente = :id_cliente
ORDER BY es_principal DESC, id_contacto DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([':id_cliente' => $clienteId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ClienteModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'id' => (int) ($row['id'] ?? 0),
                'tipo' => (string) ($row['tipo'] ?? 'Celular'),
                'valor' => (string) ($row['valor'] ?? ''),
                'principal' => (bool) ($row['principal'] ?? false),
            ];
        }, $rows);
    }

    public function guardarContacto(array $payload): int
    {
        $clienteId = (int) ($payload['cliente_id'] ?? 0);
        $tipo = trim((string) ($payload['tipo_contacto'] ?? $payload['tipo'] ?? 'Celular'));
        $valor = trim((string) ($payload['telefono'] ?? $payload['valor'] ?? ''));
        $esPrincipal = !empty($payload['es_principal']) ? 1 : 0;

        if ($clienteId <= 0 || $tipo === '' || $valor === '') {
            throw new InvalidArgumentException('Faltan datos para guardar el contacto del cliente.');
        }

        $existing = $this->pdo->prepare('SELECT id_contacto FROM contactos WHERE id_cliente = :id_cliente AND telefono = :telefono LIMIT 1');
        $existing->execute([':id_cliente' => $clienteId, ':telefono' => $valor]);
        if (($existing->fetchColumn() !== false) && empty($payload['id_contacto'])) {
            throw new InvalidArgumentException('Ese contacto ya está registrado para este cliente.');
        }

        $contactoId = (int) ($payload['id_contacto'] ?? 0);
        if ($contactoId > 0) {
            $statement = $this->pdo->prepare('UPDATE contactos SET tipo_contacto = :tipo_contacto, telefono = :telefono, es_principal = :es_principal WHERE id_contacto = :id_contacto AND id_cliente = :id_cliente');
            $statement->execute([
                ':tipo_contacto' => $tipo,
                ':telefono' => $valor,
                ':es_principal' => $esPrincipal,
                ':id_contacto' => $contactoId,
                ':id_cliente' => $clienteId,
            ]);
            return $contactoId;
        }

        if ($esPrincipal === 1) {
            $this->pdo->prepare('UPDATE contactos SET es_principal = 0 WHERE id_cliente = :id_cliente')->execute([':id_cliente' => $clienteId]);
        }

        $statement = $this->pdo->prepare('INSERT INTO contactos (id_cliente, tipo_contacto, telefono, es_principal) VALUES (:id_cliente, :tipo_contacto, :telefono, :es_principal)');
        $statement->execute([
            ':id_cliente' => $clienteId,
            ':tipo_contacto' => $tipo,
            ':telefono' => $valor,
            ':es_principal' => $esPrincipal,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function getPropiedadesCliente(int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT
    u.codigo AS code,
    p.nombre AS project,
    u.tipo AS type,
    CAST(u.area_m2 AS DECIMAL(10,2)) AS area,
    CAST(u.precio AS DECIMAL(12,2)) AS price,
    cp.estado AS status
FROM cliente_propiedad AS cp
LEFT JOIN unidades AS u ON u.id = cp.id_unidad
LEFT JOIN proyectos AS p ON p.id = u.id_proyecto
WHERE cp.id_cliente = :cliente_id
ORDER BY cp.id DESC
SQL;

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute([':cliente_id' => $clienteId]);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            error_log('[ClienteModel] ' . $exception->getMessage());
            return [];
        }

        return array_map(static function (array $row): array {
            return [
                'code' => (string) ($row['code'] ?? ''),
                'project' => (string) ($row['project'] ?? ''),
                'type' => (string) ($row['type'] ?? 'Dpto.'),
                'area' => (float) ($row['area'] ?? 0),
                'price' => (float) ($row['price'] ?? 0),
                'status' => (string) ($row['status'] ?? 'Disponible'),
            ];
        }, $rows);
    }
}
