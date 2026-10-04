-- Estructura operativa de proyectos, etapas y unidades.
-- Ejecutar una sola vez después de las migraciones anteriores.
USE `cms_inmobiliario`;

ALTER TABLE `proyectos`
  ADD COLUMN `descripcion` TEXT NULL AFTER `ubicacion`,
  ADD COLUMN `fecha_inicio` DATE NULL AFTER `descripcion`,
  ADD COLUMN `fecha_entrega` DATE NULL AFTER `fecha_inicio`,
  ADD COLUMN `estado` VARCHAR(40) NOT NULL DEFAULT 'En planificación' AFTER `fecha_entrega`,
  ADD COLUMN `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

CREATE TABLE `etapas_proyecto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_proyecto` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `orden` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_etapa_proyecto_nombre` (`id_proyecto`, `nombre`),
  KEY `idx_etapa_proyecto_orden` (`id_proyecto`, `activo`, `orden`),
  CONSTRAINT `fk_etapas_proyecto_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `unidades`
  ADD COLUMN `id_etapa` INT UNSIGNED NULL AFTER `id_proyecto`,
  ADD COLUMN `activo` TINYINT(1) NOT NULL DEFAULT 1 AFTER `estado`,
  ADD COLUMN `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD KEY `idx_unidades_proyecto_estado` (`id_proyecto`, `estado`, `activo`),
  ADD KEY `idx_unidades_etapa` (`id_etapa`),
  ADD CONSTRAINT `fk_unidades_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_unidades_etapa` FOREIGN KEY (`id_etapa`) REFERENCES `etapas_proyecto` (`id`) ON DELETE SET NULL;

INSERT IGNORE INTO `catalogo_valores` (`categoria`, `codigo`, `etiqueta`, `orden`) VALUES
('estado_proyecto', 'en_planificacion', CONVERT(UNHEX('456e20706c616e69666963616369c3b36e') USING utf8mb4), 1),
('estado_proyecto', 'En preventa', 'En preventa', 2),
('estado_proyecto', 'en_construccion', CONVERT(UNHEX('456e20636f6e73747275636369c3b36e') USING utf8mb4), 3),
('estado_proyecto', 'Entregado', 'Entregado', 4),
('estado_proyecto', 'Pausado', 'Pausado', 5),
('estado_proyecto', 'Archivado', 'Archivado', 6),
('estado_unidad', 'Disponible', 'Disponible', 1),
('estado_unidad', 'Reservado', 'Reservado', 2),
('estado_unidad', 'Separado', 'Separado', 3),
('estado_unidad', 'Vendido', 'Vendido', 4),
('estado_unidad', 'Bloqueado', 'Bloqueado', 5);

INSERT IGNORE INTO `etapas_proyecto` (`id_proyecto`, `nombre`, `orden`)
SELECT p.id, etapas.nombre, etapas.orden
FROM `proyectos` p
CROSS JOIN (
  SELECT 'Lanzamiento' AS nombre, 1 AS orden UNION ALL
  SELECT CONVERT(UNHEX('436f6d65726369616c697a616369c3b36e') USING utf8mb4), 2 UNION ALL
  SELECT CONVERT(UNHEX('436f6e73747275636369c3b36e') USING utf8mb4), 3 UNION ALL
  SELECT 'Entrega', 4
) AS etapas;

UPDATE `unidades` u
JOIN `etapas_proyecto` e ON e.id_proyecto = u.id_proyecto AND e.nombre = 'Comercialización'
SET u.id_etapa = e.id
WHERE u.id_etapa IS NULL;