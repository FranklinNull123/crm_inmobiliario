CREATE DATABASE IF NOT EXISTS `crm_inmobiliario` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `crm_inmobiliario`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `documentos`;
DROP TABLE IF EXISTS `cuotas`;
DROP TABLE IF EXISTS `ventas`;
DROP TABLE IF EXISTS `cliente_propiedad`;
DROP TABLE IF EXISTS `separaciones`;
DROP TABLE IF EXISTS `unidades`;
DROP TABLE IF EXISTS `proyectos`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `clientes_leads`;
DROP TABLE IF EXISTS `campanas`;
DROP TABLE IF EXISTS `canales`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `asesores`;
DROP TABLE IF EXISTS `metas_ventas`;
DROP TABLE IF EXISTS `leads`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `asesores` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuarios` (
  `id_usuario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombres` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `avatar_url` TEXT DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `canales` (
  `id_canal` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id_canal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `campanas` (
  `id_campana` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_canal` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id_campana`),
  KEY `id_canal` (`id_canal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes_leads` (
  `id_cliente` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_asesor` INT UNSIGNED DEFAULT NULL,
  `id_campana` INT UNSIGNED DEFAULT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `estado_lead` VARCHAR(50) NOT NULL DEFAULT 'Nuevo',
  PRIMARY KEY (`id_cliente`),
  KEY `id_asesor` (`id_asesor`),
  KEY `id_campana` (`id_campana`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leads` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `proyecto_interes` VARCHAR(120) DEFAULT NULL,
  `nivel_interes` VARCHAR(20) DEFAULT 'Bajo',
  `etapa` VARCHAR(50) NOT NULL DEFAULT 'Nuevo Lead',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `proyectos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `ubicacion` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `unidades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_proyecto` INT UNSIGNED NOT NULL,
  `codigo` VARCHAR(30) NOT NULL,
  `tipo` VARCHAR(30) NOT NULL,
  `area_m2` DECIMAL(10,2) NOT NULL,
  `precio` DECIMAL(12,2) NOT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_unique` (`codigo`),
  KEY `id_proyecto` (`id_proyecto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_asesor` INT UNSIGNED DEFAULT NULL,
  `nombre` VARCHAR(120) NOT NULL,
  `dni` VARCHAR(20) NOT NULL,
  `email` VARCHAR(120) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `ocupacion` VARCHAR(100) DEFAULT NULL,
  `estado_civil` VARCHAR(50) DEFAULT NULL,
  `estado` VARCHAR(40) NOT NULL DEFAULT 'Cliente Activo',
  `fecha_registro` DATE NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni_unique` (`dni`),
  KEY `id_asesor` (`id_asesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contactos` (
  `id_contacto` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `tipo_contacto` VARCHAR(30) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `es_principal` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_contacto`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cliente_propiedad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `id_unidad` INT UNSIGNED NOT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ventas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `id_proyecto` INT UNSIGNED NOT NULL,
  `id_unidad` INT UNSIGNED NOT NULL,
  `estado` VARCHAR(50) NOT NULL DEFAULT 'Vendido',
  `fecha` DATE NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_proyecto` (`id_proyecto`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cuotas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `id_venta` INT UNSIGNED DEFAULT NULL,
  `numero_cuota` INT UNSIGNED NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `pagada` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_venta` (`id_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `separaciones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `id_unidad` INT UNSIGNED NOT NULL,
  `monto` DECIMAL(12,2) NOT NULL,
  `metodo_pago` VARCHAR(40) NOT NULL DEFAULT 'Contado',
  `fecha_vencimiento` DATE NOT NULL,
  `observaciones` TEXT DEFAULT NULL,
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documentos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` INT UNSIGNED NOT NULL,
  `nombre` VARCHAR(180) NOT NULL,
  `tipo_archivo` VARCHAR(30) NOT NULL DEFAULT 'file-signature',
  `tamano` VARCHAR(30) NOT NULL DEFAULT '2.4 MB',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `metas_ventas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mes` VARCHAR(20) NOT NULL,
  `orden` TINYINT UNSIGNED NOT NULL,
  `venta` DECIMAL(10,2) NOT NULL,
  `meta` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `canales` (`id_canal`, `nombre`) VALUES
(1, 'WhatsApp'),
(2, 'Llamada');

INSERT INTO `campanas` (`id_campana`, `id_canal`, `nombre`) VALUES
(1, 1, 'Campaña Verano 2026'),
(2, 1, 'Lanzamiento Exclusivo'),
(3, 2, 'Feria Inmobiliaria');

INSERT INTO `usuarios` (`id_usuario`, `nombres`, `email`, `estado`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', 1),
(2, 'Carlos Ramírez', 'carlos@inmobiliaria.com', 1),
(3, 'Luis Torres', 'luis@inmobiliaria.com', 1);

INSERT INTO `asesores` (`id`, `nombre`, `email`, `estado`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', 1),
(2, 'Carlos Ramírez', 'carlos@inmobiliaria.com', 1),
(3, 'Luis Torres', 'luis@inmobiliaria.com', 1);

INSERT INTO `clientes_leads` (`id_cliente`, `id_asesor`, `id_campana`, `nombres`, `estado_lead`) VALUES
(1, 1, 1, 'Andrea Quispe', 'Contactado'),
(2, 2, 2, 'Carlos Huamán', 'Nuevo'),
(3, 3, 3, 'Miguel Fernández', 'Visita Realizada');

INSERT INTO `leads` (`id`, `nombre`, `proyecto_interes`, `nivel_interes`, `etapa`) VALUES
(1, 'Andrea Quispe', 'Condominio Las Palmas', 'Alto', 'Nuevo Lead'),
(2, 'Carlos Huamán', 'Lotes Villa Sol', 'Bajo', 'Contactado'),
(3, 'Miguel Fernández', 'Residencial Los Álamos', 'Medio', 'Visita Realizada'),
(4, 'Diego Chávez', 'Condominio Mirador', 'Alto', 'Negociación'),
(5, 'Gabriela Núñez', 'Residencial Los Álamos', 'Alto', 'Venta Cerrada');

INSERT INTO `proyectos` (`id`, `nombre`, `ubicacion`) VALUES
(1, 'Lotes Villa Sol', 'San Isidro'),
(2, 'Torres del Parque', 'Surco'),
(3, 'Condominio Las Palmas', 'San Miguel'),
(4, 'Residencial Los Álamos', 'La Molina');

INSERT INTO `unidades` (`id`, `id_proyecto`, `codigo`, `tipo`, `area_m2`, `precio`, `estado`) VALUES
(1, 1, 'LVS-A-024', 'Lote', 160.00, 89500.00, 'Disponible'),
(2, 2, 'TDP-B048', 'Dpto.', 92.50, 385000.00, 'Reservado'),
(3, 3, 'CLP-1102', 'Dpto.', 74.00, 298000.00, 'Separado'),
(4, 1, 'LVS-C-011', 'Lote', 200.00, 112000.00, 'Vendido'),
(5, 4, 'RLA-204', 'Dpto.', 81.40, 330000.00, 'Disponible');

INSERT INTO `clientes` (`id`, `id_asesor`, `nombre`, `dni`, `email`, `telefono`, `direccion`, `ocupacion`, `estado_civil`, `estado`, `fecha_registro`) VALUES
(1, 1, 'María López', '40123456', 'maria.lopez@gmail.com', '+51 999 111 222', 'Av. Brasil 123', 'Profesional', 'Soltera', 'Cliente Activo', '2025-03-15'),
(2, 2, 'Pedro Ramírez', '40876543', 'pedro.ramirez@gmail.com', '+51 999 333 444', 'Jr. Puno 456', 'Empresario', 'Casado', 'En Mora', '2024-11-20'),
(3, 3, 'Carmen Vásquez', '45123456', 'carmen.vasquez@gmail.com', '+51 999 555 666', 'Av. Benavides 789', 'Gerente', 'Divorciada', 'Cliente Activo', '2025-01-10');

INSERT INTO `contactos` (`id_contacto`, `id_cliente`, `tipo_contacto`, `telefono`, `es_principal`) VALUES
(1, 1, 'teléfono', '+51 999 111 222', 1),
(2, 2, 'teléfono', '+51 999 333 444', 1),
(3, 3, 'teléfono', '+51 999 555 666', 1);

INSERT INTO `cliente_propiedad` (`id`, `id_cliente`, `id_unidad`, `estado`) VALUES
(1, 1, 1, 'Disponible'),
(2, 2, 2, 'Reservado'),
(3, 3, 5, 'Disponible');

INSERT INTO `ventas` (`id`, `id_cliente`, `id_proyecto`, `id_unidad`, `estado`, `fecha`) VALUES
(1, 1, 1, 1, 'Vendido', '2026-03-10'),
(2, 2, 2, 2, 'Separado', '2026-04-12'),
(3, 3, 4, 5, 'Vendido', '2026-06-06');

INSERT INTO `cuotas` (`id`, `id_cliente`, `id_venta`, `numero_cuota`, `monto`, `fecha_vencimiento`, `pagada`) VALUES
(1, 1, 1, 1, 4750.00, DATE_ADD(CURDATE(), INTERVAL 3 DAY), 1),
(2, 1, 1, 2, 4750.00, DATE_ADD(CURDATE(), INTERVAL 15 DAY), 0),
(3, 2, 2, 1, 6800.00, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 0),
(4, 2, 2, 2, 6800.00, DATE_ADD(CURDATE(), INTERVAL 25 DAY), 0),
(5, 3, 3, 1, 5200.00, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 1),
(6, 3, 3, 2, 5200.00, DATE_ADD(CURDATE(), INTERVAL 40 DAY), 0);

INSERT INTO `documentos` (`id`, `id_cliente`, `nombre`, `tipo_archivo`, `tamano`) VALUES
(1, 1, 'Contrato.pdf', 'file-signature', '2.4 MB'),
(2, 1, 'Copia DNI.jpg', 'file-image', '1.1 MB'),
(3, 2, 'Ficha de pago.pdf', 'file-signature', '3.2 MB');

INSERT INTO `metas_ventas` (`id`, `mes`, `orden`, `venta`, `meta`) VALUES
(1, 'Abr', 1, 1.8, 2.0),
(2, 'May', 2, 2.3, 2.5),
(3, 'Jun', 3, 2.8, 2.9),
(4, 'Jul', 4, 2.4, 2.7),
(5, 'Ago', 5, 3.1, 3.0),
(6, 'Set', 6, 3.4, 3.2);

INSERT INTO `separaciones` (`id`, `id_cliente`, `id_unidad`, `monto`, `metodo_pago`, `fecha_vencimiento`, `observaciones`, `creado_en`) VALUES
(1, 2, 2, 385000.00, 'Cuotas', DATE_ADD(CURDATE(), INTERVAL 20 DAY), 'Separación de departamento con pago inicial', NOW());
