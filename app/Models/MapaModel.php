<?php
declare(strict_types=1);

final class MapaModel
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function getProjects(): array
    {
        return $this->pdo->query(
            "SELECT id, nombre AS name, ubigeo_distrito AS district, latitud AS latitude, longitud AS longitude "
            . "FROM proyectos WHERE estado <> 'Archivado' ORDER BY nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLeads(?int $userId = null): array
    {
        $sql = "SELECT id, nombre AS name, proyecto_interes AS project, etapa AS status, ubigeo_distrito AS district, "
            . "latitud AS latitude, longitud AS longitude FROM leads WHERE id_cliente_convertido IS NULL";
        $params = [];
        if ($userId !== null) {
            $sql .= ' AND id_usuario_asignado = :user_id';
            $params['user_id'] = $userId;
        }
        $sql .= ' ORDER BY nombre';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMapData(?int $userId = null): array
    {
        $projects = $this->pdo->query(
            "SELECT p.id, p.nombre AS name, p.ubicacion AS address, p.ubigeo_distrito AS district, "
            . "p.latitud AS latitude, p.longitud AS longitude, p.estado AS status, "
            . "COUNT(u.id) AS units_total, "
            . "SUM(CASE WHEN u.estado = 'Disponible' THEN 1 ELSE 0 END) AS units_available "
            . 'FROM proyectos p LEFT JOIN unidades u ON u.id_proyecto = p.id AND u.activo = 1 '
            . "WHERE p.estado <> 'Archivado' GROUP BY p.id ORDER BY p.nombre"
        )->fetchAll(PDO::FETCH_ASSOC);

        $salesSql = 'SELECT v.id, v.fecha AS date, v.estado AS status, c.nombre AS client, u.codigo AS unit, '
            . 'p.nombre AS project, p.ubigeo_distrito AS district, p.latitud AS latitude, p.longitud AS longitude '
            . 'FROM ventas v JOIN clientes c ON c.id = v.id_cliente JOIN unidades u ON u.id = v.id_unidad '
            . 'JOIN proyectos p ON p.id = v.id_proyecto LEFT JOIN asesores a ON a.id = c.id_asesor';
        $salesParams = [];
        if ($userId !== null) {
            $salesSql .= ' WHERE a.id_usuario = :user_id';
            $salesParams['user_id'] = $userId;
        }
        $salesSql .= ' ORDER BY v.fecha DESC, v.id DESC';
        $salesStatement = $this->pdo->prepare($salesSql);
        $salesStatement->execute($salesParams);
        $sales = $salesStatement->fetchAll(PDO::FETCH_ASSOC);

        $leads = $this->getLeads($userId);
        return ['projects' => $projects, 'sales' => $sales, 'leads' => $leads];
    }

    public function saveLocation(string $type, int $id, string $district, ?float $latitude, ?float $longitude, ?int $userId = null): void
    {
        $geoFile = dirname(__DIR__, 2) . '/public/data/ubigeo/districts.json';
        $contents = file_get_contents($geoFile);
        if ($contents === false) {
            throw new RuntimeException('No se pudo cargar el catálogo geográfico del Perú.');
        }
        $districts = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        $validDistrict = false;
        foreach ($districts as $item) {
            if (($item['id'] ?? null) === $district) {
                $validDistrict = true;
                break;
            }
        }
        if (!$validDistrict) {
            throw new InvalidArgumentException('Selecciona un distrito válido del Perú.');
        }
        if (($latitude === null) !== ($longitude === null)) {
            throw new InvalidArgumentException('Selecciona un punto en el mapa o limpia ambas coordenadas.');
        }
        if ($latitude !== null && ($latitude < -18.5 || $latitude > 0.5 || $longitude < -82.5 || $longitude > -68.0)) {
            throw new InvalidArgumentException('El punto debe estar dentro del territorio peruano.');
        }

        if ($type === 'project') {
            $statement = $this->pdo->prepare('UPDATE proyectos SET ubigeo_distrito = :district, latitud = :latitude, longitud = :longitude WHERE id = :id');
            $statement->execute(['district' => $district, 'latitude' => $latitude, 'longitude' => $longitude, 'id' => $id]);
        } elseif ($type === 'lead') {
            $sql = 'UPDATE leads SET ubigeo_distrito = :district, latitud = :latitude, longitud = :longitude WHERE id = :id AND id_cliente_convertido IS NULL';
            $params = ['district' => $district, 'latitude' => $latitude, 'longitude' => $longitude, 'id' => $id];
            if ($userId !== null) {
                $sql .= ' AND id_usuario_asignado = :user_id';
                $params['user_id'] = $userId;
            }
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);
        } else {
            throw new InvalidArgumentException('El tipo de ubicación no es válido.');
        }

        if ($statement->rowCount() === 0) {
            $check = $this->pdo->prepare($type === 'project'
                ? 'SELECT 1 FROM proyectos WHERE id = :id'
                : 'SELECT 1 FROM leads WHERE id = :id AND id_cliente_convertido IS NULL' . ($userId === null ? '' : ' AND id_usuario_asignado = :user_id'));
            $checkParams = ['id' => $id];
            if ($type === 'lead' && $userId !== null) {
                $checkParams['user_id'] = $userId;
            }
            $check->execute($checkParams);
            if ($check->fetchColumn() === false) {
                throw new InvalidArgumentException('No se encontró el registro o no tienes acceso.');
            }
        }
    }
}
