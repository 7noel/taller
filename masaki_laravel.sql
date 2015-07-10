-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-07-2015 a las 15:51:42
-- Versión del servidor: 5.6.24
-- Versión de PHP: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `masaki_laravel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `checkitems`
--

CREATE TABLE IF NOT EXISTS `checkitems` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `checkitem_group_id` int(10) unsigned NOT NULL,
  `with_status` tinyint(1) NOT NULL DEFAULT '1',
  `with_value` tinyint(1) NOT NULL DEFAULT '0',
  `column_two` tinyint(1) NOT NULL DEFAULT '0',
  `pre_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `checkitems`
--

INSERT INTO `checkitems` (`id`, `name`, `checkitem_group_id`, `with_status`, `with_value`, `column_two`, `pre_value`, `post_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Faros delanteros (Revisar focos de alta)', 1, 1, 0, 0, '', '', '2015-07-07 01:27:02', '2015-07-07 03:10:51', NULL),
(2, 'Faros delanteros (Revisar focos de baja)', 1, 1, 0, 0, '', '', '2015-07-07 01:31:26', '2015-07-07 03:10:51', NULL),
(3, 'Faros traseros', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(4, 'Luces de frenos', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(5, 'Luces de parada de emergencia', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(6, 'Luces direccionales', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(7, 'Luces interiores', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(8, 'Rociador de parabrisa', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(9, 'Operación de la plumilla', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(10, 'Jebes de plumilla', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(11, 'Estado del parabrisas', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(12, 'Freno de estacionamiento', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(13, 'Operación del claxon', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(14, 'Operación del embrague (si aplica)', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(15, 'Estado del filtro de polvo y polen', 1, 1, 0, 0, '', '', '2015-07-07 01:38:03', '2015-07-07 03:10:51', NULL),
(16, 'Estado de la batería (Revisar prueba del ED-18 adjunta)', 2, 1, 0, 0, '', '', '2015-07-07 01:39:13', '2015-07-07 03:10:57', NULL),
(17, 'Fluido Refrigerante', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(18, 'Fluido de dirección hidraúlica', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(19, 'Fluido de frenos', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(20, 'Fluido de lavado de parabrisa', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(21, 'Fluido de transmisión automática', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(22, 'Estado del filtro de aire', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(23, 'Estado de correas de transmisión', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(24, 'Estado de mangueras de radiador', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(25, 'Reservorio del fluido hidraúlico', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(26, 'Líneas de freno/Mangueras/Cable del freno de estacionamiento', 3, 1, 0, 0, '', '', '2015-07-07 01:47:10', '2015-07-07 03:11:13', NULL),
(27, 'Estado de los amortiguadores', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(28, 'Estado de los trapecios', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(29, 'Estado de las rótulas y ponchos', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(30, 'Estado de la cremallera de dirección y retenes de polvo', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(31, 'Estado del sistema de escape', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(32, 'Fugas de aceite de motor y/o fluidos', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(33, 'Estado de los ponchos y puntas de palier', 4, 1, 0, 0, '', '', '2015-07-07 01:50:11', '2015-07-07 03:11:30', NULL),
(34, '<strong>Patrón de Desgaste</strong>', 5, 0, 0, 0, '', '', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(35, 'Delantera izquierda', 5, 1, 1, 0, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(36, 'Trasera izquierda', 5, 1, 1, 0, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(37, 'Llanta de repuesto', 5, 1, 1, 0, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(38, 'Presión de aire juego trasero', 5, 0, 1, 0, '', 'psi', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(39, 'Delantera Derecha', 5, 1, 1, 1, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(40, 'Trasera Derecha', 5, 1, 1, 1, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(41, 'Presión de aire de juego delantero', 5, 0, 1, 1, 'Banda', '32 avos', '2015-07-07 01:54:19', '2015-07-07 03:13:28', NULL),
(42, '(Límite de servicio: 1.6 mm)', 6, 0, 0, 0, '', '', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL),
(43, 'Delantera izquierda', 6, 1, 1, 0, '', 'mm', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL),
(44, 'Trasera izquierda', 6, 1, 1, 0, '', 'mm', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL),
(45, 'Los frenos no fueron inspeccionados en esta vista', 6, 0, 0, 0, '', '', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL),
(46, 'Delantera derecha', 6, 1, 1, 1, '', 'mm', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL),
(47, 'Trasera derecha', 6, 1, 1, 1, '', 'mm', '2015-07-07 01:58:31', '2015-07-07 03:13:46', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `checkitem_groups`
--

CREATE TABLE IF NOT EXISTS `checkitem_groups` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `is_double_column` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `checkitem_groups`
--

INSERT INTO `checkitem_groups` (`id`, `name`, `order`, `is_double_column`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INTERIOR/EXTERIOOR', 1, 0, '2015-07-07 01:27:02', '2015-07-07 03:10:51', NULL),
(2, 'BATERÍA', 2, 0, '2015-07-07 01:39:13', '2015-07-07 03:10:57', NULL),
(3, 'DEBAJO DEL CAPOT', 3, 0, '2015-07-07 01:39:40', '2015-07-07 03:11:13', NULL),
(4, 'DEBAJO DEL AUTO', 4, 0, '2015-07-07 01:50:10', '2015-07-07 03:11:30', NULL),
(5, 'CONDICIÓN DE LLANTAS', 5, 1, '2015-07-07 01:54:19', '2015-07-07 01:58:39', NULL),
(6, 'CONDICIÓN DE PASTILLAS DE FRENO', 6, 1, '2015-07-07 01:58:31', '2015-07-07 01:58:31', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `checkitems`
--
ALTER TABLE `checkitems`
  ADD PRIMARY KEY (`id`), ADD KEY `checkitems_checkitem_group_id_foreign` (`checkitem_group_id`);

--
-- Indices de la tabla `checkitem_groups`
--
ALTER TABLE `checkitem_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `checkitems`
--
ALTER TABLE `checkitems`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT de la tabla `checkitem_groups`
--
ALTER TABLE `checkitem_groups`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `checkitems`
--
ALTER TABLE `checkitems`
ADD CONSTRAINT `checkitems_checkitem_group_id_foreign` FOREIGN KEY (`checkitem_group_id`) REFERENCES `checkitem_groups` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
