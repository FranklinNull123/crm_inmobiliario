<?php
declare(strict_types=1);

require_once __DIR__ . '/../Models/MapaModel.php';

final class MapaController
{
    public function __construct(private readonly MapaModel $mapaModel)
    {
    }

    public function index(?int $userId = null): array
    {
        $basePath = dirname(__DIR__, 2) . '/public/data/ubigeo/';
        $decode = static function (string $file) use ($basePath): array {
            $contents = file_get_contents($basePath . $file);
            if ($contents === false) {
                throw new RuntimeException('No se pudo cargar el catálogo geográfico del Perú.');
            }
            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        };

        return [
            'departments' => $decode('departments.json'),
            'provinces' => $decode('provinces.json'),
            'districts' => $decode('districts.json'),
            'projects' => $this->mapaModel->getProjects(),
            'leads' => $this->mapaModel->getLeads($userId),
            'mapData' => $this->mapaModel->getMapData($userId),
        ];
    }

    public function saveLocation(array $input, ?int $userId = null): void
    {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $type = $input['type'] ?? null;
        $district = $input['district'] ?? null;
        if ($id === false || !is_string($type) || !is_string($district)) {
            throw new InvalidArgumentException('Completa el tipo, registro y distrito de la ubicación.');
        }

        $latitude = $this->coordinate($input['latitude'] ?? null, 'latitud');
        $longitude = $this->coordinate($input['longitude'] ?? null, 'longitud');
        $this->mapaModel->saveLocation($type, $id, $district, $latitude, $longitude, $userId);
    }

    private function coordinate(mixed $value, string $name): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('La ' . $name . ' no es válida.');
        }
        return (float) $value;
    }
}
