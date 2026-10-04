USE `cms_inmobiliario`;

CREATE TABLE IF NOT EXISTS `recordatorios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(120) NOT NULL,
  `descripcion` TEXT NULL,
  `tipo` VARCHAR(40) NOT NULL DEFAULT 'General',
  `prioridad` VARCHAR(20) NOT NULL DEFAULT 'media',
  `fecha_programada` DATE NOT NULL,
  `estado` VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recordatorios_fecha` (`fecha_programada`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `recordatorios` (`titulo`, `descripcion`, `tipo`, `prioridad`, `fecha_programada`, `estado`)
VALUES
('Seguimiento de lead premium', 'Llamar al cliente para confirmar visita del proyecto.', 'Lead', 'alta', DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'pendiente'),
('Confirmar firma de contrato', 'Enviar documento final y coordinar firma con asesor.', 'Cliente', 'alta', DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'pendiente'),
('Actualizar inventario', 'Revisar disponibilidad de unidades prioritarias.', 'General', 'media', DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'pendiente')
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo);
