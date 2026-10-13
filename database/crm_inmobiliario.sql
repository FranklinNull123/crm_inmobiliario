-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 13-10-2026 a las 00:11:06
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `crm_inmobiliario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asesores`
--

DROP TABLE IF EXISTS `asesores`;
CREATE TABLE IF NOT EXISTS `asesores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_usuario` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_asesores_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asesores`
--

INSERT INTO `asesores` (`id`, `nombre`, `email`, `estado`, `id_usuario`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', 1, 1),
(2, 'Carlos Ramírez', 'carlos@inmobiliaria.com', 1, 2),
(3, 'Luis Torres', 'luis@inmobiliaria.com', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` int UNSIGNED DEFAULT NULL,
  `evento` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_id` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dispositivo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_auditoria_usuario_fecha` (`id_usuario`,`creado_en`),
  KEY `idx_auditoria_entidad` (`entidad`,`entidad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `id_usuario`, `evento`, `entidad`, `entidad_id`, `datos_anteriores`, `datos_nuevos`, `ip`, `dispositivo`, `creado_en`) VALUES
(1, NULL, 'superadmin_inicial_creado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 16:59:49'),
(2, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 17:00:18'),
(3, NULL, 'login_fallido', 'usuarios', NULL, NULL, '{\"email\":\"admin@sistema.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:18:22'),
(4, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:18:41'),
(5, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:19:19'),
(6, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:19:51'),
(7, 4, 'proyecto_creado', 'proyectos', '8', NULL, '{\"id\":8,\"name\":\"ESPERANZA SATIPO\",\"location\":\"MARGARITAS\",\"description\":\"Venta de terreno\",\"startDate\":\"2026-10-06\",\"deliveryDate\":\"2026-10-31\",\"status\":\"En preventa\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:30:32'),
(8, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:23'),
(9, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:25'),
(10, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:32'),
(11, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:45'),
(12, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":false}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:55'),
(13, 4, 'usuario_estado_actualizado', 'usuarios', '3', NULL, '{\"activo\":true}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:32:59'),
(14, 4, 'cuota_cobrada', 'cuotas', '3', NULL, '{\"cuota_id\":3}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:33:42'),
(15, 4, 'cuota_cobrada', 'cuotas', '2', NULL, '{\"cuota_id\":2}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:33:48'),
(16, 4, 'contrato_creado', 'documentos', '4', NULL, '{\"cliente_id\":3,\"nombre\":\"Taladro El\\u00e9ctrico\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:34:54'),
(17, 4, 'lead_estado_actualizado', 'leads', '2', '{\"estado\":\"Contactado\"}', '{\"estado\":\"Nuevo\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:36:31'),
(18, 4, 'lead_estado_actualizado', 'leads', '1', '{\"estado\":\"Nuevo Lead\"}', '{\"estado\":\"Contactado\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:36:34'),
(19, 4, 'documento_creado', 'documentos', '5', NULL, '{\"cliente_id\":3,\"nombre\":\"Contrato.pdf\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-06 18:46:42'),
(20, NULL, 'login_fallido', 'usuarios', NULL, NULL, '{\"email\":\"anderson@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:39:24'),
(21, NULL, 'login_fallido', 'usuarios', NULL, NULL, '{\"email\":\"anderson@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:39:32'),
(22, 4, 'login_fallido', 'usuarios', '4', NULL, '{\"intentos\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:42:32'),
(23, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:42:45'),
(24, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:50:25'),
(25, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:54:20'),
(26, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', '2026-10-12 17:58:03'),
(27, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:58:58'),
(28, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:59:12'),
(29, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 17:59:49'),
(30, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', '2026-10-12 18:02:32'),
(31, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', '2026-10-12 18:06:09'),
(32, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', '2026-10-12 18:11:50'),
(33, 4, 'perfil_actualizado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 18:13:47'),
(34, 4, 'lead_estado_actualizado', 'leads', '2', '{\"estado\":\"Nuevo\"}', '{\"estado\":\"Contactado\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 18:59:45'),
(35, 4, 'lead_estado_actualizado', 'leads', '2', '{\"estado\":\"Contactado\"}', '{\"estado\":\"Nuevo\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 18:59:47'),
(36, 4, 'lead_estado_actualizado', 'leads', '1', '{\"estado\":\"Contactado\"}', '{\"estado\":\"Nuevo\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 18:59:48'),
(37, 4, 'lead_estado_actualizado', 'leads', '2', '{\"estado\":\"Nuevo\"}', '{\"estado\":\"Contactado\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-12 18:59:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanas`
--

DROP TABLE IF EXISTS `campanas`;
CREATE TABLE IF NOT EXISTS `campanas` (
  `id_campana` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_canal` int UNSIGNED NOT NULL,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_campana`),
  KEY `id_canal` (`id_canal`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `campanas`
--

INSERT INTO `campanas` (`id_campana`, `id_canal`, `nombre`) VALUES
(1, 1, 'Campaña Verano 2026'),
(2, 1, 'Lanzamiento Exclusivo'),
(3, 2, 'Feria Inmobiliaria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canales`
--

DROP TABLE IF EXISTS `canales`;
CREATE TABLE IF NOT EXISTS `canales` (
  `id_canal` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_canal`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `canales`
--

INSERT INTO `canales` (`id_canal`, `nombre`) VALUES
(1, 'WhatsApp'),
(2, 'Llamada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_valores`
--

DROP TABLE IF EXISTS `catalogo_valores`;
CREATE TABLE IF NOT EXISTS `catalogo_valores` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoria` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etiqueta` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` smallint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_catalogo_categoria_codigo` (`categoria`,`codigo`),
  KEY `idx_catalogo_categoria_activo` (`categoria`,`activo`,`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `catalogo_valores`
--

INSERT INTO `catalogo_valores` (`id`, `categoria`, `codigo`, `etiqueta`, `activo`, `orden`) VALUES
(1, 'moneda', 'PEN', 'Sol peruano (S/)', 1, 1),
(2, 'medio_pago', 'transferencia', 'Transferencia bancaria', 1, 1),
(3, 'medio_pago', 'deposito', 'Depósito en cuenta', 1, 2),
(4, 'medio_pago', 'efectivo', 'Efectivo', 1, 3),
(5, 'medio_pago', 'tarjeta', 'Tarjeta de crédito/débito', 1, 4),
(6, 'medio_pago', 'yape_plin', 'Yape / Plin', 1, 5),
(7, 'estado_lead', 'nuevo', 'Nuevo', 1, 1),
(8, 'estado_lead', 'contactado', 'Contactado', 1, 2),
(9, 'estado_lead', 'visita', 'Visita realizada', 1, 3),
(10, 'estado_lead', 'negociacion', 'Negociación', 1, 4),
(11, 'estado_lead', 'cerrado', 'Venta cerrada', 1, 5),
(12, 'canal', 'whatsapp', 'WhatsApp', 1, 1),
(13, 'canal', 'llamada', 'Llamada', 1, 2),
(14, 'canal', 'web', 'Sitio web', 1, 3),
(15, 'canal', 'referido', 'Referido', 1, 4),
(16, 'estado_proyecto', 'en_planificacion', 'En planificación', 1, 1),
(17, 'estado_proyecto', 'En preventa', 'En preventa', 1, 2),
(18, 'estado_proyecto', 'en_construccion', 'En construcción', 1, 3),
(19, 'estado_proyecto', 'Entregado', 'Entregado', 1, 4),
(20, 'estado_proyecto', 'Pausado', 'Pausado', 1, 5),
(21, 'estado_proyecto', 'Archivado', 'Archivado', 1, 6),
(22, 'estado_unidad', 'Disponible', 'Disponible', 1, 1),
(23, 'estado_unidad', 'Reservado', 'Reservado', 1, 2),
(24, 'estado_unidad', 'Separado', 'Separado', 1, 3),
(25, 'estado_unidad', 'Vendido', 'Vendido', 1, 4),
(26, 'estado_unidad', 'Bloqueado', 'Bloqueado', 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_asesor` int UNSIGNED DEFAULT NULL,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ocupacion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cliente Activo',
  `fecha_registro` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni_unique` (`dni`),
  KEY `id_asesor` (`id_asesor`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `id_asesor`, `nombre`, `dni`, `email`, `telefono`, `direccion`, `ocupacion`, `estado_civil`, `estado`, `fecha_registro`) VALUES
(1, 1, 'María López', '40123456', 'maria.lopez@gmail.com', '+51 999 111 222', 'Av. Brasil 123', 'Profesional', 'Soltera', 'Cliente Activo', '2025-03-15'),
(2, 2, 'Pedro Ramírez', '40876543', 'pedro.ramirez@gmail.com', '+51 999 333 444', 'Jr. Puno 456', 'Empresario', 'Casado', 'En Mora', '2024-11-20'),
(3, 3, 'Carmen Vásquez', '45123456', 'carmen.vasquez@gmail.com', '+51 999 555 666', 'Av. Benavides 789', 'Gerente', 'Divorciada', 'Cliente Activo', '2025-01-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_leads`
--

DROP TABLE IF EXISTS `clientes_leads`;
CREATE TABLE IF NOT EXISTS `clientes_leads` (
  `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_asesor` int UNSIGNED DEFAULT NULL,
  `id_campana` int UNSIGNED DEFAULT NULL,
  `nombres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_lead` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nuevo',
  PRIMARY KEY (`id_cliente`),
  KEY `id_asesor` (`id_asesor`),
  KEY `id_campana` (`id_campana`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes_leads`
--

INSERT INTO `clientes_leads` (`id_cliente`, `id_asesor`, `id_campana`, `nombres`, `estado_lead`) VALUES
(1, 1, 1, 'Andrea Quispe', 'Contactado'),
(2, 2, 2, 'Carlos Huamán', 'Nuevo'),
(3, 3, 3, 'Miguel Fernández', 'Visita Realizada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_propiedad`
--

DROP TABLE IF EXISTS `cliente_propiedad`;
CREATE TABLE IF NOT EXISTS `cliente_propiedad` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_unidad` int UNSIGNED NOT NULL,
  `estado` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente_propiedad`
--

INSERT INTO `cliente_propiedad` (`id`, `id_cliente`, `id_unidad`, `estado`) VALUES
(1, 1, 1, 'Disponible'),
(2, 2, 2, 'Reservado'),
(3, 3, 5, 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

DROP TABLE IF EXISTS `configuracion_sistema`;
CREATE TABLE IF NOT EXISTS `configuracion_sistema` (
  `clave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `actualizado_por` int UNSIGNED DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`),
  KEY `fk_configuracion_usuario` (`actualizado_por`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`clave`, `valor`, `actualizado_por`, `actualizado_en`) VALUES
('empresa_nombre', 'Inmobi CMS', NULL, '2026-09-30 16:46:45'),
('moneda_principal', 'PEN', NULL, '2026-09-30 16:46:45'),
('zona_horaria', 'America/Lima', NULL, '2026-09-30 16:46:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

DROP TABLE IF EXISTS `contactos`;
CREATE TABLE IF NOT EXISTS `contactos` (
  `id_contacto` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `tipo_contacto` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_contacto`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id_contacto`, `id_cliente`, `tipo_contacto`, `telefono`, `es_principal`) VALUES
(1, 1, 'tel??fono', '+51 999 111 222', 1),
(2, 2, 'tel??fono', '+51 999 333 444', 1),
(3, 3, 'tel??fono', '+51 999 555 666', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuotas`
--

DROP TABLE IF EXISTS `cuotas`;
CREATE TABLE IF NOT EXISTS `cuotas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_venta` int UNSIGNED DEFAULT NULL,
  `numero_cuota` int UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_venta` (`id_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuotas`
--

INSERT INTO `cuotas` (`id`, `id_cliente`, `id_venta`, `numero_cuota`, `monto`, `fecha_vencimiento`, `pagada`) VALUES
(1, 1, 1, 1, 4750.00, '2026-10-03', 1),
(2, 1, 1, 2, 4750.00, '2026-10-15', 1),
(3, 2, 2, 1, 6800.00, '2026-09-28', 1),
(4, 2, 2, 2, 6800.00, '2026-10-25', 0),
(5, 3, 3, 1, 5200.00, '2026-10-10', 1),
(6, 3, 3, 2, 5200.00, '2026-11-09', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

DROP TABLE IF EXISTS `documentos`;
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `nombre` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_archivo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file-signature',
  `tamano` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2.4 MB',
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `id_cliente`, `nombre`, `tipo_archivo`, `tamano`) VALUES
(1, 1, 'Contrato.pdf', 'file-signature', '2.4 MB'),
(2, 1, 'Copia DNI.jpg', 'file-image', '1.1 MB'),
(3, 2, 'Ficha de pago.pdf', 'file-signature', '3.2 MB'),
(4, 3, 'Taladro Eléctrico', 'contract', '2.4 MB'),
(5, 3, 'Contrato.pdf', 'file-signature', 'Nuevo archivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapas_proyecto`
--

DROP TABLE IF EXISTS `etapas_proyecto`;
CREATE TABLE IF NOT EXISTS `etapas_proyecto` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_proyecto` int UNSIGNED NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` smallint UNSIGNED NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_etapa_proyecto_nombre` (`id_proyecto`,`nombre`),
  KEY `idx_etapa_proyecto_orden` (`id_proyecto`,`activo`,`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `etapas_proyecto`
--

INSERT INTO `etapas_proyecto` (`id`, `id_proyecto`, `nombre`, `orden`, `activo`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 'Lanzamiento', 1, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(2, 2, 'Lanzamiento', 1, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(3, 3, 'Lanzamiento', 1, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(4, 4, 'Lanzamiento', 1, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(5, 1, 'Comercialización', 2, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(6, 2, 'Comercialización', 2, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(7, 3, 'Comercialización', 2, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(8, 4, 'Comercialización', 2, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(9, 1, 'Construcción', 3, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(10, 2, 'Construcción', 3, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(11, 3, 'Construcción', 3, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(12, 4, 'Construcción', 3, 1, '2026-09-30 17:06:10', '2026-09-30 17:18:25'),
(13, 1, 'Entrega', 4, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(14, 2, 'Entrega', 4, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(15, 3, 'Entrega', 4, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(16, 4, 'Entrega', 4, 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_acceso`
--

DROP TABLE IF EXISTS `intentos_acceso`;
CREATE TABLE IF NOT EXISTS `intentos_acceso` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dispositivo` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correcto` tinyint(1) NOT NULL DEFAULT '0',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_intentos_email_fecha` (`email`,`creado_en`),
  KEY `idx_intentos_ip_fecha` (`ip`,`creado_en`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `intentos_acceso`
--

INSERT INTO `intentos_acceso` (`id`, `email`, `ip`, `dispositivo`, `correcto`, `creado_en`) VALUES
(1, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 1, '2026-09-30 17:00:18'),
(2, 'admin@sistema.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 0, '2026-10-06 18:18:22'),
(3, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', 1, '2026-10-06 18:18:41'),
(4, 'anderson@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 0, '2026-10-12 17:39:24'),
(5, 'anderson@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 0, '2026-10-12 17:39:32'),
(6, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 0, '2026-10-12 17:42:32'),
(7, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 1, '2026-10-12 17:42:45'),
(8, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', 1, '2026-10-12 17:58:03'),
(9, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.141.0 Chrome/150.0.7871.250 Electron/43.7.7 Safari/537.36', 1, '2026-10-12 18:02:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `leads`
--

DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_campana` int UNSIGNED DEFAULT NULL,
  `id_usuario_asignado` int UNSIGNED DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proyecto_interes` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nivel_interes` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Bajo',
  `etapa` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nuevo Lead',
  `id_cliente_convertido` int UNSIGNED DEFAULT NULL,
  `ubigeo_distrito` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_leads_usuario_etapa` (`id_usuario_asignado`,`etapa`),
  KEY `fk_leads_campana` (`id_campana`),
  KEY `idx_leads_cliente_convertido` (`id_cliente_convertido`),
  KEY `idx_leads_ubigeo` (`ubigeo_distrito`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `leads`
--

INSERT INTO `leads` (`id`, `id_campana`, `id_usuario_asignado`, `nombre`, `proyecto_interes`, `nivel_interes`, `etapa`, `id_cliente_convertido`, `ubigeo_distrito`, `latitud`, `longitud`) VALUES
(1, 1, 1, 'Andrea Quispe', 'Condominio Las Palmas', 'Alto', 'Nuevo', NULL, NULL, NULL, NULL),
(2, 2, 2, 'Carlos Huamán', 'Lotes Villa Sol', 'Bajo', 'Contactado', NULL, NULL, NULL, NULL),
(3, 3, 3, 'Miguel Fernández', 'Residencial Los Álamos', 'Medio', 'Visita Realizada', NULL, NULL, NULL, NULL),
(4, NULL, NULL, 'Diego Chávez', 'Condominio Mirador', 'Alto', 'Negociación', NULL, NULL, NULL, NULL),
(5, NULL, NULL, 'Gabriela Núñez', 'Residencial Los Álamos', 'Alto', 'Venta Cerrada', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_ventas`
--

DROP TABLE IF EXISTS `metas_ventas`;
CREATE TABLE IF NOT EXISTS `metas_ventas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `mes` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` tinyint UNSIGNED NOT NULL,
  `venta` decimal(10,2) NOT NULL,
  `meta` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `metas_ventas`
--

INSERT INTO `metas_ventas` (`id`, `mes`, `orden`, `venta`, `meta`) VALUES
(1, 'Abr', 1, 1.80, 2.00),
(2, 'May', 2, 2.30, 2.50),
(3, 'Jun', 3, 2.80, 2.90),
(4, 'Jul', 4, 2.40, 2.70),
(5, 'Ago', 5, 3.10, 3.00),
(6, 'Set', 6, 3.40, 3.20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE IF NOT EXISTS `permisos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etiqueta` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permisos_modulo_accion` (`modulo`,`accion`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `modulo`, `accion`, `etiqueta`) VALUES
(1, 'dashboard', 'ver', 'Ver Dashboard'),
(2, 'dashboard', 'crear', 'Crear Dashboard'),
(3, 'dashboard', 'editar', 'Editar Dashboard'),
(4, 'dashboard', 'eliminar', 'Eliminar Dashboard'),
(5, 'dashboard', 'aprobar', 'Aprobar Dashboard'),
(6, 'dashboard', 'exportar', 'Exportar Dashboard'),
(7, 'dashboard', 'imprimir', 'Imprimir Dashboard'),
(8, 'dashboard', 'descargar', 'Descargar Dashboard'),
(9, 'crm', 'ver', 'Ver CRM'),
(10, 'crm', 'crear', 'Crear CRM'),
(11, 'crm', 'editar', 'Editar CRM'),
(12, 'crm', 'eliminar', 'Eliminar CRM'),
(13, 'crm', 'aprobar', 'Aprobar CRM'),
(14, 'crm', 'exportar', 'Exportar CRM'),
(15, 'crm', 'imprimir', 'Imprimir CRM'),
(16, 'crm', 'descargar', 'Descargar CRM'),
(17, 'proyectos', 'ver', 'Ver Proyectos'),
(18, 'proyectos', 'crear', 'Crear Proyectos'),
(19, 'proyectos', 'editar', 'Editar Proyectos'),
(20, 'proyectos', 'eliminar', 'Eliminar Proyectos'),
(21, 'proyectos', 'aprobar', 'Aprobar Proyectos'),
(22, 'proyectos', 'exportar', 'Exportar Proyectos'),
(23, 'proyectos', 'imprimir', 'Imprimir Proyectos'),
(24, 'proyectos', 'descargar', 'Descargar Proyectos'),
(25, 'inventario', 'ver', 'Ver Inventario'),
(26, 'inventario', 'crear', 'Crear Inventario'),
(27, 'inventario', 'editar', 'Editar Inventario'),
(28, 'inventario', 'eliminar', 'Eliminar Inventario'),
(29, 'inventario', 'aprobar', 'Aprobar Inventario'),
(30, 'inventario', 'exportar', 'Exportar Inventario'),
(31, 'inventario', 'imprimir', 'Imprimir Inventario'),
(32, 'inventario', 'descargar', 'Descargar Inventario'),
(33, 'clientes', 'ver', 'Ver Clientes'),
(34, 'clientes', 'crear', 'Crear Clientes'),
(35, 'clientes', 'editar', 'Editar Clientes'),
(36, 'clientes', 'eliminar', 'Eliminar Clientes'),
(37, 'clientes', 'aprobar', 'Aprobar Clientes'),
(38, 'clientes', 'exportar', 'Exportar Clientes'),
(39, 'clientes', 'imprimir', 'Imprimir Clientes'),
(40, 'clientes', 'descargar', 'Descargar Clientes'),
(41, 'separaciones', 'ver', 'Ver Separaciones'),
(42, 'separaciones', 'crear', 'Crear Separaciones'),
(43, 'separaciones', 'editar', 'Editar Separaciones'),
(44, 'separaciones', 'eliminar', 'Eliminar Separaciones'),
(45, 'separaciones', 'aprobar', 'Aprobar Separaciones'),
(46, 'separaciones', 'exportar', 'Exportar Separaciones'),
(47, 'separaciones', 'imprimir', 'Imprimir Separaciones'),
(48, 'separaciones', 'descargar', 'Descargar Separaciones'),
(49, 'ventas', 'ver', 'Ver Ventas'),
(50, 'ventas', 'crear', 'Crear Ventas'),
(51, 'ventas', 'editar', 'Editar Ventas'),
(52, 'ventas', 'eliminar', 'Eliminar Ventas'),
(53, 'ventas', 'aprobar', 'Aprobar Ventas'),
(54, 'ventas', 'exportar', 'Exportar Ventas'),
(55, 'ventas', 'imprimir', 'Imprimir Ventas'),
(56, 'ventas', 'descargar', 'Descargar Ventas'),
(57, 'caja', 'ver', 'Ver Caja'),
(58, 'caja', 'crear', 'Crear Caja'),
(59, 'caja', 'editar', 'Editar Caja'),
(60, 'caja', 'eliminar', 'Eliminar Caja'),
(61, 'caja', 'aprobar', 'Aprobar Caja'),
(62, 'caja', 'exportar', 'Exportar Caja'),
(63, 'caja', 'imprimir', 'Imprimir Caja'),
(64, 'caja', 'descargar', 'Descargar Caja'),
(65, 'reportes', 'ver', 'Ver Reportes'),
(66, 'reportes', 'crear', 'Crear Reportes'),
(67, 'reportes', 'editar', 'Editar Reportes'),
(68, 'reportes', 'eliminar', 'Eliminar Reportes'),
(69, 'reportes', 'aprobar', 'Aprobar Reportes'),
(70, 'reportes', 'exportar', 'Exportar Reportes'),
(71, 'reportes', 'imprimir', 'Imprimir Reportes'),
(72, 'reportes', 'descargar', 'Descargar Reportes'),
(73, 'configuracion', 'ver', 'Ver Configuraci??n'),
(74, 'configuracion', 'crear', 'Crear Configuraci??n'),
(75, 'configuracion', 'editar', 'Editar Configuraci??n'),
(76, 'configuracion', 'eliminar', 'Eliminar Configuraci??n'),
(77, 'configuracion', 'aprobar', 'Aprobar Configuraci??n'),
(78, 'configuracion', 'exportar', 'Exportar Configuraci??n'),
(79, 'configuracion', 'imprimir', 'Imprimir Configuraci??n'),
(80, 'configuracion', 'descargar', 'Descargar Configuraci??n'),
(81, 'auditoria', 'ver', 'Ver Auditor??a'),
(82, 'auditoria', 'crear', 'Crear Auditor??a'),
(83, 'auditoria', 'editar', 'Editar Auditor??a'),
(84, 'auditoria', 'eliminar', 'Eliminar Auditor??a'),
(85, 'auditoria', 'aprobar', 'Aprobar Auditor??a'),
(86, 'auditoria', 'exportar', 'Exportar Auditor??a'),
(87, 'auditoria', 'imprimir', 'Imprimir Auditor??a'),
(88, 'auditoria', 'descargar', 'Descargar Auditor??a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ubicacion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubigeo_distrito` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `estado` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En planificaci??n',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_proyectos_ubigeo` (`ubigeo_distrito`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `ubicacion`, `ubigeo_distrito`, `latitud`, `longitud`, `descripcion`, `fecha_inicio`, `fecha_entrega`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Lotes Villa Sol', 'San Isidro', NULL, NULL, NULL, NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(2, 'Torres del Parque', 'Surco', NULL, NULL, NULL, NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(3, 'Condominio Las Palmas', 'San Miguel', NULL, NULL, NULL, NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(4, 'Residencial Los Álamos', 'La Molina', NULL, NULL, NULL, NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-10-12 18:20:15'),
(8, 'ESPERANZA SATIPO', 'MARGARITAS', NULL, NULL, NULL, 'Venta de terreno', '2026-10-06', '2026-10-31', 'En preventa', '2026-10-06 18:30:32', '2026-10-06 18:30:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recordatorios`
--

DROP TABLE IF EXISTS `recordatorios`;
CREATE TABLE IF NOT EXISTS `recordatorios` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `prioridad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `fecha_programada` date NOT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recordatorios_fecha` (`fecha_programada`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recordatorios`
--

INSERT INTO `recordatorios` (`id`, `titulo`, `descripcion`, `tipo`, `prioridad`, `fecha_programada`, `estado`, `creado_en`) VALUES
(1, 'Seguimiento de lead premium', 'Llamar al cliente para confirmar visita del proyecto.', 'Lead', 'alta', '2026-10-08', 'pendiente', '2026-10-06 06:15:01'),
(2, 'Confirmar firma de contrato', 'Enviar documento final y coordinar firma con asesor.', 'Cliente', 'alta', '2026-10-10', 'pendiente', '2026-10-06 06:15:01'),
(3, 'Actualizar inventario', 'Revisar disponibilidad de unidades prioritarias.', 'General', 'media', '2026-10-07', 'pendiente', '2026-10-06 06:15:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etiqueta` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `etiqueta`, `descripcion`, `activo`, `creado_en`) VALUES
(1, 'superadmin', 'Superadministrador', 'Acceso total y administraci??n inicial del sistema.', 1, '2026-09-30 16:46:45'),
(2, 'administrador', 'Administrador', 'Administraci??n operativa del sistema.', 1, '2026-09-30 16:46:45'),
(3, 'gerencia', 'Gerencia', 'Consulta de indicadores y aprobaciones.', 1, '2026-09-30 16:46:45'),
(4, 'director_comercial', 'Director comercial', 'Gesti??n comercial, campa??as y asesores.', 1, '2026-09-30 16:46:45'),
(5, 'asesor', 'Asesor', 'Gesti??n de su cartera comercial.', 1, '2026-09-30 16:46:45'),
(6, 'jefe_proyecto', 'Jefe de proyecto', 'Gesti??n de proyectos e inventario.', 1, '2026-09-30 16:46:45'),
(7, 'caja_cobranza', 'Caja y cobranza', 'Gesti??n de caja, pagos y cobranzas.', 1, '2026-09-30 16:46:45'),
(8, 'legal_documentos', 'Legal y documentos', 'Gesti??n de contratos y documentos.', 1, '2026-09-30 16:46:45'),
(9, 'soporte_auditoria', 'Soporte y auditor??a', 'Consulta t??cnica y de trazabilidad.', 1, '2026-09-30 16:46:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

DROP TABLE IF EXISTS `rol_permisos`;
CREATE TABLE IF NOT EXISTS `rol_permisos` (
  `id_rol` int UNSIGNED NOT NULL,
  `id_permiso` int UNSIGNED NOT NULL,
  `permitido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `fk_rol_permisos_permiso` (`id_permiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id_rol`, `id_permiso`, `permitido`) VALUES
(1, 1, 1),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1),
(1, 6, 1),
(1, 7, 1),
(1, 8, 1),
(1, 9, 1),
(1, 10, 1),
(1, 11, 1),
(1, 12, 1),
(1, 13, 1),
(1, 14, 1),
(1, 15, 1),
(1, 16, 1),
(1, 17, 1),
(1, 18, 1),
(1, 19, 1),
(1, 20, 1),
(1, 21, 1),
(1, 22, 1),
(1, 23, 1),
(1, 24, 1),
(1, 25, 1),
(1, 26, 1),
(1, 27, 1),
(1, 28, 1),
(1, 29, 1),
(1, 30, 1),
(1, 31, 1),
(1, 32, 1),
(1, 33, 1),
(1, 34, 1),
(1, 35, 1),
(1, 36, 1),
(1, 37, 1),
(1, 38, 1),
(1, 39, 1),
(1, 40, 1),
(1, 41, 1),
(1, 42, 1),
(1, 43, 1),
(1, 44, 1),
(1, 45, 1),
(1, 46, 1),
(1, 47, 1),
(1, 48, 1),
(1, 49, 1),
(1, 50, 1),
(1, 51, 1),
(1, 52, 1),
(1, 53, 1),
(1, 54, 1),
(1, 55, 1),
(1, 56, 1),
(1, 57, 1),
(1, 58, 1),
(1, 59, 1),
(1, 60, 1),
(1, 61, 1),
(1, 62, 1),
(1, 63, 1),
(1, 64, 1),
(1, 65, 1),
(1, 66, 1),
(1, 67, 1),
(1, 68, 1),
(1, 69, 1),
(1, 70, 1),
(1, 71, 1),
(1, 72, 1),
(1, 73, 1),
(1, 74, 1),
(1, 75, 1),
(1, 76, 1),
(1, 77, 1),
(1, 78, 1),
(1, 79, 1),
(1, 80, 1),
(1, 81, 1),
(1, 82, 1),
(1, 83, 1),
(1, 84, 1),
(1, 85, 1),
(1, 86, 1),
(1, 87, 1),
(1, 88, 1),
(2, 1, 1),
(2, 2, 1),
(2, 3, 1),
(2, 4, 1),
(2, 5, 1),
(2, 6, 1),
(2, 7, 1),
(2, 8, 1),
(2, 9, 1),
(2, 10, 1),
(2, 11, 1),
(2, 12, 1),
(2, 13, 1),
(2, 14, 1),
(2, 15, 1),
(2, 16, 1),
(2, 17, 1),
(2, 18, 1),
(2, 19, 1),
(2, 20, 1),
(2, 21, 1),
(2, 22, 1),
(2, 23, 1),
(2, 24, 1),
(2, 25, 1),
(2, 26, 1),
(2, 27, 1),
(2, 28, 1),
(2, 29, 1),
(2, 30, 1),
(2, 31, 1),
(2, 32, 1),
(2, 33, 1),
(2, 34, 1),
(2, 35, 1),
(2, 36, 1),
(2, 37, 1),
(2, 38, 1),
(2, 39, 1),
(2, 40, 1),
(2, 41, 1),
(2, 42, 1),
(2, 43, 1),
(2, 44, 1),
(2, 45, 1),
(2, 46, 1),
(2, 47, 1),
(2, 48, 1),
(2, 49, 1),
(2, 50, 1),
(2, 51, 1),
(2, 52, 1),
(2, 53, 1),
(2, 54, 1),
(2, 55, 1),
(2, 56, 1),
(2, 57, 1),
(2, 58, 1),
(2, 59, 1),
(2, 60, 1),
(2, 61, 1),
(2, 62, 1),
(2, 63, 1),
(2, 64, 1),
(2, 65, 1),
(2, 66, 1),
(2, 67, 1),
(2, 68, 1),
(2, 69, 1),
(2, 70, 1),
(2, 71, 1),
(2, 72, 1),
(2, 73, 1),
(2, 74, 1),
(2, 75, 1),
(2, 76, 1),
(2, 77, 1),
(2, 78, 1),
(2, 79, 1),
(2, 80, 1),
(2, 81, 1),
(3, 1, 1),
(3, 5, 1),
(3, 6, 1),
(3, 7, 1),
(3, 8, 1),
(3, 9, 1),
(3, 13, 1),
(3, 14, 1),
(3, 15, 1),
(3, 16, 1),
(3, 17, 1),
(3, 21, 1),
(3, 22, 1),
(3, 23, 1),
(3, 24, 1),
(3, 25, 1),
(3, 29, 1),
(3, 30, 1),
(3, 31, 1),
(3, 32, 1),
(3, 33, 1),
(3, 37, 1),
(3, 38, 1),
(3, 39, 1),
(3, 40, 1),
(3, 41, 1),
(3, 45, 1),
(3, 46, 1),
(3, 47, 1),
(3, 48, 1),
(3, 49, 1),
(3, 53, 1),
(3, 54, 1),
(3, 55, 1),
(3, 56, 1),
(3, 57, 1),
(3, 61, 1),
(3, 62, 1),
(3, 63, 1),
(3, 64, 1),
(3, 65, 1),
(3, 69, 1),
(3, 70, 1),
(3, 71, 1),
(3, 72, 1),
(4, 1, 1),
(4, 2, 1),
(4, 3, 1),
(4, 5, 1),
(4, 6, 1),
(4, 7, 1),
(4, 8, 1),
(4, 9, 1),
(4, 10, 1),
(4, 11, 1),
(4, 13, 1),
(4, 14, 1),
(4, 15, 1),
(4, 16, 1),
(4, 17, 1),
(4, 18, 1),
(4, 19, 1),
(4, 21, 1),
(4, 22, 1),
(4, 23, 1),
(4, 24, 1),
(4, 25, 1),
(4, 26, 1),
(4, 27, 1),
(4, 29, 1),
(4, 30, 1),
(4, 31, 1),
(4, 32, 1),
(4, 33, 1),
(4, 34, 1),
(4, 35, 1),
(4, 37, 1),
(4, 38, 1),
(4, 39, 1),
(4, 40, 1),
(4, 41, 1),
(4, 42, 1),
(4, 43, 1),
(4, 45, 1),
(4, 46, 1),
(4, 47, 1),
(4, 48, 1),
(4, 49, 1),
(4, 50, 1),
(4, 51, 1),
(4, 53, 1),
(4, 54, 1),
(4, 55, 1),
(4, 56, 1),
(4, 65, 1),
(4, 66, 1),
(4, 67, 1),
(4, 69, 1),
(4, 70, 1),
(4, 71, 1),
(4, 72, 1),
(5, 9, 1),
(5, 10, 1),
(5, 11, 1),
(5, 25, 1),
(5, 26, 1),
(5, 27, 1),
(5, 33, 1),
(5, 34, 1),
(5, 35, 1),
(5, 41, 1),
(5, 42, 1),
(5, 43, 1),
(6, 1, 1),
(6, 2, 1),
(6, 3, 1),
(6, 6, 1),
(6, 17, 1),
(6, 18, 1),
(6, 19, 1),
(6, 22, 1),
(6, 25, 1),
(6, 26, 1),
(6, 27, 1),
(6, 30, 1),
(6, 33, 1),
(6, 34, 1),
(6, 35, 1),
(6, 38, 1),
(7, 1, 1),
(7, 2, 1),
(7, 3, 1),
(7, 5, 1),
(7, 6, 1),
(7, 7, 1),
(7, 8, 1),
(7, 33, 1),
(7, 34, 1),
(7, 35, 1),
(7, 37, 1),
(7, 38, 1),
(7, 39, 1),
(7, 40, 1),
(7, 49, 1),
(7, 50, 1),
(7, 51, 1),
(7, 53, 1),
(7, 54, 1),
(7, 55, 1),
(7, 56, 1),
(7, 57, 1),
(7, 58, 1),
(7, 59, 1),
(7, 61, 1),
(7, 62, 1),
(7, 63, 1),
(7, 64, 1),
(7, 65, 1),
(7, 66, 1),
(7, 67, 1),
(7, 69, 1),
(7, 70, 1),
(7, 71, 1),
(7, 72, 1),
(8, 1, 1),
(8, 2, 1),
(8, 3, 1),
(8, 7, 1),
(8, 8, 1),
(8, 33, 1),
(8, 34, 1),
(8, 35, 1),
(8, 39, 1),
(8, 40, 1),
(8, 49, 1),
(8, 50, 1),
(8, 51, 1),
(8, 55, 1),
(8, 56, 1),
(8, 65, 1),
(8, 66, 1),
(8, 67, 1),
(8, 71, 1),
(8, 72, 1),
(9, 1, 1),
(9, 81, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `separaciones`
--

DROP TABLE IF EXISTS `separaciones`;
CREATE TABLE IF NOT EXISTS `separaciones` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_unidad` int UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Contado',
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `separaciones`
--

INSERT INTO `separaciones` (`id`, `id_cliente`, `id_unidad`, `monto`, `metodo_pago`, `fecha_vencimiento`, `observaciones`, `creado_en`) VALUES
(1, 2, 2, 385000.00, 'Cuotas', '2026-10-20', 'Separaci??n de departamento con pago inicial', '2026-09-30 16:20:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

DROP TABLE IF EXISTS `unidades`;
CREATE TABLE IF NOT EXISTS `unidades` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_proyecto` int UNSIGNED NOT NULL,
  `id_etapa` int UNSIGNED DEFAULT NULL,
  `codigo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_m2` decimal(10,2) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `estado` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo_unique` (`codigo`),
  KEY `id_proyecto` (`id_proyecto`),
  KEY `idx_unidades_proyecto_estado` (`id_proyecto`,`estado`,`activo`),
  KEY `idx_unidades_etapa` (`id_etapa`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidades`
--

INSERT INTO `unidades` (`id`, `id_proyecto`, `id_etapa`, `codigo`, `tipo`, `area_m2`, `precio`, `estado`, `activo`, `creado_en`, `actualizado_en`) VALUES
(1, 1, 5, 'LVS-A-024', 'Lote', 160.00, 89500.00, 'Disponible', 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(2, 2, 6, 'TDP-B048', 'Dpto.', 92.50, 385000.00, 'Reservado', 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(3, 3, 7, 'CLP-1102', 'Dpto.', 74.00, 298000.00, 'Separado', 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(4, 1, 5, 'LVS-C-011', 'Lote', 200.00, 112000.00, 'Vendido', 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(5, 4, 8, 'RLA-204', 'Dpto.', 81.40, 330000.00, 'Disponible', 1, '2026-09-30 17:06:10', '2026-09-30 17:06:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `failed_login_attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `avatar_url` mediumtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombres`, `email`, `password_hash`, `estado`, `failed_login_attempts`, `locked_until`, `last_login_at`, `created_at`, `updated_at`, `avatar_url`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-09-30 16:46:45', ''),
(2, 'Carlos Ramírez', 'carlos@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-10-12 18:53:17', ''),
(3, 'Luis Torres', 'luis@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-10-06 18:32:59', '');
INSERT INTO `usuarios` (`id_usuario`, `nombres`, `email`, `password_hash`, `estado`, `failed_login_attempts`, `locked_until`, `last_login_at`, `created_at`, `updated_at`, `avatar_url`) VALUES
(4, 'FRANKLIN', 'andersonfranklinvelizhuaman@gmail.com', '$2y$10$CyMAovFGC9tYl5MCWViBoepnll9RjFHOEGPbIuizIjauRwwPTyay6', 1, 0, NULL, '2026-10-12 18:02:32', '2026-09-30 16:59:49', '2026-10-12 18:13:47', 'data:image/jpeg;base64,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');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

DROP TABLE IF EXISTS `usuario_roles`;
CREATE TABLE IF NOT EXISTS `usuario_roles` (
  `id_usuario` int UNSIGNED NOT NULL,
  `id_rol` int UNSIGNED NOT NULL,
  `asignado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`,`id_rol`),
  KEY `fk_usuario_roles_rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_roles`
--

INSERT INTO `usuario_roles` (`id_usuario`, `id_rol`, `asignado_en`) VALUES
(4, 1, '2026-09-30 16:59:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_proyecto` int UNSIGNED NOT NULL,
  `id_unidad` int UNSIGNED NOT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Vendido',
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_proyecto` (`id_proyecto`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cliente`, `id_proyecto`, `id_unidad`, `estado`, `fecha`) VALUES
(1, 1, 1, 1, 'Vendido', '2026-03-10'),
(2, 2, 2, 2, 'Separado', '2026-04-12'),
(3, 3, 4, 5, 'Vendido', '2026-06-06');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asesores`
--
ALTER TABLE `asesores`
  ADD CONSTRAINT `fk_asesores_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD CONSTRAINT `fk_configuracion_usuario` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `etapas_proyecto`
--
ALTER TABLE `etapas_proyecto`
  ADD CONSTRAINT `fk_etapas_proyecto_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`);

--
-- Filtros para la tabla `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_leads_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas` (`id_campana`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_cliente_convertido` FOREIGN KEY (`id_cliente_convertido`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_leads_usuario` FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `fk_rol_permisos_permiso` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rol_permisos_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `unidades`
--
ALTER TABLE `unidades`
  ADD CONSTRAINT `fk_unidades_etapa` FOREIGN KEY (`id_etapa`) REFERENCES `etapas_proyecto` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_unidades_proyecto` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`);

--
-- Filtros para la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD CONSTRAINT `fk_usuario_roles_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `fk_usuario_roles_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
