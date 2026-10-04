-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2026 a las 01:16:05
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cms_inmobiliario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asesores`
--

CREATE TABLE `asesores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `asesores`
--

INSERT INTO `asesores` (`id`, `nombre`, `email`, `estado`, `id_usuario`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', 1, 1),
(2, 'Carlos Ram??rez', 'carlos@inmobiliaria.com', 1, 2),
(3, 'Luis Torres', 'luis@inmobiliaria.com', 1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL,
  `evento` varchar(80) NOT NULL,
  `entidad` varchar(80) NOT NULL,
  `entidad_id` varchar(80) DEFAULT NULL,
  `datos_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_anteriores`)),
  `datos_nuevos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos_nuevos`)),
  `ip` varchar(45) DEFAULT NULL,
  `dispositivo` varchar(500) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria`
--

INSERT INTO `auditoria` (`id`, `id_usuario`, `evento`, `entidad`, `entidad_id`, `datos_anteriores`, `datos_nuevos`, `ip`, `dispositivo`, `creado_en`) VALUES
(1, NULL, 'superadmin_inicial_creado', 'usuarios', '4', NULL, '{\"email\":\"andersonfranklinvelizhuaman@gmail.com\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 16:59:49'),
(2, 4, 'login_correcto', 'usuarios', '4', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-09-30 17:00:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanas`
--

CREATE TABLE `campanas` (
  `id_campana` int(10) UNSIGNED NOT NULL,
  `id_canal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `campanas`
--

INSERT INTO `campanas` (`id_campana`, `id_canal`, `nombre`) VALUES
(1, 1, 'Campa??a Verano 2026'),
(2, 1, 'Lanzamiento Exclusivo'),
(3, 2, 'Feria Inmobiliaria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canales`
--

CREATE TABLE `canales` (
  `id_canal` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `catalogo_valores` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoria` varchar(60) NOT NULL,
  `codigo` varchar(60) NOT NULL,
  `etiqueta` varchar(120) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `clientes` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_asesor` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(120) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ocupacion` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(50) DEFAULT NULL,
  `estado` varchar(40) NOT NULL DEFAULT 'Cliente Activo',
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `id_asesor`, `nombre`, `dni`, `email`, `telefono`, `direccion`, `ocupacion`, `estado_civil`, `estado`, `fecha_registro`) VALUES
(1, 1, 'Mar??a L??pez', '40123456', 'maria.lopez@gmail.com', '+51 999 111 222', 'Av. Brasil 123', 'Profesional', 'Soltera', 'Cliente Activo', '2025-03-15'),
(2, 2, 'Pedro Ram??rez', '40876543', 'pedro.ramirez@gmail.com', '+51 999 333 444', 'Jr. Puno 456', 'Empresario', 'Casado', 'En Mora', '2024-11-20'),
(3, 3, 'Carmen V??squez', '45123456', 'carmen.vasquez@gmail.com', '+51 999 555 666', 'Av. Benavides 789', 'Gerente', 'Divorciada', 'Cliente Activo', '2025-01-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_leads`
--

CREATE TABLE `clientes_leads` (
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_asesor` int(10) UNSIGNED DEFAULT NULL,
  `id_campana` int(10) UNSIGNED DEFAULT NULL,
  `nombres` varchar(100) NOT NULL,
  `estado_lead` varchar(50) NOT NULL DEFAULT 'Nuevo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes_leads`
--

INSERT INTO `clientes_leads` (`id_cliente`, `id_asesor`, `id_campana`, `nombres`, `estado_lead`) VALUES
(1, 1, 1, 'Andrea Quispe', 'Contactado'),
(2, 2, 2, 'Carlos Huam??n', 'Nuevo'),
(3, 3, 3, 'Miguel Fern??ndez', 'Visita Realizada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_propiedad`
--

CREATE TABLE `cliente_propiedad` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_unidad` int(10) UNSIGNED NOT NULL,
  `estado` varchar(30) NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `configuracion_sistema` (
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `actualizado_por` int(10) UNSIGNED DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

CREATE TABLE `contactos` (
  `id_contacto` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `tipo_contacto` varchar(30) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `es_principal` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `cuotas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_venta` int(10) UNSIGNED DEFAULT NULL,
  `numero_cuota` int(10) UNSIGNED NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `pagada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuotas`
--

INSERT INTO `cuotas` (`id`, `id_cliente`, `id_venta`, `numero_cuota`, `monto`, `fecha_vencimiento`, `pagada`) VALUES
(1, 1, 1, 1, 4750.00, '2026-10-03', 1),
(2, 1, 1, 2, 4750.00, '2026-10-15', 0),
(3, 2, 2, 1, 6800.00, '2026-09-28', 0),
(4, 2, 2, 2, 6800.00, '2026-10-25', 0),
(5, 3, 3, 1, 5200.00, '2026-10-10', 1),
(6, 3, 3, 2, 5200.00, '2026-11-09', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(180) NOT NULL,
  `tipo_archivo` varchar(30) NOT NULL DEFAULT 'file-signature',
  `tamano` varchar(30) NOT NULL DEFAULT '2.4 MB'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `id_cliente`, `nombre`, `tipo_archivo`, `tamano`) VALUES
(1, 1, 'Contrato.pdf', 'file-signature', '2.4 MB'),
(2, 1, 'Copia DNI.jpg', 'file-image', '1.1 MB'),
(3, 2, 'Ficha de pago.pdf', 'file-signature', '3.2 MB');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapas_proyecto`
--

CREATE TABLE `etapas_proyecto` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 1,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `intentos_acceso` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(120) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `dispositivo` varchar(500) DEFAULT NULL,
  `correcto` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `intentos_acceso`
--

INSERT INTO `intentos_acceso` (`id`, `email`, `ip`, `dispositivo`, `correcto`, `creado_en`) VALUES
(1, 'andersonfranklinvelizhuaman@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 1, '2026-09-30 17:00:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_campana` int(10) UNSIGNED DEFAULT NULL,
  `id_usuario_asignado` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `proyecto_interes` varchar(120) DEFAULT NULL,
  `nivel_interes` varchar(20) DEFAULT 'Bajo',
  `etapa` varchar(50) NOT NULL DEFAULT 'Nuevo Lead'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `leads`
--

INSERT INTO `leads` (`id`, `id_campana`, `id_usuario_asignado`, `nombre`, `proyecto_interes`, `nivel_interes`, `etapa`) VALUES
(1, 1, 1, 'Andrea Quispe', 'Condominio Las Palmas', 'Alto', 'Nuevo Lead'),
(2, 2, 2, 'Carlos Huam??n', 'Lotes Villa Sol', 'Bajo', 'Contactado'),
(3, 3, 3, 'Miguel Fern??ndez', 'Residencial Los ??lamos', 'Medio', 'Visita Realizada'),
(4, NULL, NULL, 'Diego Ch??vez', 'Condominio Mirador', 'Alto', 'Negociaci??n'),
(5, NULL, NULL, 'Gabriela N????ez', 'Residencial Los ??lamos', 'Alto', 'Venta Cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metas_ventas`
--

CREATE TABLE `metas_ventas` (
  `id` int(10) UNSIGNED NOT NULL,
  `mes` varchar(20) NOT NULL,
  `orden` tinyint(3) UNSIGNED NOT NULL,
  `venta` decimal(10,2) NOT NULL,
  `meta` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `permisos` (
  `id` int(10) UNSIGNED NOT NULL,
  `modulo` varchar(60) NOT NULL,
  `accion` varchar(30) NOT NULL,
  `etiqueta` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `proyectos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `estado` varchar(40) NOT NULL DEFAULT 'En planificaci??n',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `ubicacion`, `descripcion`, `fecha_inicio`, `fecha_entrega`, `estado`, `creado_en`, `actualizado_en`) VALUES
(1, 'Lotes Villa Sol', 'San Isidro', NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(2, 'Torres del Parque', 'Surco', NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(3, 'Condominio Las Palmas', 'San Miguel', NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10'),
(4, 'Residencial Los ??lamos', 'La Molina', NULL, NULL, NULL, 'En planificaci??n', '2026-09-30 17:06:10', '2026-09-30 17:06:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `etiqueta` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `rol_permisos` (
  `id_rol` int(10) UNSIGNED NOT NULL,
  `id_permiso` int(10) UNSIGNED NOT NULL,
  `permitido` tinyint(1) NOT NULL DEFAULT 0
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

CREATE TABLE `separaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_unidad` int(10) UNSIGNED NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(40) NOT NULL DEFAULT 'Contado',
  `fecha_vencimiento` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `separaciones`
--

INSERT INTO `separaciones` (`id`, `id_cliente`, `id_unidad`, `monto`, `metodo_pago`, `fecha_vencimiento`, `observaciones`, `creado_en`) VALUES
(1, 2, 2, 385000.00, 'Cuotas', '2026-10-20', 'Separaci??n de departamento con pago inicial', '2026-09-30 16:20:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades`
--

CREATE TABLE `unidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `id_etapa` int(10) UNSIGNED DEFAULT NULL,
  `codigo` varchar(30) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `area_m2` decimal(10,2) NOT NULL,
  `precio` decimal(12,2) NOT NULL,
  `estado` varchar(30) NOT NULL DEFAULT 'Disponible',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `usuarios` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `failed_login_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombres`, `email`, `password_hash`, `estado`, `failed_login_attempts`, `locked_until`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Rosa Mendoza', 'rosa@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-09-30 16:46:45'),
(2, 'Carlos Ram??rez', 'carlos@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-09-30 16:46:45'),
(3, 'Luis Torres', 'luis@inmobiliaria.com', NULL, 1, 0, NULL, NULL, '2026-09-30 16:46:45', '2026-09-30 16:46:45'),
(4, 'FRANKLIN', 'andersonfranklinvelizhuaman@gmail.com', '$2y$10$CyMAovFGC9tYl5MCWViBoepnll9RjFHOEGPbIuizIjauRwwPTyay6', 1, 0, NULL, '2026-09-30 17:00:18', '2026-09-30 16:59:49', '2026-09-30 17:00:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_rol` int(10) UNSIGNED NOT NULL,
  `asignado_en` datetime NOT NULL DEFAULT current_timestamp()
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

CREATE TABLE `ventas` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_proyecto` int(10) UNSIGNED NOT NULL,
  `id_unidad` int(10) UNSIGNED NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Vendido',
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cliente`, `id_proyecto`, `id_unidad`, `estado`, `fecha`) VALUES
(1, 1, 1, 1, 'Vendido', '2026-03-10'),
(2, 2, 2, 2, 'Separado', '2026-04-12'),
(3, 3, 4, 5, 'Vendido', '2026-06-06');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asesores`
--
ALTER TABLE `asesores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_asesores_usuario` (`id_usuario`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_auditoria_usuario_fecha` (`id_usuario`,`creado_en`),
  ADD KEY `idx_auditoria_entidad` (`entidad`,`entidad_id`);

--
-- Indices de la tabla `campanas`
--
ALTER TABLE `campanas`
  ADD PRIMARY KEY (`id_campana`),
  ADD KEY `id_canal` (`id_canal`);

--
-- Indices de la tabla `canales`
--
ALTER TABLE `canales`
  ADD PRIMARY KEY (`id_canal`);

--
-- Indices de la tabla `catalogo_valores`
--
ALTER TABLE `catalogo_valores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_catalogo_categoria_codigo` (`categoria`,`codigo`),
  ADD KEY `idx_catalogo_categoria_activo` (`categoria`,`activo`,`orden`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni_unique` (`dni`),
  ADD KEY `id_asesor` (`id_asesor`);

--
-- Indices de la tabla `clientes_leads`
--
ALTER TABLE `clientes_leads`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_asesor` (`id_asesor`),
  ADD KEY `id_campana` (`id_campana`);

--
-- Indices de la tabla `cliente_propiedad`
--
ALTER TABLE `cliente_propiedad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`clave`),
  ADD KEY `fk_configuracion_usuario` (`actualizado_por`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `cuotas`
--
ALTER TABLE `cuotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `etapas_proyecto`
--
ALTER TABLE `etapas_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_etapa_proyecto_nombre` (`id_proyecto`,`nombre`),
  ADD KEY `idx_etapa_proyecto_orden` (`id_proyecto`,`activo`,`orden`);

--
-- Indices de la tabla `intentos_acceso`
--
ALTER TABLE `intentos_acceso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_intentos_email_fecha` (`email`,`creado_en`),
  ADD KEY `idx_intentos_ip_fecha` (`ip`,`creado_en`);

--
-- Indices de la tabla `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leads_usuario_etapa` (`id_usuario_asignado`,`etapa`),
  ADD KEY `fk_leads_campana` (`id_campana`);

--
-- Indices de la tabla `metas_ventas`
--
ALTER TABLE `metas_ventas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_permisos_modulo_accion` (`modulo`,`accion`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_nombre` (`nombre`);

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id_rol`,`id_permiso`),
  ADD KEY `fk_rol_permisos_permiso` (`id_permiso`);

--
-- Indices de la tabla `separaciones`
--
ALTER TABLE `separaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- Indices de la tabla `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_unique` (`codigo`),
  ADD KEY `id_proyecto` (`id_proyecto`),
  ADD KEY `idx_unidades_proyecto_estado` (`id_proyecto`,`estado`,`activo`),
  ADD KEY `idx_unidades_etapa` (`id_etapa`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuarios_email` (`email`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id_usuario`,`id_rol`),
  ADD KEY `fk_usuario_roles_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_proyecto` (`id_proyecto`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asesores`
--
ALTER TABLE `asesores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `campanas`
--
ALTER TABLE `campanas`
  MODIFY `id_campana` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `canales`
--
ALTER TABLE `canales`
  MODIFY `id_canal` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `catalogo_valores`
--
ALTER TABLE `catalogo_valores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes_leads`
--
ALTER TABLE `clientes_leads`
  MODIFY `id_cliente` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cliente_propiedad`
--
ALTER TABLE `cliente_propiedad`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cuotas`
--
ALTER TABLE `cuotas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `etapas_proyecto`
--
ALTER TABLE `etapas_proyecto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `intentos_acceso`
--
ALTER TABLE `intentos_acceso`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `metas_ventas`
--
ALTER TABLE `metas_ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `separaciones`
--
ALTER TABLE `separaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `unidades`
--
ALTER TABLE `unidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
