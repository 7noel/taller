-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2015 a las 15:53:25
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
-- Estructura de tabla para la tabla `brands`
--

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_car` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `is_car`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'NINGUNO', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(2, 'HONDA', '', 1, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(3, '3M', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(4, 'ABRO', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(5, 'ALTERNATIVA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(6, 'BASF', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(7, 'BOSCH', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(8, 'CAPSA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(9, 'CHEVRON', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(10, 'CONCEPT', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(11, 'DURACELL', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(12, 'ETNA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(13, 'FACTA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(14, 'FAST', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(15, 'GARMIN', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(16, 'GLASURIT', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(17, 'GORILLA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(18, 'HELLA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(19, 'LG', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(20, 'MAC', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(21, 'MICHELIN', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(22, 'MITSUBISHI', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(23, 'NISSAN', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(24, 'PRESTIGE', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(25, 'PROTEMAX', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(26, 'SHELL', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(27, 'TOYOTA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(28, 'WURTH', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(29, 'YOKOHAMA', '', 0, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `car_quotes`
--

CREATE TABLE IF NOT EXISTS `car_quotes` (
  `id` int(10) unsigned NOT NULL,
  `catalog_car_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `attention` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `set_price` decimal(15,2) NOT NULL,
  `observations` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `car_quotes`
--

INSERT INTO `car_quotes` (`id`, `catalog_car_id`, `company_id`, `attention`, `currency_id`, `price`, `set_price`, `observations`, `employee_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 1, 'JOSE', 2, '49990.00', '49990.00', '', 1, '2015-07-10 20:19:46', '2015-07-10 20:30:25', '2015-07-10 20:30:25'),
(3, 1, 43, 'LEONELA', 2, '49990.00', '45990.00', '', 1, '2015-07-10 20:29:57', '2015-07-10 20:33:37', NULL),
(4, 2, 1, 'qffasdfasdfsdf', 2, '40990.00', '39990.00', '', 1, '2015-07-15 22:14:36', '2015-07-23 20:55:24', '2015-07-23 20:55:24'),
(6, 3, 1, 'MARIAN DERTEANO', 2, '31990.00', '28990.00', '', 1, '2015-07-23 20:55:52', '2015-07-23 20:55:52', NULL),
(8, 16, 1, 'Marcos Geller', 2, '32990.00', '32990.00', '', 1, '2015-07-30 21:44:04', '2015-07-30 21:44:42', '2015-07-30 21:44:42'),
(9, 4, 1, 'Marcos Geller', 2, '40990.00', '40990.00', '', 1, '2015-07-30 23:35:32', '2015-08-14 19:32:39', '2015-08-14 19:32:39'),
(10, 2, 1, 'MARIAN DERTEANO', 2, '40990.00', '39990.00', '', 1, '2015-07-30 23:42:20', '2015-08-14 19:32:36', '2015-08-14 19:32:36'),
(11, 19, 1, 'PEPELUCHO', 2, '39990.00', '39990.00', '', 2, '2015-07-31 15:44:17', '2015-08-14 19:32:31', '2015-08-14 19:32:31'),
(16, 12, 1, 'vjbdhfvbds', 2, '35990.00', '35990.00', '', 1, '2015-08-03 20:52:13', '2015-08-14 19:32:28', '2015-08-14 19:32:28'),
(17, 10, 1, ' hchfjfcjncjncfhngnfxng', 2, '28990.00', '28990.00', '', 1, '2015-08-03 20:53:12', '2015-08-14 19:32:22', '2015-08-14 19:32:22'),
(18, 17, 1, 'Estuardo', 2, '35990.00', '35990.00', '', 1, '2015-08-05 21:08:10', '2015-08-14 19:32:18', '2015-08-14 19:32:18'),
(19, 15, 1, 'sfsfs', 2, '52990.00', '52990.00', '', 1, '2015-08-05 22:16:59', '2015-08-14 19:32:15', '2015-08-14 19:32:15'),
(20, 13, 1, 'adhzggfgfh', 2, '43990.00', '43990.00', '', 1, '2015-08-05 23:10:28', '2015-08-14 19:32:10', '2015-08-14 19:32:10'),
(21, 14, 1, 'fjxnxvb', 2, '49990.00', '49990.00', '', 1, '2015-08-05 23:11:46', '2015-08-06 14:53:36', '2015-08-06 14:53:36'),
(22, 15, 1, 'melissa mendoza ', 2, '52990.00', '48990.00', '', 1, '2015-08-06 14:51:36', '2015-08-06 14:53:17', '2015-08-06 14:53:17'),
(24, 2, 1, 'RAFAEL AREVALO LOZANO', 2, '40990.00', '39990.00', '', 1, '2015-08-09 17:40:34', '2015-08-14 19:32:04', '2015-08-14 19:32:04'),
(25, 5, 1, 'Cathy Sandoval', 2, '18990.00', '17990.00', '', 1, '2015-08-14 19:33:08', '2015-08-14 19:33:08', NULL),
(26, 7, 1, 'Cathy Sandoval', 2, '23990.00', '23990.00', '', 1, '2015-08-14 20:37:06', '2015-08-14 20:37:06', NULL),
(27, 12, 1, 'kytdjhg', 2, '35990.00', '35990.00', '', 1, '2015-08-14 21:07:22', '2015-08-14 21:07:22', NULL),
(28, 9, 1, 'PAMELA ', 2, '29.00', '26990.00', '', 1, '2015-08-14 21:47:14', '2015-08-14 21:47:14', NULL),
(29, 10, 1, 'PAMELA ', 2, '28990.00', '28990.00', '', 1, '2015-08-14 21:47:59', '2015-08-14 21:47:59', NULL),
(30, 2, 1, 'JOse', 2, '40990.00', '40000.00', '', 1, '2015-08-14 22:51:17', '2015-08-14 22:51:17', NULL),
(32, 16, 1, 'LUIS NORERO VENTURO', 2, '32990.00', '29990.00', '', 1, '2015-08-15 00:33:52', '2015-08-15 00:33:52', NULL),
(33, 20, 1, 'uhsdbfsd', 2, '42.00', '42900.00', '', 1, '2015-09-04 15:28:01', '2015-09-04 15:28:01', NULL),
(34, 21, 1, 'Marcos Geller', 2, '49900.00', '49900.00', '', 1, '2015-09-04 20:43:22', '2015-09-04 20:43:22', NULL),
(35, 6, 1, 'hfhgc', 2, '20990.00', '20990.00', '', 1, '2015-09-04 20:44:16', '2015-09-04 20:44:16', NULL),
(36, 17, 1, 'yfhdmh', 2, '35990.00', '35990.00', '', 1, '2015-09-04 20:46:32', '2015-09-04 20:46:32', NULL),
(37, 18, 1, 'hdcc ', 2, '38990.00', '38990.00', '', 1, '2015-09-04 20:47:06', '2015-09-04 20:47:06', NULL),
(38, 19, 1, 'uf.ig.ug', 2, '39990.00', '39990.00', '', 1, '2015-09-04 20:47:47', '2015-09-04 20:47:47', NULL),
(39, 4, 1, 'bjb,jh kug m', 2, '34990.00', '34990.00', '', 1, '2015-09-04 20:54:47', '2015-09-04 20:54:47', NULL),
(41, 2, 1, 'RAUL VELIT', 2, '40990.00', '36900.00', '', 1, '2015-09-10 14:49:19', '2015-09-10 14:49:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalog_cars`
--

CREATE TABLE IF NOT EXISTS `catalog_cars` (
  `id` int(10) unsigned NOT NULL,
  `manufacture_year` int(10) unsigned NOT NULL,
  `model_year` int(10) unsigned NOT NULL,
  `cylinder` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transmission` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seats` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fuel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `version_id` int(10) unsigned NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image2` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_image3` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image3` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description_image4` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image4` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `catalog_cars`
--

INSERT INTO `catalog_cars` (`id`, `manufacture_year`, `model_year`, `cylinder`, `transmission`, `seats`, `fuel`, `version_id`, `currency_id`, `price`, `image`, `image1`, `image2`, `description_image3`, `image3`, `description_image4`, `image4`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2015, 2015, '3500 c.c.', 'automatic', '8', 'gasoline', 20, 2, '49990.00', '', 'ODYSSEY.jpg', 'ODYSSEYp.jpg', 'Espacio para 8 pasajeros', 'IntOdyssey1.jpg', 'Amplio Espacio ', 'IntOdyssey2.jpg', '2015-07-08 21:10:33', '2015-07-08 21:11:09', NULL),
(2, 2015, 2015, '2,400 c.c. 4 Cilindros', 'AUTOMATICA CVT 4WD', '5', 'gasolina', 16, 2, '40990.00', '', 'honda-crv-2015 frente.jpg', '', 'Spoiler Posterior', 'TAILGATE-SPOILER_mid.jpg', 'Airbags completos', '2015-Honda-CR-V-14-850x349.jpg', '2015-07-15 21:18:12', '2015-07-23 18:15:25', NULL),
(3, 2015, 2015, '2,400 c.c. 4 Cilindros', 'AUTOMATICA CVT 2WD', '5', 'GASOLINA', 13, 2, '31990.00', '', 'c7f98913025aa45951d92d6e83c5759f.png', 'a58df90c89602c377ede63f8a98f483a.png', 'TABLERO ERGONÓMICO', '2015-Honda-CR-V-SUV-LX-4dr-Front-wheel-Drive-Photo-9.png', 'AMPLIO ESPACIO INTERIOR', 'interior crv tolva.jpg', '2015-07-15 22:20:50', '2015-07-30 15:28:09', NULL),
(4, 2015, 2015, '2,400 c.c. - 4 cilindros', 'AUTOMATICA CVT 2WD', '5', 'GASOLINA', 15, 2, '34990.00', '', 'honda-crv-2015 frente.jpg', '', 'PANTALLA TOUCH SCREEN', '2015-Honda-CR-V-Touring-interior.jpg', 'GRAN ESPACIO INTERIOR', 'interior crv tolva.jpg', '2015-07-23 18:08:35', '2015-08-05 23:13:58', NULL),
(5, 2015, 2015, '1,500 c.c.   4 cilindros en linea', 'Mecánica 6 velocidades', '5', 'GASOLINA', 1, 2, '18990.00', '', 'novo-honda-fit-2015-14.jpg', 'Fitpost.jpg', 'Modo Refresh', 'FITint.jpg', 'Sistema Magic Seat', 'Honda-Fit-2015-ELX-interior-10.jpg', '2015-07-23 21:08:39', '2015-07-30 15:27:31', NULL),
(6, 2015, 2015, '1,500 c.c.   4 cilindros en linea', 'AUTOMATICA CVT', '5', 'GASOLINA', 2, 2, '20990.00', '', 'novo-honda-fit-2015-14.jpg', 'Fitpost.jpg', 'Modo Refresh', 'FITint.jpg', 'Sistema Magic Seat', 'Honda-Fit-2015-ELX-interior-10.jpg', '2015-07-23 21:10:54', '2015-07-30 15:27:21', NULL),
(7, 2015, 2015, '1,500 c.c.   4 cilindros en linea', 'AUTOMATICA CVT', '5', 'GASOLINA', 3, 2, '23990.00', '', 'Fitdel.jpg', 'Fitpost.jpg', 'Modo Refresh', 'FITint.jpg', 'Pantalla 5'''' con cámara de retro', '2015-honda-fit-ex-interior-photo-607678-s-1280x782.jpg', '2015-07-23 21:13:42', '2015-07-30 15:27:05', NULL),
(8, 2015, 2015, '1,800 c.c.  -  4 cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 4, 2, '25990.00', '', 'civic sedan del.jpg', 'civic sedan post.jpg', 'TABLERO ERGONÓMICO', '', 'doble panel de instrumentos', 'civiccopit.jpg', '2015-07-30 14:33:26', '2015-07-30 15:26:32', NULL),
(9, 2015, 2015, '1,800 c.c.  -  4 cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 5, 2, '28990.00', '', 'civic sedan del.jpg', 'civic sedan post.jpg', 'Diseño deportivo', 'civiccopit.jpg', 'Mandos en el Timón', 'civic timon.jpg', '2015-07-30 14:36:49', '2015-08-14 23:55:44', NULL),
(10, 2015, 2015, '1,800 c.c.  -  4 cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 7, 2, '28990.00', '', '2013-honda-civic-si-coupe.jpg', 'civic coupe lat.jpg', 'tablero ergonomico', 'honda-civic-2015-interior-69tbzitl.png', 'gran espacio interior', 'int civic.jpg', '2015-07-30 14:43:57', '2015-08-05 17:20:30', NULL),
(11, 2015, 2015, '2,400 c.c. 4 Cilindros', 'Mecánica 6 velocidades', '5', 'GASOLINA', 6, 2, '35990.00', '', 'si lat1.jpg', 'si lat 2.jpg', 'interior', 'INT civic si.jpg', 'Panel en rojo', 'panel si.jpg', '2015-07-30 14:50:43', '2015-07-30 15:25:43', NULL),
(12, 2015, 2015, '2,400 c.c. 4 Cilindros', 'Mecánica 6 velocidades', '5', 'GASOLINA', 8, 2, '35990.00', '', '2013-honda-civic-si-sedan-17_318.jpg', '2014-honda-civic-si-coupe-6_600x0w.jpg', 'panel de instrumentos', '2015-honda-civic-si-sedan-instrument-panel.jpg', 'Asientos exclusivos', 'black-red_fabric_si_sedan_int.jpg', '2015-07-30 15:05:40', '2015-07-30 15:25:31', NULL),
(13, 2015, 2015, '3500 c.c.', 'AUTOMATICA ', '8', 'GASOLINA', 17, 2, '43990.00', '', 'pilot lx 2015.jpg', '2015-Honda-Pilot-Exterior1.jpg', 'Fila Posterior', 'interior pilot.jpg', 'Panel de Instrumentos', '2015_honda_pilot_dashboard.jpg', '2015-07-30 15:16:29', '2015-07-30 15:16:29', NULL),
(14, 2015, 2015, '3500 c.c.', 'AUTOMATICA ', '8', 'GASOLINA', 18, 2, '49990.00', '', 'pilot2015.jpg', '', 'panel de instrumentos', '2015_honda_pilot_dashboard.jpg', 'GRAN ESPACIO INTERIOR', 'cargo.jpg', '2015-07-30 15:25:00', '2015-07-30 15:25:00', NULL),
(15, 2015, 2015, '3500 c.c.', 'AUTOMATICA ', '8', 'GASOLINA', 19, 2, '52990.00', '', 'pilot touring.jpg', 'pilot touring2.jpg', '8 PLAZAS PARA ADULTOS', 'pilotint.jpg', 'GRAN ESPACIO INTERIOR', 'cargo.jpg', '2015-07-30 16:16:22', '2015-07-30 16:16:22', NULL),
(16, 2015, 2015, '2,400 c.c. 4 Cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 9, 2, '32990.00', '', '2015_honda_accord EX.jpg', '2014-honda-accord-sedan-front3_0.jpg', 'Espacio para  pasajeros', '2015_honda_accord_sedan_ex-l_i_oem_1_600.jpg', 'Panel de Instrumentos', '2014-honda-accord-sedan-4-door-v6-auto-ex-l-temperature-controls_100440777_m.jpg', '2015-07-30 21:43:24', '2015-07-30 21:43:24', NULL),
(17, 2015, 2015, '2,400 c.c. 4 Cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 10, 2, '35990.00', '', '3608.png', '896.jpg', 'pantalla tactil', '2014-honda-accord-sedan-4-door-v6-auto-ex-l-temperature-controls_100440777_m.jpg', 'espacio interior', '2015-honda-accord-sedan-interior.jpg', '2015-07-30 21:52:54', '2015-07-30 21:52:54', NULL),
(18, 2015, 2015, '3,500 c.c.    -  v6 6 cilindros', 'AUTOMATICA ', '5', 'GASOLINA', 11, 2, '38990.00', '', '3608.png', 'black-Accord-360-panel05.png', 'Lane Watch', 'lanewatch.jpg', 'tablero delantero', 'lead14-2014-honda-accord-v6-touring-review.jpg', '2015-07-30 23:01:43', '2015-07-30 23:01:43', NULL),
(19, 2015, 2015, '3,500 c.c.    -  6 cilindros en V', 'AUTOMATICA ', '5', 'GASOLINA', 12, 2, '39990.00', '', '2015-honda-accord-front-three-quarter-02.jpg', 'ACCORD COUPE.jpg', 'TECNOLOGIA Y CONFORT', '2015-Honda-Accord-Sedan-Sport-Front-Interior.jpg', 'TECNOLOGIA EARTH DREAM', 'honda-accord-touring-2013-i3.jpg', '2015-07-30 23:06:56', '2015-07-31 14:46:42', NULL),
(20, 2015, 2016, '3,500 c.c.    -  6 cilindros en V', 'AUTOMATICA ', '8', 'GASOLINA', 21, 2, '42.00', '', '2016-Honda-Pilot-PLACEMENT-626x382.jpg', '2016-Honda-Pilot-pr-r-598.jpg', 'NOVEDOSO TABLERO DE CONTROL', '2016-Honda-Pilot-15.jpg', 'AREA DE CARGA AMPLIA', '2016-honda-pilot-cargo-area.jpeg', '2015-09-04 14:03:01', '2015-09-04 15:27:32', NULL),
(21, 2015, 2016, '3,500 c.c.    -  6 cilindros en V', 'AUTOMATICA ', '8', 'GASOLINA', 22, 2, '49900.00', '', '2016-honda-pilot-redesign-pictures.jpg', '2016-Honda-Pilot-pr-r-598.jpg', 'Novedoso panel de instrumentos', '2016-Honda-Pilot-15.jpg', 'gran espacio interior', '2016-Honda-Pilot-Interior.jpg', '2015-09-04 20:29:42', '2015-09-04 20:29:42', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'REPUESTOS', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL);

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
  `with_check` tinyint(1) NOT NULL DEFAULT '0',
  `column_two` tinyint(1) NOT NULL DEFAULT '0',
  `pre_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `post_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `checkitems`
--

INSERT INTO `checkitems` (`id`, `name`, `checkitem_group_id`, `with_status`, `with_value`, `with_check`, `column_two`, `pre_value`, `post_value`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Faros delanteros (Revisar focos)', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:27:02', '2015-08-14 20:40:26', NULL),
(2, 'Faros traseros (Revisar focos)', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:31:26', '2015-08-14 20:40:26', NULL),
(3, 'Luces interiores', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:26', NULL),
(4, 'Rociador de parabrisas', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:26', NULL),
(5, 'Operación y estado de plumillas', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:26', NULL),
(6, 'Freno de estacionamiento', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:26', NULL),
(7, 'Salida de aire frio en el aire acondicionado', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:27', NULL),
(8, 'Operación del claxon', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:27', NULL),
(9, 'Estado del filtro de polvo y polen', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 20:40:27', NULL),
(10, 'Jebes de plumilla', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:48', '2015-08-14 18:52:48'),
(11, 'Estado del parabrisas', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:48', '2015-08-14 18:52:48'),
(12, 'Freno de estacionamiento', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:49', '2015-08-14 18:52:49'),
(13, 'Operación del claxon', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:49', '2015-08-14 18:52:49'),
(14, 'Operación del embrague (si aplica)', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:49', '2015-08-14 18:52:49'),
(15, 'Estado del filtro de polvo y polen', 1, 1, 0, 0, 0, '', '', '2015-07-07 06:38:03', '2015-08-14 18:52:49', '2015-08-14 18:52:49'),
(16, 'Estado de la batería (Revisar prueba adjunta)', 2, 1, 0, 0, 0, '', '', '2015-07-07 06:39:13', '2015-08-14 18:54:00', NULL),
(17, 'Fluido refrigerante', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:39', NULL),
(18, 'Fluido de dirección hidráulica (Si aplica)', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:39', NULL),
(19, 'Fluido de frenos', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:39', NULL),
(20, 'Fluido de lavado del parabrisas', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:39', NULL),
(21, 'Fluido de transmisión automatica (Si aplica)', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', NULL),
(22, 'Estado de filtro de aire', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', NULL),
(23, 'Estado de correas de transmisión', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', NULL),
(24, 'Estado de mangueras de radiador', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', NULL),
(25, 'Fluido hidraulico de embrague (Si aplica)', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', NULL),
(26, 'Líneas de freno/Mangueras/Cable del freno de estacionamiento', 3, 1, 0, 0, 0, '', '', '2015-07-07 06:47:10', '2015-08-14 18:55:40', '2015-08-14 18:55:40'),
(27, 'Estado de los amortiguadores y suspensión', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(28, 'Estado de trapecios', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(29, 'Estado de creamallera de direccion y retenes de polvo', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(30, 'Estado de sistema de escape', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(31, 'Fugas de aceite de motor y/o fluidos', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(32, 'Estado de guardapolvos de palier', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', NULL),
(33, 'Estado de los ponchos y puntas de palier', 4, 1, 0, 0, 0, '', '', '2015-07-07 06:50:11', '2015-08-14 18:57:28', '2015-08-14 18:57:28'),
(34, '<strong>Patrón de Desgaste</strong>', 5, 0, 0, 0, 0, '', '', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(35, 'Delantera izquierda', 5, 1, 1, 0, 0, 'Banda', '32 avos', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(36, 'Trasera izquierda', 5, 1, 1, 0, 0, 'Banda', '32 avos', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(37, 'Llanta de repuesto', 5, 1, 1, 0, 0, 'Banda', '32 avos', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(38, 'Presión de juego trasero', 5, 0, 1, 0, 0, '', 'Psi', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(39, 'Delantera Derecha', 5, 1, 1, 0, 1, 'Banda', '32 avos', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(40, 'Trasera Derecha', 5, 1, 1, 0, 1, 'Banda', '32 avos', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(41, 'Presión de juego delantero', 5, 0, 1, 0, 1, '', 'Psi', '2015-07-07 06:54:19', '2015-09-11 18:06:24', NULL),
(42, '(Limite de servicio 3.00mm)', 6, 0, 0, 0, 0, '', '', '2015-07-07 06:58:31', '2015-08-14 19:01:44', NULL),
(43, 'Delantera izquierda', 6, 1, 1, 0, 0, '', 'mm', '2015-07-07 06:58:31', '2015-08-14 19:01:44', NULL),
(44, 'Trasera izquierda', 6, 1, 1, 0, 0, '', 'mm', '2015-07-07 06:58:31', '2015-08-14 19:01:45', NULL),
(45, 'Los frenos no fueron inspeccionados en esta vista', 6, 1, 0, 0, 0, '', '', '2015-07-07 06:58:31', '2015-08-14 19:01:45', NULL),
(46, 'Delantera derecha', 6, 1, 1, 0, 1, '', 'mm', '2015-07-07 06:58:31', '2015-08-14 19:01:45', NULL),
(47, 'Trasera derecha', 6, 1, 1, 0, 1, '', 'mm', '2015-07-07 06:58:31', '2015-08-14 19:01:45', NULL),
(48, 'Alineamiento de dirección', 7, 1, 0, 0, 0, '', '', '2015-08-14 19:04:54', '2015-08-14 20:41:27', NULL),
(49, 'Balanceo de ruedas', 7, 1, 0, 0, 0, '', '', '2015-08-14 19:04:54', '2015-08-14 20:41:27', NULL),
(50, 'La prueba de manejo no se realizó en esta visita', 7, 1, 1, 0, 0, '', '', '2015-08-14 19:04:54', '2015-08-14 20:41:27', NULL),
(51, 'Inspección final de operaciones', 7, 1, 1, 0, 0, '', '', '2015-08-14 19:04:54', '2015-08-14 20:41:27', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `checkitem_groups`
--

INSERT INTO `checkitem_groups` (`id`, `name`, `order`, `is_double_column`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'INTERIOR/ EXTERIOR', 1, 0, '2015-07-07 06:27:02', '2015-08-14 20:40:26', NULL),
(2, 'BATERÍA', 2, 0, '2015-07-07 06:39:13', '2015-08-14 18:54:00', NULL),
(3, 'DEBAJO DEL CAPÓ', 3, 0, '2015-07-07 06:39:40', '2015-08-14 18:55:39', NULL),
(4, 'DEBAJO DEL AUTO', 4, 0, '2015-07-07 06:50:10', '2015-08-14 18:57:28', NULL),
(5, 'CONDICIÓN DE LLANTAS', 5, 1, '2015-07-07 06:54:19', '2015-07-07 06:58:39', NULL),
(6, 'CONDICIÓN DE PASTILLAS', 6, 1, '2015-07-07 06:58:31', '2015-08-14 19:01:44', NULL),
(7, 'PRUEBA DE MANEJO Y/O INSPECCIÓN FINAL', 7, 0, '2015-08-14 19:04:54', '2015-08-14 20:41:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colors`
--

CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `in` tinyint(1) NOT NULL,
  `out` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `colors`
--

INSERT INTO `colors` (`id`, `name`, `in`, `out`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'NEGRO', 1, 1, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(2, 'AZUL', 1, 1, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(3, 'BEIGE', 1, 1, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(10) unsigned NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `brand_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paternal_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maternal_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_id` int(10) unsigned NOT NULL,
  `doc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ubigeo_id` int(10) unsigned NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `birth` date DEFAULT NULL,
  `is_provider` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `company_name`, `brand_name`, `name`, `paternal_surname`, `maternal_surname`, `id_type_id`, `doc`, `address`, `ubigeo_id`, `phone`, `mobile`, `email`, `birth`, `is_provider`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SIN CLIENTE', '', '', '', '', 1, '10000000001', 'CAL. JUAN DE ARONA NRO. 830 SAN ISIDRO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(2, 'LA POSITIVA SEGUROS Y REASEGUROS', '', '', '', '', 1, '20100210909', 'CAL. FRANCISCO MASIAS NRO. 370 (CRUCE CON AV. JAVIER PRADO ESTE) SAN ISIDRO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(3, 'RIMAC SEGUROS Y REASEGUROS', '', '', '', '', 1, '20100041953', 'AV. PASEO DE LA REPUBLICA NRO. 3505 INT. P-11 SAN ISIDRO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(4, 'MAPFRE PERU COMPAÃ‘IA DE SEGUROS Y REASEGUROS S.A.', '', '', '', '', 1, '20202380621', 'AV. 28 DE JULIO NRO. 873 URB. MIRAFLORES MIRAFLORES LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(5, 'LIMAUTOS AUTOMOTRIZ DEL PERU S.A.C.', '', '', '', '', 1, '20537942381', 'AV. REPUBLICA DE PANAMA NRO. 4628 (PISO 1) SURQUILLO LIMA LIMA', 127814137, '6129292', '-', '-', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(7, 'DEL CASTILLO ZEVALLOS IDA GRACIELA', '', '', '', '', 2, '40218785', 'CA. EULER #150 BRE', 127814137, '9875079', '989381974', 'graciela@hotmail.com ', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(8, 'FERNANDEZ SAETTONE DANIEL ', '', '', '', '', 2, '99999999', '  LIMA LIMA', 127814137, '', '', 'danielfermor@speedy.com.pe', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(9, 'LUIS AUGUSTO  GARCIA ', '', '', '', '', 1, '20100041953', 'av. PASEO DE LA REPUBLICA PANAMA URB. - ATE LIMA LIMA', 127814137, '111111', '956602845', '-', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(12, 'RIVERA DEL CARPIO MEZA MATEO', '', '', '', '', 2, '6183549', 'MANZANILLABLOCK G DPTO.208 URB. MANZANILLABLOCK G DPTO.208 LIMA LIMA LIMA', 127814137, '3256302', '994962574', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(13, 'LUIS GUSTAVO QUIROZ', '', '', '', '', 2, '42400965', 'CALLE LOS SAUCOS 108 URB. SALAMANCA ATE LIMA LIMA', 127814137, '', '986611509', 'Gustavoquirozmory@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(14, 'WALTER URIBE ARROYO', '', '', '', '', 2, '40176624', 'AV: AVIACION 1689 URB. AV: AVIACION 1689 SAN LUIS LIMA LIMA', 127814137, '940281022', '940281022', 'WURIBE21@HOTMAIL.COM', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(15, 'SUSSY SOLANO DOMINGUEZ', '', '', '', '', 2, '44868305', 'URB. - SAN BORJA LIMA LIMA', 127814137, '-', '-', '-', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(16, 'MIGUEL HUAYANAY ALVARADO', '', '', '', '', 2, '45937982', ' LIMA LIMA LIMA', 127814137, '', '995988606', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(17, 'NAVARRO VERA CARLOS CESAR ', '', '', '', '', 2, '9839651', 'AV.PACHACUTEC 4533 VILLA MARIA DEL TRIUNFO LIMA LIMA', 127814137, '', '981599151', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(18, 'ROSALES CASTILLO HILDANELLY', '', '', '', '', 2, '257210', 'AV. PASEO DE LA REPUBLICA MZ.C LOTES   DPTO .204 URB. URBANIZACION LOS ROSALES SANTIAGO DE SURCO LIM', 127814137, '', '998228779', 'hildanellycastillo18@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(19, 'TORRES CARLOS GLORIA GIOVANNA', '', '', '', '', 2, '10180712', 'JIRON RIO PIURA NRO 601 DPTO 103 URB. JIRON RIO PIURA SAN LUIS LIMA LIMA', 127814137, '3255335', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(20, 'ENRIQUE RAMIRO CHAMAN RICCI', '', '', '', '', 2, '22316141', 'Jr LORETO 561 DPTO 112 BREÃ‘A LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(21, 'CARLA DENISE ABAÃ‘EZ BURGOS', '', '', '', '', 2, '41992940', 'CALLE OSMOS 437 DPTO.101 URB. LAS GARDENIAS SANTIAGO DE SURCO LIMA LIMA', 127814137, '', '998102703', 'carla.ibanez@nextel.com.pe', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(22, 'JORGE CHISTIAN PAREDES CHAVEZ', '', '', '', '', 2, '44773868', 'calle manuel perez de tudela 232 dpto h SAN ISIDRO LIMA LIMA', 127814137, '987956218', '', 'jorgepc87@gmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(23, 'GARCES BARRIOS LUIS ALBERTO ', '', '', '', '', 2, '7269564', 'CALLE ACAPULCO  MZ B 1 LT- 8 LA MOLINA LIMA LIMA', 127814137, '998663951', '998663951', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(24, 'JORGE SAMANIEGO CASTRO', '', '', '', '', 2, '43461971', ' LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(25, 'ORO TEXTIL SANTA MARIA S.A.C ', '', '', '', '', 1, '20515496271', 'CALLE PICASSO NRO 164 SAN BORJA LIMA LIMA', 127814137, '', '981377318', 'ALMACEN@OROTEXTIL.COM.PE', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(26, 'KIA IMPORT PERU S.A.C ', '', '', '', '', 1, '20472468147', 'AV. REPUBLICA DE PANAMA SURQUILLO LIMA LIMA', 127814137, '0', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(27, 'MECHAN CHAVEZ PERCY JOEL', '', '', '', '', 2, '10109634', 'CALLE CA CARACAS 159 SANTA PATRICIA LA M LA MOLINA LIMA LIMA', 127814137, '3491120', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(28, 'EDDY EDUARDO MIMBELA TREVEJO ', '', '', '', '', 2, '41298362', 'CALLE D MZ K LT25 ASOCC V URB. CALLE D MZ K LT25 ASOCC V INDEPENDENCIA LIMA LIMA', 127814137, '', '987207361', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(29, 'LUIS FRANCISCO GUILLERMO VILLA FAJARDO ', '', '', '', '', 2, '43315340', 'JR. 25 DE AMRZO MZ 15 LOT 11A AA.HH. NUEVO LURIN LURIN LIMA LIMA', 127814137, '430-2191', '986213478', 'lufranckvifa@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(30, 'JAVIER ALONSO BEDOYA DE VIVANCO ', '', '', '', '', 2, '3117576', 'AV. LA PLANICIE URB. AV. LA PLANICIE LA MOLINA LIMA LIMA', 127814137, '3117576', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(31, 'RIVAS CHACALTANA JOSE ANTONIO', '', '', '', '', 2, '430066275', 'PSJ LA CANTERA  107 SAN JUAN DE LURIGANCHO LIMA LIMA', 127814137, '2762724', '999614112', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(32, 'JUAN GUILLEN', '', '', '', '', 2, '4310437', '  LIMA LIMA', 127814137, '', '946231507', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(33, 'GUTARRA ALVAREZ MELCHOR', '', '', '', '', 2, '7808693', 'CALLE CIPRIANO AGERO 123 MIRAFLORES LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(34, 'ROMERO FERNANDEZ LUIS ANGEL', '', '', '', '', 2, '6793930', 'AV. BRASIL NÂ° 2760 DPTO 1402 PISO 14 PUEBLO LIBRE LIMA LIMA', 127814137, '', '993352270', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(35, 'HUAMAN CAMPOS CARLOS ANTONIO', '', '', '', '', 2, '43368798', 'CALLE LAS ROCAS 140 URB. LO  LIMA LIMA', 127814137, '', '992350874', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(36, 'ROXANA ESTRELLA RIVERA HONORES', '', '', '', '', 2, '42954334', 'CESAR VALLEJO 104 DEP 502 LA MOLINA LIMA LIMA', 127814137, '3492911', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(37, 'AUTOSERVICIOS CIRO HERRERA S.A.C.', '', '', '', '', 1, '20108727072', 'CALLE DOÃ‘A ANA MZ G  LOTE 19 URB. LOS ROSALES SANTIAGO DE SURCO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(38, 'ERNESTO ANGEL LLAGAS REYES ', '', '', '', '', 2, '15218168', 'Mz d LOTE 12 Sarita Colonia zapallal VENTANILLA  LIMA LIMA', 127814137, '', '964357490', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(39, 'JORGE CRISOLOGO DURAND', '', '', '', '', 2, '42034153', 'URB. SAN BORJA SUR URB. CALLE BERRUGUETE 150 URB. SAN BORJA SAN BORJA LIMA LIMA', 127814137, '987704864', '', 'jcrisologod@gmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(40, 'ROBERTO CAMA', '', '', '', '', 2, '47621477', ' URB. CA ANCASH  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(41, 'IBAÃ‘EZ BURGOS JUAN FERNANDO', '', '', '', '', 2, '43152393', 'CALLE COSMOS 437 DPTO 101 URB. CALLE COSMOS 437 DPTO 101 SANTIAGO DE SURCO LIMA LIMA', 127814137, '', '987986948', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(42, 'CUPE RODRIGUEZ JEIMY VICTORIA', '', '', '', '', 2, '41206203', 'CALLE 18 MZ A1 LT.35 URB. PRADERAS DE SANTA ANITA II ETAPA SANTA ANITA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(43, 'CALDERON ALVAREZ JOSE ALBERTO ', '', '', '', '', 2, '41766978', 'CALLE LOMA PONCIANA 253 SANTIAGO DE SURCO LIMA LIMA', 127814137, '', '971004007', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(44, 'CHUQUIZITA PAREDE CRISTHIAN LUIS', '', '', '', '', 2, '41234294', 'PJ. 49 S/N MZ C1 LT 36 URB. BOCANEGRA SECT 2  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(45, 'JORGE ALEJANDRO CONDORI CHEICO', '', '', '', '', 2, '9836483', 'COMITE 5 LOTE 30 URB. P. JOVEN SAN FRANCISCO ATE LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(46, 'VICTOR PICUAGA', '', '', '', '', 2, '7842141', ' URB. AV ROCA Y BOLOGNA  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(47, 'NADER GOERGE QUMSIYEH AL BADAWIL', '', '', '', '', 2, '10218561', 'AV. RINCONADA DEL LAGO 1480 LA MOLINA LIMA LIMA', 127814137, '3481424', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(48, 'ROXANA PALOMINO BRIONES ', '', '', '', '', 2, '9829215', 'CA CARLOS VILLARAN 140 LA VICTORIA LIMA LIMA', 127814137, '', '997531147', 'ROXANA.PALOMINO@YAHOO.ES', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(49, 'HUAMAN YAPIA FREDY', '', '', '', '', 2, '43730077', 'AV JOSE GALVEZ 718  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(50, 'LUQUE MAMANI HAROLD ERNESTO', '', '', '', '', 2, '43651481', 'JIRON ALONSO DE RINCON 1636 LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(51, 'ALEX MANUEL JURURO BORDA', '', '', '', '', 2, '40219982', 'CALLE TIAHUANACO 135 DPTO 302 ETAPA II URB. PORTADA DEL SOL DE LA MOLINA LA MOLINA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(52, 'KLOTZ S.A.C', '', '', '', '', 1, '20549661301', 'CAL. H MZA. G LOTE. 8 DPTO. 201 URB. SIRIUS URB. LA MOLINA LIMA LIMA LIMA', 127814137, '', '992169166', 'joseluis.rodriguez.luna@gmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(53, 'SUPER STAR SECURITY S.A.C.', '', '', '', '', 1, '20371048686', 'MZA. Z1 LOTE. 15 URB. QUINTA TRISTAN JOSE LUIS BUSTAMANTE Y RIVERO AREQUIPA AREQUIPA', 127814137, '3724965', '959302964', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(54, 'SOLANO DOMINGUEZ YOHN MICHAEL', '', '', '', '', 2, '43106663', 'CALLE 2 MZ C LT 23 URB. SANTA ROSITA LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(55, 'TORPOCO HUAYLLANI CINTYA PAOLA', '', '', '', '', 2, '44055250', 'AV. BRASIL 2760 DPTO 603 BREÃ‘A LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(56, 'BALDEON BERRIOS FERNANDO ', '', '', '', '', 2, '42425888', 'ASOCIACION 13 DE FEBRERO MZ.A.LT.17. ATE ATE LIMA LIMA', 127814137, '', '989097214', 'fernando.baldeon@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(57, 'VICTOR RENATO RODRIGUEZ AGUILA', '', '', '', '', 3, '45856', '  LIMA LIMA', 127814137, '618-7576', '999947619', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(58, ' FUENTES NEYRA FERNANDO ', '', '', '', '', 2, '9392936', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(59, 'SOTO GUARNIZ LEONARDO ALFREDO', '', '', '', '', 2, '43766893', 'JR. ENRIQUE VILLAR # 520  LIMA LIMA', 127814137, '', '990286851', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(60, 'FAGA MOTORS S.A.', '', '', '', '', 1, '20519093724', 'JR. LAMPA 972 - LIMA 1 LIMA LIMA LIMA', 127814137, '512-0500', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(61, 'RD RENTAL S.A.C', '', '', '', '', 1, '20517668657', 'CAL. 2 MZA. C LOTE. 06 URB. LA MERCED ATE LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(62, 'SERGIO POBLETE GRILLO', '', '', '', '', 2, '40458636', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(63, 'MARGARITO MARIANO LLACSAHUANCA PAUCAR', '', '', '', '', 2, '9471847', 'PSJ. AREQUIPA 163 COOP. PABLO VI COMAS LIMA LIMA', 127814137, '', '977227278', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(64, 'EMPRESA DE SERVICIOS TURISTICOS GOLD S.A.C.', '', '', '', '', 1, '20508157224', 'AV. EL SOL NRO 1574 INT. F CHORRILLOS LIMA LIMA', 127814137, '', '993420683', 'pagh1817@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(65, 'MORENO MURGIA, JUAN FRANCISCO', '', '', '', '', 2, '3428298', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(66, 'GALLARDO ALARCON LUIS ALBERTO ', '', '', '', '', 2, '15389477', 'AV LOS MOCHICAS NRO 214 PISO 2 URB. SALAMANCA ATE LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(67, 'MARIO LU FREUNDT', '', '', '', '', 2, '7509339', 'CALLE OSWALDO HERCELLES 326 LA VICTORIA LIMA LIMA', 127814137, '', '997101219', 'MLU@CLARO.COM.PE', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(68, 'JACQUELINE CHUQUILLANQUI ', '', '', '', '', 2, '42833257', 'CALLE 6 MZ E LOTE 1 URB. LA MERCED ATE LIMA LIMA', 127814137, '', '995954456', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(69, 'VMOTORS', '', '', '', '', 1, '', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(70, 'FALEROBOJORQUEZ NILTON CHISTIAN', '', '', '', '', 2, '15862001', ' URB. MZTT 2 LT 53 URB LA FLORESTA DE PRO LOS OLIVOS LIMA LIMA', 127814137, '', '989113989', 'cristianfalero.nb@gmai.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(71, 'MOSQUERA ESPINOZA JAMES HENRY', '', '', '', '', 2, '41146637', 'NEBULOSAS 2610 COOP LOS ANGELES SAN JUAN DE LURIGANCHO LIMA LIMA', 127814137, '997683346', '997683346', 'JMOSQUERAE@PUCPE.PE', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(72, 'MOSQUERA ESPINOZA JAMES HENRY', '', '', '', '', 2, '41146637', ' URB. JR NEBULOSAS 2610 COOP LOS ANGELES SAN JUAN DE LURIGANCHO LIMA LIMA', 127814137, '', '997683346', 'JMOSQUERAE@PUCPE.PE', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(73, 'YARLEQUE RENTERIA LUIS ALBERTO ', '', '', '', '', 2, '3892287', 'MZ.54 LT.17 3RA ZONA BAYOBAR URB. MZ.54 LT.17 3RA ZONA BAYOBAR  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(74, 'LIMAUTOS AUTOMOTRIZ DEL PERU S.A.C', '', '', '', '', 1, '20537942381', 'CALLE LOS HORNOS 353 URB. NARANJAL LOS OLIVOS LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(75, 'ENRIQUE ARTURO MILLONES LOAYZA ', '', '', '', '', 2, '43250638', 'JR NEBULOSA 2694 SAN JUAN DE LURIGANCHO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(76, 'CARLO RAMIREZ', '', '', '', '', 1, '', 'xxxxx URB. XXXXX  LIMA LIMA', 127814137, '987566334', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(77, 'DIAS SILVA JOSE LUIS ', '', '', '', '', 1, '7729749', 'santa patricia URB. JR . CURAZAO 347 URB .  LIMA LIMA', 127814137, '4283100', '999535671', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(78, 'BENEDETE DYER SERGIO', '', '', '', '', 2, '29600468', 'CALLE LOS APACHES 170 BLOCK SANTIAGO DE SURCO LIMA LIMA', 127814137, '966955380', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(79, 'ADOLFO TORRES GUERRA', '', '', '', '', 2, '12458578', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(80, 'SERVICIOS POSTALES DEL PERU SOCIEDAD ANONIMA ', '', '', '', '', 1, '20256136865', 'AV. TOMAS VALLE CD.7 NRO. SN (ALTURA CUADRA 7 AV. TOMAS VALLE) LOS OLIVOS LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(81, 'VICTOR MENDOZA CHAMPI', '', '', '', '', 2, '9330401', 'SAN BORJA SAN BORJA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(82, 'RAMIREZ PALMA FELIX', '', '', '', '', 2, '25540826', '  LIMA LIMA', 127814137, '', '989025724', 'framirez@abengoaperu.com.pe', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(83, 'EDDY MIMBELA TRIVEJO', '', '', '', '', 2, '41298362', '  LIMA LIMA', 127814137, '', '987207361', 'eddy.mimbela@outlook.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(84, 'RAMUSSEN OCHOA JORGE RODOLFO', '', '', '', '', 2, '25857209', 'AV: SALAVERRY 3769 URB. AV: SALAVERRY 3769 SAN ISIDRO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(85, 'MARIO NOGUEROL ', '', '', '', '', 1, '20472468147', 'AV: REPUBLICA PANAMA URB. AV: REPUBLICA PANAMA SURQUILLO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(86, 'RONY ORTIZ MUNGUIA', '', '', '', '', 2, '9561665', '  LIMA LIMA', 127814137, '', '995734903', 'rony_ortiz@lim.ajinomoto.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(87, 'SILVIA CASTRO BOLANOS', '', '', '', '', 2, '29671744', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(88, 'ESTELA BEATRIZ TRELLES MEJIA', '', '', '', '', 2, '47408283', 'CALLE SAN MARTIN MZ G8 LT 19 URB. LOS CEDROS DE VILLA CHORRILLOS LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(89, 'WILMER DENIS GAMARRA RAMIREZ', '', '', '', '', 2, '99999999', '  LIMA LIMA', 127814137, '', '988085726', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(90, 'CUBA MARMANILLO SEVERO LEONIDAS ', '', '', '', '', 2, '7016616', 'AV. EL SOL  558 URB. AV. EL SOL  558 BARRANCO LIMA LIMA', 127814137, '992750176', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(91, 'CORPORACION DUX PERU S.A.C', '', '', '', '', 1, '20549290133', 'CALLE TRADICIONES 104 URB.  URB SANTA FELICIA LA MOLINA LIMA LIMA', 127814137, '994329491', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(92, 'QUELOPANA CAROLINAMILAGROS', '', '', '', '', 2, '40305112', 'CALLE LOS ALCANFORES MZC LT . 21 URB. URB. EL REMANSO LA MOLINA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(94, 'ARTURO HERMINIO CACERES GONZALES', '', '', '', '', 2, '5236969', ' COMAS LIMA LIMA', 127814137, '', '976320555', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(95, 'JOSE ANTONIO QUELOPANA SALINAS ', '', '', '', '', 2, '10612152', 'AV. LOS ALCANFORES MZ. O LT. 21 URB. AV. LOS ALCANFORES MZ. O LT. 21 LA MOLINA LIMA LIMA', 127814137, '3655666', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(96, 'PARI  COLQUI RICHARD JEAN ', '', '', '', '', 2, '4082568', 'urb. rosal de salamanca URB. EDF. D DPTO 202 ATE LIMA LIMA', 127814137, '4369658', '983457593', 'rjparic@gamil.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(97, 'CARLOS PUENTE DE LA VEGA MELENDEZ', '', '', '', '', 2, '', ' LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(99, 'ANA MARIA VARGAS MENACHO', '', '', '', '', 2, '6989294', '  LIMA LIMA', 127814137, '', '999220369', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(100, 'COLETTI PAREJA STEPHANIE ROCIO', '', '', '', '', 2, '43662753', 'CALLE IVAN HUERTAS NRO 251 SANTIAGO DE SURCO LIMA LIMA', 127814137, '2748280', '', 'scoletti24@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(101, 'RIVERA DIESEL S.A.', '', '', '', '', 1, '20118201401', 'CAL. 2 MZA. C LOTE 06 URB. LA MERCED ATE LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(102, 'JOSE ALBERTO QUELOPANA', '', '', '', '', 2, '', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(103, 'LIONEL ENRIQUE GOMEZ HUACCHO', '', '', '', '', 1, '43339127', 'AV. NICOLAS AYLLON CHACLACAYO LIMA LIMA', 127814137, '', '987951569', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(104, 'VEGA MATIAS OSMAR DENNYS', '', '', '', '', 1, '10426732233', 'AV. LA CAPILLA NRO. 1137 RIMAC LIMA LIMA', 127814137, '', '980683468', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(105, 'CASTILLO MONTENGRO ERIKA MERCEDES', '', '', '', '', 2, '9753433', 'santa patricia URB. RODRIGO DE TRIANA 121 SANTA PATRICI  LIMA LIMA', 127814137, '992768479', '992768479', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(106, 'JULIO CESAR AYALA MOLINA', '', '', '', '', 2, '9865865', ' LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(107, 'ADASA SISTEMAS S.A.', '', '', '', '', 1, '20547358636', 'AV. JORGE CHAVEZ 263 OF. 201 MIRAFLORES LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(108, 'KENNY SAUL MATIAS SANTOS', '', '', '', '', 2, '42760904', '  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(109, 'VICUÃ‘A TELLO LUIS ENRIQUE', '', '', '', '', 2, '10136427', ' URB. AV. SAN BORJA SUR 173 DPTO .802 SAN BORJA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(111, 'RUBEN ROBERTO LOAYZA HUAMAN', '', '', '', '', 2, '48303870', 'lima URB. LIMA LIMA LIMA LIMA', 127814137, '944569989', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(112, 'GIULIANA ESTHER HUSRI CISNEROS', '', '', '', '', 2, '72156687', 'CALLE FELIPE PINGLO ALVA 233 URB. SIMA LIMA LIMA LIMA', 127814137, '4543411', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(113, 'LEIGH TAVARA, JAVIER ANTONIO', '', '', '', '', 2, '43583501', ' LIMA LIMA LIMA', 127814137, '', '999928560', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(114, 'COSMOS AGENCIA MARITIMA SAC', '', '', '', '', 1, '20100010136', 'JR. MARISCAL MILLER NRO. 450 INT. 901 PROV. CONST. DEL CALLAO - CALLAO  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(115, 'HERNANDO BERNEDO PAZ', '', '', '', '', 2, '29666272', ' URB. LIMA LIMA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(117, 'TIPTO RIOS EDWIN', '', '', '', '', 2, '40722959', ' SAN MARTIN COP. MZ G LT 3 COOP SAN MARTIN 3 SAN JUAN DE LURIGANCHO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(118, 'CESAR SOLORZANO', '', '', '', '', 2, '15728638', ' MIRAFLORES LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(119, 'THAIS MARIA GALLO TORRES', '', '', '', '', 2, '9383372', '  LIMA LIMA', 127814137, '', '979792869', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(121, 'LUIS ENRIQUE FUENTES BONILLA', '', '', '', '', 2, '6175301', ' LIMA LIMA LIMA', 127814137, '', '998464982', 'lfuentes21@gmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(122, 'LUIS ENRIQUE FUENTES BONILLA', '', '', '', '', 2, '6175301', 'PSJ 16 DE DICIENBRE RIMAC LIMA LIMA', 127814137, '', '998464982', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(123, 'LUIS ENRIQUE GOMEZ BUSTAMANTE', '', '', '', '', 2, '10264273', 'Jr. Rio Orinoco Mz ZÂ´ Lote 05 URB. LAS PRADERAS LA MOLINA LIMA LIMA', 127814137, '', '981068529', 'lgomez-21@hotmail.com', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(124, 'RICARDO MANUELROMERO VASQUEZ', '', '', '', '', 2, '8150131', '975127897 URB. AV. AMANCAES INDEPENDENCIA LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(125, 'JOSE LUIS RAMIREZ SIMON ', '', '', '', '', 2, '9773998', 'ALTA MZ W LT 8 URB. COMINETE VECINAL 5 ACIENDA  LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL),
(126, 'EL PACIFICO PERUANO-SUIZA CIA SEGUROS Y REASEGUROS', '', '', '', '', 1, '20100035392', 'CAL. JUAN DE ARONA NRO. 830 SAN ISIDRO LIMA LIMA', 127814137, '', '', '', NULL, 0, '2015-05-23 15:00:00', '2015-05-23 15:00:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `symbol`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'NUEVOS SOLES', 'S/.', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(2, 'DOLARES AMERICANOS', 'US$', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_types`
--

CREATE TABLE IF NOT EXISTS `document_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_sales` tinyint(1) NOT NULL,
  `to_purchases` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `document_types`
--

INSERT INTO `document_types` (`id`, `name`, `description`, `to_sales`, `to_purchases`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FACTURA', '', 1, 1, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(2, 'BOLETA', '', 1, 1, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(3, 'NOTA DE CREDITO', '', 1, 1, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(4, 'NOTA DE DEBITO', '', 1, 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paternal_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maternal_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_type_id` int(10) unsigned NOT NULL,
  `doc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `job_id` int(10) unsigned NOT NULL,
  `gender` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ubigeo_id` int(10) unsigned NOT NULL,
  `phone_personal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone_company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_personal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile_company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_personal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_company` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `signature` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `other_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id`, `name`, `paternal_surname`, `maternal_surname`, `full_name`, `id_type_id`, `doc`, `job_id`, `gender`, `address`, `ubigeo_id`, `phone_personal`, `phone_company`, `mobile_personal`, `mobile_company`, `email_personal`, `email_company`, `user_id`, `signature`, `other_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'XX', 'MARCO', 'GELLER', 'MARCO GELLER XX', 2, '12345678', 2, 0, 'DIRECCION', 12431412, '', '5554444', '', '999955555', '', '', 8, 'firma_noel.png', 0, '2015-07-10 20:17:59', '2015-07-10 20:24:19', NULL),
(2, 'NOEL', 'HUILLCA', 'HUAMANI', 'HUILLCA HUAMANI NOEL', 2, '44243484', 1, 0, 'JR. LAS GROSELLAS 910', 127814137, '', '', '', '', '', '', 1, 'firmah.jpg', 0, '2015-07-31 15:43:30', '2015-07-31 15:45:35', NULL),
(3, 'EDWIN HANS', 'RIVAS', 'CARLOS', 'RIVAS CARLOS EDWIN HANS', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 12, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(4, 'HENRRY EDWAR', 'AGUILAR', 'CORIZAPRA', 'AGUILAR CORIZAPRA HENRRY EDWAR', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 13, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(5, 'LUIS ENRIQUE', 'BERRÚ', 'DÍAZ', 'BERRÚ DÍAZ LUIS ENRIQUE', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 14, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(6, 'CARLOS', 'MARCA', 'HUASCO', 'MARCA HUASCO CARLOS', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 15, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(7, 'CARLOS ALBERTO', 'VERGARA', 'SANCHEZ', 'VERGARA SANCHEZ CARLOS ALBERTO', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 16, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(8, 'EDGAR IVÁN', 'RODRIGUEZ', 'MARCHÁN', 'RODRIGUEZ MARCHÁN EDGAR IVÁN', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 17, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(9, 'OSIBES EDISON ', 'ALARCÓN', 'FLORES', 'ALARCÓN FLORES OSIBES EDISON ', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 18, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(10, 'JHONY PABLO', 'LAGOS', 'LINARES', 'LAGOS LINARES JHONY PABLO', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 19, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(11, 'CHRISTIAN FRANCO', 'CAPCHA', 'PAREDES', 'CAPCHA PAREDES CHRISTIAN FRANCO', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 20, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(12, 'JEDY ANTHONY', 'CHACCA', 'CASTRO', 'CHACCA CASTRO JEDY ANTHONY', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 21, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(13, 'TEOFILO', 'HUAMANI', 'ALCCAHUAMAN', 'HUAMANI ALCCAHUAMAN TEOFILO', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 22, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(14, 'VICTOR HUGO', 'CHAMORRO', 'BACA', 'CHAMORRO BACA VICTOR HUGO', 2, '12345678', 9, 0, 'direccion', 12431412, '', '', '', '', '', '', 23, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(15, 'EUSEBIO', 'PAITAN', 'QUISPE', 'PAITAN QUISPE EUSEBIO', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 24, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(16, 'WILMER CHRISTIAN', 'HUAMANI', 'CUPE', 'HUAMANI CUPE WILMER CHRISTIAN', 2, '12345678', 4, 0, 'direccion', 12431412, '', '', '', '', '', '', 25, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(17, 'HENRY MANUEL', 'ELIAS', 'LAOS', 'ELIAS LAOS HENRY MANUEL', 2, '12345678', 9, 0, 'direccion', 12431412, '', '', '', '', '', '', 26, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(18, 'RUBÉN', 'CHUMPITAZ', 'CORNEJO', 'CHUMPITAZ CORNEJO RUBÉN', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 27, '', 27, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(19, 'ISIDRO ERNESTO', 'PERLA', 'GALLESI', 'PERLA GALLESI ISIDRO ERNESTO', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 28, '', 26, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(20, 'MIGUEL', 'CHACÓN', 'ORTEGA', 'CHACÓN ORTEGA MIGUEL', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 29, '', 101, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(21, 'JAVIER RODOLFO', 'POLO', 'MENDOZA', 'POLO MENDOZA JAVIER RODOLFO', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 30, '', 22, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(22, 'DAVID GERSIN', 'MONZON', 'VALDIVIA', 'MONZON VALDIVIA DAVID GERSIN', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 31, '', 25, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(23, 'JOSE LUIS', 'ESPINOZA', 'ARRIETA', 'ESPINOZA ARRIETA JOSE LUIS', 2, '12345678', 6, 0, 'direccion', 12431412, '', '', '', '', '', '', 32, '', 111, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(24, 'WILLIAM FREDY', 'YAUYO', 'PAREDES', 'YAUYO PAREDES WILLIAM FREDY', 2, '12345678', 5, 0, 'direccion', 12431412, '', '', '', '', '', '', 11, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(25, 'IGNACIO', 'ESTRADA', 'PEIRANO', 'ESTRADA PEIRANO IGNACIO', 2, '12345678', 5, 0, 'direccion', 12431412, '', '', '', '', '', '', 33, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(26, 'ANABLE', 'HUILLCA', '', 'HUILLCA  ANABLE', 2, '12345678', 7, 0, 'direccion', 12431412, '', '', '', '', '', '', 34, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(27, 'INÉS', 'PÉREZ', 'GARCÍA', 'PÉREZ GARCÍA INÉS', 2, '12345678', 7, 0, 'direccion', 12431412, '', '', '', '', '', '', 35, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
(28, 'PATRICIA ', 'DE LA PINIELLA', '', 'DE LA PINIELLA  PATRICIA ', 2, '12345678', 7, 0, 'direccion', 12431412, '', '', '', '', '', '', 36, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `exchanges`
--

CREATE TABLE IF NOT EXISTS `exchanges` (
  `id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `sales` decimal(10,4) NOT NULL,
  `purchase` decimal(10,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `exchanges`
--

INSERT INTO `exchanges` (`id`, `date`, `currency_id`, `sales`, `purchase`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2015-07-17', 1, '3.0000', '3.0000', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `features`
--

CREATE TABLE IF NOT EXISTS `features` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `feature_group_id` int(10) unsigned NOT NULL,
  `catalog_car_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=739 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `features`
--

INSERT INTO `features` (`id`, `name`, `value`, `feature_group_id`, `catalog_car_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Motor', '3,471 c.c.      V6', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:24:39', NULL),
(2, 'Potencia Máxima (Hp / rpm)', '250      HP  -  5,700 rpm', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(3, 'Torque de Motor  (Kgm.  /  rpm )', '39.4 Kgm.  -  4,800 rpm', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(4, 'Tren de Válvulas', '24   SOHC   i - VTEC', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(5, 'Capacidad del Tanque de Combustible', '79.5 Lts.  - 24 Glns.', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(6, 'Asistencia de Direccion', 'EPS - TCS', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(7, 'Sistema de Inyección y Economy', 'Drive by Wire + VCM', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(8, 'Sistemas de Freno', 'ABS + EBD + VSA', 1, 1, '2015-07-09 21:06:28', '2015-07-09 21:06:28', NULL),
(9, 'Longitud', '5,152 mm', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(10, 'Ancho', '2,011 mm', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(11, 'Alto', '1,737 mm', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(12, 'Distancia suelo/chasis', '    155 mm', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(13, 'Peso en vacío', '2,058 Kgs', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(14, 'Medida de Neumáticos', '235 / 65 / R17', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(15, 'Tipo de Aros', 'Aleación 17''''', 2, 1, '2015-07-09 21:09:57', '2015-07-09 21:09:57', NULL),
(16, 'EXTERIOR', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(17, 'Faros Halogenos', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(18, 'Luces Diurnas (DRL)', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(19, 'Techo Corredizo automático one touch', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(20, 'INTERIOR', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(21, 'Pantalla Touch Screen', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(22, 'Pantalla de Multi información (i - Mid)', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(23, 'Bluetooth - Hands Free Link', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(24, 'Aire Acondicionado Climatizado Tri - Zona', '', 3, 1, '2015-07-09 21:14:26', '2015-07-09 21:14:26', NULL),
(25, 'Equipo de Audio con salida 7 parlantes', '', 3, 1, '2015-07-09 21:14:27', '2015-07-09 21:14:27', NULL),
(26, 'Asientos regulables electricamente en altura y profundidad', '', 3, 1, '2015-07-09 21:14:27', '2015-07-09 21:14:27', NULL),
(27, 'Controles en el volante iluminados (Audio/Velocidad crucero/Teléfono/i-Mid)', '', 3, 1, '2015-07-09 21:14:27', '2015-07-09 21:24:39', NULL),
(28, 'Honda Lanewatch', '', 3, 1, '2015-07-09 21:24:39', '2015-07-09 21:24:39', NULL),
(29, 'Cool Box', '', 3, 1, '2015-07-09 21:24:39', '2015-07-09 21:24:39', NULL),
(30, 'Airbags Piloto, Copiloto, Laterales y Cortina', '', 4, 1, '2015-07-09 21:24:39', '2015-07-09 21:24:39', NULL),
(31, 'Cinturones de 23 puntos en todas las plazas', '', 4, 1, '2015-07-09 21:24:39', '2015-07-09 21:24:39', NULL),
(32, 'Anclajes LATCH para niños', '', 4, 1, '2015-07-09 21:24:39', '2015-07-09 21:24:39', NULL),
(33, 'Cabezales Activos delanteros', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(34, 'Sistema Engine Start/Stop Button ', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(35, 'Estructura de Protección Tridimensional (ACE)', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(36, 'Sistema de Protección al Peatón (SPP)', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(37, 'Alarma Integrada', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(38, 'Tercera Luz de Freno en el Aleron Posterior', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(39, 'Cámara de vista Posterior con guía', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(40, 'Sensores de Estacionamiento', '', 4, 1, '2015-07-09 21:24:40', '2015-07-09 21:24:40', NULL),
(41, 'Motor', '2,354 c.c.', 1, 2, '2015-07-15 21:26:28', '2015-07-15 21:26:28', NULL),
(42, 'Potencia Máxima (HP / rpm )', '185      hp  -  7,000 rpm', 1, 2, '2015-07-15 21:26:28', '2015-07-23 18:36:09', NULL),
(43, 'Torque de Motor (Kgm / rpm )', '25 Kgm  -  4,400 rpm', 1, 2, '2015-07-15 21:26:28', '2015-07-23 18:36:09', NULL),
(44, 'Tren de Válvulas', 'DOHC   i  -  VTEC de 16 Válvulas', 1, 2, '2015-07-15 21:26:29', '2015-07-23 18:36:09', NULL),
(45, 'Capacidad de tanque de combustible', '58 Lts.        18 Glns.', 1, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(46, 'Sistemas de Freno', 'ABS + EBD + VSA', 1, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(47, 'Asistencia de Dirección', 'EPS  +  TCS', 1, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(48, 'Asistencias de ahorro de combustible', 'Drive by wire  +  Eco Assist', 1, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(49, 'Longitud', '4,530 mm', 2, 2, '2015-07-15 21:26:29', '2015-07-23 18:36:09', NULL),
(50, 'Ancho', '1,820 mm', 2, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(51, 'Altura', '1,654 mm', 2, 2, '2015-07-15 21:26:29', '2015-07-23 18:36:09', NULL),
(52, 'Distancia al suelo', '1,720 mm', 2, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(53, 'Peso en vacío', '1,594 Kgs.', 2, 2, '2015-07-15 21:26:29', '2015-07-23 18:36:10', NULL),
(54, 'Neumaticos', '225 / 65 / R17', 2, 2, '2015-07-15 21:26:29', '2015-07-15 21:26:29', NULL),
(55, 'Aros', 'Aleación 17 de pulgadas', 2, 2, '2015-07-15 21:26:29', '2015-07-23 18:36:10', NULL),
(56, 'EXTERIOR', '', 3, 2, '2015-07-15 21:54:40', '2015-07-15 21:54:40', NULL),
(57, 'Faros Halogenos', '', 3, 2, '2015-07-15 21:54:40', '2015-07-15 21:54:40', NULL),
(58, 'Luces Diurnas Leds (LDR)', '', 3, 2, '2015-07-15 22:03:19', '2015-07-15 22:03:19', NULL),
(59, 'INTERIOR', '', 3, 2, '2015-07-15 22:03:19', '2015-07-15 22:03:19', NULL),
(60, 'Aire Acondicionado Climatizado Dual', '', 3, 2, '2015-07-15 22:03:19', '2015-07-15 22:03:19', NULL),
(61, 'Equipo de Audio con 4 parlantes y 2 Twiters- memoria de 2 Gb', '', 3, 2, '2015-07-15 22:03:19', '2015-07-15 22:08:19', NULL),
(62, 'Asientos y timón con cuero de fábrica regulables en altura y profundidad', '', 3, 2, '2015-07-15 22:03:19', '2015-07-15 22:08:19', NULL),
(63, 'Blue tooth Hands Free Link', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:08:19', NULL),
(64, 'Pantalla de multinfomación i - Mid', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:08:19', NULL),
(65, 'Pantalla Touch Screen ', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:08:19', NULL),
(66, 'Controles montados en el volante iluminados (audio-cruce control-bluetooth)', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:08:19', NULL),
(67, 'Sistema de encendido ENGINE START / STOP', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:12:29', NULL),
(68, 'Fácil y Cómodo abatimiento de asientos posteriores', '', 3, 2, '2015-07-15 22:08:19', '2015-07-15 22:08:19', NULL),
(69, 'Airbags Piloto, Copiloto, Laterales y cortina', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(70, 'Cinturones de 3 puntos en todas las plazas', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(71, 'Seguridad para asientos de Niños (LATCH)', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(72, 'Cabezales Activos delanteros', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(73, 'Alarma Integrada', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(74, 'Estructura de Protección tridimensional (ACE)', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(75, 'Llave de apertura remota y por aproximación de puertas', '', 4, 2, '2015-07-15 22:12:29', '2015-07-15 22:12:29', NULL),
(76, 'Tercera Luz de Freno en Spoiler posterior', '', 4, 2, '2015-07-15 22:14:05', '2015-07-15 22:14:05', NULL),
(77, 'Sistema Smart Entry', '', 4, 2, '2015-07-15 22:14:05', '2015-07-15 22:14:05', NULL),
(78, 'Vigas en las puertas', '', 4, 2, '2015-07-15 22:14:05', '2015-07-15 22:14:05', NULL),
(79, 'Sistema de Protección al Peatón  (SPP)', '', 4, 2, '2015-07-15 22:14:05', '2015-07-15 22:14:05', NULL),
(80, 'Motor', '2,354 c.c.', 1, 4, '2015-07-23 18:13:12', '2015-07-23 18:13:12', NULL),
(81, 'Potencia Máxima (HP / rpm)', '185      HP  -  7,000 rpm', 1, 4, '2015-07-23 18:13:12', '2015-07-23 18:33:02', NULL),
(82, 'Torque de Motor', '25  kgm.  -  4,400 rpm', 1, 4, '2015-07-23 18:13:12', '2015-07-23 18:33:02', NULL),
(83, 'Tren de valvulas', 'DOHC     i - VTEC de 16 Válvulas', 1, 4, '2015-07-23 18:13:12', '2015-07-23 18:33:02', NULL),
(84, 'Capacidad del tanque de combustible', '58   Lts.          18   Glns.', 1, 4, '2015-07-23 18:13:12', '2015-07-23 18:13:12', NULL),
(85, 'Asistencia de Dirección', 'EPS   +   TCS', 1, 4, '2015-07-23 18:16:57', '2015-07-23 18:16:57', NULL),
(86, 'Sistemas de Freno', 'ABS   +   EBD   +   VSA', 1, 4, '2015-07-23 18:16:57', '2015-07-23 18:16:57', NULL),
(87, 'Asistencias de Ahorro de Combustible', 'Drive by Wire  +  Sistema Eco Assist', 1, 4, '2015-07-23 18:33:02', '2015-07-23 18:33:02', NULL),
(88, 'Asistencia de Marcha en Pendiente', 'Hill Start Assist', 1, 4, '2015-07-23 18:33:02', '2015-07-30 23:38:34', '2015-07-30 23:38:34'),
(89, 'Longitud', '4,530 mm', 2, 4, '2015-07-23 18:33:02', '2015-07-23 18:33:02', NULL),
(90, 'Ancho ', '1,820 mm', 2, 4, '2015-07-23 18:33:02', '2015-07-23 18:33:02', NULL),
(91, 'Altura', '1,654 mm', 2, 4, '2015-07-23 18:33:02', '2015-07-23 18:33:02', NULL),
(92, 'Distancia al suelo', '1,720 mm', 2, 4, '2015-07-23 18:33:03', '2015-07-23 18:33:03', NULL),
(93, 'Peso en Vacío', '1594 kg.', 2, 4, '2015-07-23 18:33:03', '2015-07-23 18:33:03', NULL),
(94, 'Neumaticos', '225 / 65 / R17  102T', 2, 4, '2015-07-23 18:33:03', '2015-07-23 18:33:03', NULL),
(95, 'Ruedas', 'Aleación de 17 pulgadas', 2, 4, '2015-07-23 18:33:03', '2015-07-23 18:33:03', NULL),
(96, 'Motor', '2,354 c.c.', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(97, 'Potencia Máxima (HP / rpm)', '185      hp  -  7,000 rpm', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(98, 'Torque de Motor', '25 Kgm  -  4,400 rpm', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(99, 'Tren de valvulas', 'DOHC   i  -  VTEC de 16 Válvulas', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(100, 'Capacidad del tanque de combustible', '58   Lts.          18   Glns.', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(101, 'Sistemas de Frenado', 'ABS  +   EBD', 1, 3, '2015-07-23 18:39:00', '2015-07-23 18:39:00', NULL),
(102, 'Asistencias', '', 1, 3, '2015-07-23 18:39:01', '2015-07-23 18:39:01', NULL),
(103, 'EXTERIOR', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(104, 'Faros Halógenos de Reflector múltiple', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 19:57:18', NULL),
(105, 'Luces Diurnas LEDS  (DRL)', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(106, 'INTERIOR', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(107, 'Pantalla de Multi-información  I-Mid', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(108, 'Pantalla Táctil de 7 pulgadas', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:49:07', NULL),
(109, 'Asientos y Timón regulables en altura y profundidad forrados en cuero', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:49:07', NULL),
(110, 'Aire Acondicionado Climatizado dual', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:49:07', NULL),
(111, 'Velocidad de crucero en el volante', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(112, 'Controles Montados en el volante iluminados (Bluetooth, Cruce control, Audio)', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(113, 'Blue Tooth + Hands Free Link ', '', 3, 4, '2015-07-23 18:46:40', '2015-07-23 18:46:40', NULL),
(114, 'Airbags Piloto, Copiloto, laterales y tipo cortina con sensor de vuelco', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 19:57:18', NULL),
(115, 'Cinturones de 3 puntos en todas las plazas', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 18:46:41', NULL),
(116, 'Anclajes para niños LATCH', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 18:46:41', NULL),
(117, 'Cabezales Activos delanteros', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 18:46:41', NULL),
(118, 'Inmovilizador de Motor', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 18:46:41', NULL),
(119, 'Alarma Integrada', '', 4, 4, '2015-07-23 18:46:41', '2015-07-23 18:46:41', NULL),
(120, 'Estructura de Protección tridimensional (ACE)', '', 4, 4, '2015-07-23 19:57:18', '2015-07-23 19:57:18', NULL),
(121, 'Tercera Luz de freno', '', 4, 4, '2015-07-23 19:57:18', '2015-07-23 19:57:18', NULL),
(122, 'Vigas en las Puertas', '', 4, 4, '2015-07-23 19:57:18', '2015-07-23 19:57:18', NULL),
(123, 'Sistema de entrada de control remoto', '', 4, 4, '2015-07-23 19:57:18', '2015-07-23 19:57:18', NULL),
(124, 'Longitud', '4,530 mm', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(125, 'Altura', '1,654 mm', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(126, 'Ancho', '1,820 mm', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(127, 'Distancia al Suelo', '1,720 mm', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(128, 'Peso en vacío', '1523 mm', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(129, 'Neumáticos', '225  /  65  /  R17', 2, 3, '2015-07-23 20:01:46', '2015-07-23 20:01:46', NULL),
(130, 'EXTERIOR', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(131, 'Faros Halogenos de Reflecor Múltiple', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(132, 'Luces de Conducción Diurna (DRL) LEDS', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(133, 'Sistema de Entrada de Control Remoto', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(134, 'INTERIOR', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(135, 'Sistema de Aire Acondicionado con Filtración de aire', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(136, 'Asientos y timon revestido en cuero', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(137, 'Asientos y timón regulables en altura y profundidad', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(138, 'Controles en el volante iluminados (Bluetooth, Cruce control, Audio)', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(139, 'Pantalla de Multi-información I-Mid con mando desde el timón', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(140, 'Control de Velocidad de crucero', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(141, 'Equipo de Audio con 4 parlantes', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(142, 'Bluetooth  +  Hands Free Link', '', 3, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(143, 'Airbags Piloto y Copiloto', '', 4, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(144, 'Cinturones de 3 puntos en todas sus Plazas', '', 4, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(145, 'Anclajes para Niños LATCH', '', 4, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(146, 'Cabezales Activos delanteros', '', 4, 3, '2015-07-23 20:44:47', '2015-07-23 20:44:47', NULL),
(147, 'Inmovilizador de motor', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(148, 'Alarma Integrada', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(149, 'Apertura Remota de puertas', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(150, 'Estructura de Protección tridimensional (ACE)', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(151, 'Sistema de Protección al peaton (SPP)', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(152, 'Tercera Luz de Freno', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(153, 'Vigas de protección en las puertas', '', 4, 3, '2015-07-23 20:44:48', '2015-07-23 20:44:48', NULL),
(154, 'Motor', '3.5 litros', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(155, 'Potencia Neta', '278       Hp       @  6,200 rpm', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(156, 'Torque de Motor', '34.8      Kg.m   @  4,900 rpm', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(157, 'Tren de valvulas', 'SOHC i-VTEC de 24 Válvulas', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(158, 'Sistemas adicionales', 'VCM - ACM - ACN - Eco Assist', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(159, 'Capacidad de Tanque de Combustible', '65 Litros', 1, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(160, 'Motor', '3.5 litros', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(161, 'Longitud', '4,825 mm', 2, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(162, 'Potencia Neta', '278       Hp       @  6,200 rpm', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(163, 'Ancho', '1,855 mm', 2, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(164, 'Torque de Motor', '34.8      Kg.m   @  4,900 rpm', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(165, 'Altura', '1,445 mm', 2, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(166, 'Tren de valvulas', 'SOHC i-VTEC de 24 Válvulas', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(167, 'Peso Neto', '1,603 Kg.', 2, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(168, 'Sistemas adicionales', 'VCM - ACM - ACN - Eco Assist', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(169, 'Neumáticos y Rines de Aluminio', '235 / 45 R18', 2, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(170, 'Capacidad de Tanque de Combustible', '65 Litros', 1, 19, '2015-07-31 15:01:28', '2015-09-04 20:49:12', '2015-09-04 20:49:12'),
(171, 'EXTERIOR', '', 3, 19, '2015-07-31 15:01:28', '2015-07-31 15:01:28', NULL),
(172, 'Longitud', '4,825 mm', 2, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(173, 'Luces de Conducción Diurna (DRL)', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(174, 'Ancho', '1,855 mm', 2, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(175, 'Doble salida de escape cromado', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(176, 'Altura', '1,445 mm', 2, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(177, 'Faros Neblineros', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(178, 'Peso Neto', '1,603 Kg.', 2, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(179, 'Manijas de apertura con acabado en cromo', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(180, 'Neumáticos y Rines de Aluminio', '235 / 45 R18', 2, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(181, 'INTERIOR', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(182, 'Asiento del conductor con ajuste eléctrico de 8 vías forrado en cuero', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:25:16', NULL),
(183, 'Columna de Dirección con ajuste manual de altura y profundidad', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(184, 'Luces de Conducción Diurna (DRL)', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(185, 'Volante con control de audio y Velocidad de crucero', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(186, 'Doble salida de escape cromado', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(187, 'Interfaz HandsFreeLink con tecnología Bluetooth', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(188, 'Faros Neblineros', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:46', '2015-09-04 20:51:46'),
(189, 'Climatizador doble Zona', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(190, 'Manijas de apertura con acabado en cromo', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(191, 'Sistema de acceso inteligente sin llave (Smart Entry Keyless)', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(192, 'INTERIOR', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(193, 'Sistema de encendido de motor con botón (Smart Start Engine)', '', 3, 19, '2015-07-31 15:01:29', '2015-07-31 15:01:29', NULL),
(194, 'Columna de Dirección con ajuste manual de altura y profundidad', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(195, 'Volante con control de audio y Velocidad de crucero', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(196, 'Interfaz HandsFreeLink con tecnología Bluetooth', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(197, 'Climatizador doble Zona', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(198, 'Sistema de acceso inteligente sin llave (Smart Entry Keyless)', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(199, 'Sistema de encendido de motor con botón (Smart Start Engine)', '', 3, 19, '2015-07-31 15:01:29', '2015-09-04 20:51:10', '2015-09-04 20:51:10'),
(200, 'Bolsas de aire frontales, laterales y tipo cortina', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(201, 'Cinturones de seguridad con sistema automático de tensión', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(202, 'Estructura de la carrocería con Ingeniería de Compatibilidad Avanzada (ACE II)', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(203, 'Sistemas de Freno (ABS - EBD - VSA)', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(204, 'Lane Watch', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(205, 'Sistema de Anclaje para silla de bebé (LATCH)', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(206, 'Apoya cabezas activos', '', 4, 19, '2015-07-31 15:25:16', '2015-07-31 15:25:16', NULL),
(207, 'Motor', '3471 c.c. v-6', 1, 18, '2015-08-05 17:06:38', '2015-08-05 17:16:11', NULL),
(208, 'Potencia Máxima (HP / rpm)', '276 @ 6200', 1, 18, '2015-08-05 17:16:11', '2015-08-05 17:16:11', NULL),
(209, 'Torque de Motor  (kgm. / rpm )', '38.15 @ 4900', 1, 18, '2015-08-05 17:16:11', '2015-08-05 17:16:11', NULL),
(210, 'Tren de valvulas', '24 Valvulas SOHC i-VTEC', 1, 18, '2015-08-05 17:16:11', '2015-08-05 17:16:11', NULL),
(211, 'Transmision', 'Automática de 6 velocidades', 1, 18, '2015-08-05 17:16:11', '2015-08-05 17:16:11', NULL),
(212, 'Sistemas de Frenado', 'ABS  +   EBD + VSA', 1, 18, '2015-08-05 17:16:11', '2015-08-05 17:16:11', NULL),
(213, 'Direccion', 'Asistido eléctricamente', 1, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(214, 'Tecnología HONDA', 'Sistema VCM - ANC - Eco Assist', 1, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(215, 'Longitud ', '4890 mm', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(216, 'Altura ', '1475 mm', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(217, 'Ancho', '1850 mm', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(218, 'Peso Vehicular ', '1628 Kgs.', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(219, 'Neumaticos', '225 / 50 R17 94V', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(220, 'Rines', 'Aleación', 2, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(221, 'EXTERIOR', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(222, 'Luces de Freno LED', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(223, 'Faros Halogenos Multireflectores con función apagado', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(224, 'INTERIOR', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(225, 'Asientos de cuero con ajuste eléctrico de 8 posiciones', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(226, 'Panel de Información múltiple i-Mid de 8 pulgadas', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(227, 'Cámara de retroceso con guía', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(228, 'Bluetooth Hands FreeLink', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(229, 'Botón Engine Start/Stop', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(230, 'Velocidad de crucero', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(231, 'Timon con ajuste de altura y Profundidad', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(232, 'Touch Screen radio con 6 parlantes y Subwoofer', '', 3, 18, '2015-08-05 20:35:57', '2015-08-05 20:35:57', NULL),
(233, 'Estructura de Ingenieria de compatibilidad Avanzada ACE', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(234, 'Airbags Piloto, Copiloto, laterales y tipo cortina con sensor de vuelco', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(235, 'Cinturones de 3 puntos en todas las plazas', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(236, 'Anclajes para niños LATCH', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(237, 'Daytime Running Light  (DRL) ', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(238, 'Brake Assist', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(239, 'Sistema de Seguridad con Smart Entry', '', 4, 18, '2015-08-05 20:40:19', '2015-08-05 20:40:19', NULL),
(240, 'Motor', '2356 en linea 4 cilindros', 1, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(241, 'Potencia Máxima (HP / rpm)', '173  HP  @  6200 rpm', 1, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(242, 'Torque de Motor', '22.95 kgm @  4000 rpm', 1, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(243, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(244, 'Hill Start Assist ', 'Inmovilizador de Motor', 1, 17, '2015-08-05 20:50:37', '2015-08-05 21:06:03', NULL),
(245, 'Dirección eléctricamente asistida', 'EPS', 1, 17, '2015-08-05 20:50:37', '2015-08-05 21:42:21', NULL),
(246, 'Longitud', '4890 mm', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(247, 'Altura', '1475 mm', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(248, 'Ancho', '1850 mm', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(249, 'Peso Vehicular ', '1531 kgs.', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(250, 'Llantas toda estación', '225 / 50 R17  94V', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(251, 'Rines', '17" aleación', 2, 17, '2015-08-05 20:50:37', '2015-08-05 20:50:37', NULL),
(252, 'Sistemas de Freno', 'ABS + EBD + VSA', 1, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(253, 'EXTERIOR', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(254, 'Faros Neblineros', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(255, 'Luces de freno LEDS ', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(256, 'Sunroof Eléctrico Inclinable', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(257, 'INTERIOR', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(258, 'Panel de Información Multiple i-Mid de 8 pulgadas', '', 3, 17, '2015-08-05 21:06:03', '2015-08-05 21:06:03', NULL),
(259, 'Aire Acondicionado doble zona', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(260, 'Controles iluminados en el Timón (Cruce control, Audio, Teléfono, i-Mid)', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(261, 'Cámara de retroceso Multiángulo', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(262, 'Bluetooth HandsFreeLink', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(263, 'Asientos de cuero con ajuste electrico de 8 posiciones', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(264, 'Boton Engine Start/Stop', '', 3, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(265, 'Estructura de Ingeniería de Compatibilidad Avanzada (ACE II)', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(266, 'Airbags Piloto, Copiloto, Laterales y Cortina', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(267, 'Sistema de encendido ENGINE START / STOP', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(268, 'Airbags Piloto, Copiloto, laterales y tipo cortina con sensor de vuelco', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(269, 'Cinturones de 3 puntos en todas las plazas', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(270, 'Cabezales Activos delanteros', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(271, 'Anclajes para la Silla de Bebés LATCH', '', 4, 17, '2015-08-05 21:06:04', '2015-08-05 21:06:04', NULL),
(272, 'Motor', '2356 en linea 4 cilindros', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(273, 'Potencia Máxima (HP / rpm)', '173 HP @ 6200 rpm', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(274, 'Torque de Motor', '22.95 kgm. @ 4000 rpm', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(275, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(276, 'Hill Start Assist', 'Inmovilizador de Motor', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(277, 'Dirección eléctricamente asistida', 'EPS', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(278, 'Sistemas de Freno', 'ABS + EBD + VSA', 1, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(279, 'Longitud', '4890 mm', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(280, 'Altura', '1475 mm', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(281, 'Ancho', '1850 mm', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(282, 'Peso Vehicular', '1531 kgs.', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(283, 'Llantas toda estación', '225 / 50 R17 94 V', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(284, 'Rines', '17" aleación', 2, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(285, 'EXTERIOR', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(286, 'Luces de Freno LEDS', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(287, 'Sunroof Eléctrico reclinable', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(288, 'Faros Halógenos Multireflectivos', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(289, 'INTERIOR', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(290, 'Panel de Información Múltiple i-Mid de 8 pulgadas', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(291, 'Aire Acondicionado Climatizado Dual', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(292, 'Controles iluminados en el timon (Crucero, Audio, Teléfono, i-Mid)', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(293, 'Bluetooth HandsfreeLink', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(294, 'Asientos y Timón regulables en altura y profundidad forrados en cuero electricos', '', 3, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(295, 'Apertura de puertas control remoto', '', 4, 16, '2015-08-05 21:42:04', '2015-08-05 21:42:04', NULL),
(296, 'Estructura de Ingenieria de Compatibilidad Avanzada (ACE II)', '', 4, 16, '2015-08-05 21:42:05', '2015-08-05 21:42:05', NULL),
(297, 'Airbags Piloto, Copiloto, laterales y tipo cortina', '', 4, 16, '2015-08-05 21:42:05', '2015-08-05 21:42:05', NULL),
(298, 'Cinturones de 3 puntos en todas las plazas', '', 4, 16, '2015-08-05 21:42:05', '2015-08-05 21:42:05', NULL),
(299, 'Cabezales Activos delanteros', '', 4, 16, '2015-08-05 21:42:05', '2015-08-05 21:42:05', NULL),
(300, 'Anclajes para la Silla de bebés LATCH', '', 4, 16, '2015-08-05 21:42:05', '2015-08-05 21:42:05', NULL),
(301, 'Motor', '3471 c.c. 6 cilindros en V', 1, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(302, 'Potencia de Motor', '250 HP @ 5700 rpm', 1, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(303, 'Torque Máximo', '34.9 Kgm. @ 4800 rpm', 1, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(304, 'Tren de Valvulas', '24 valvulas SOHC  i-VTEC', 1, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(305, 'Sistema de Cilindros Variable y Traccion Integral', 'VCM + VTM', 1, 15, '2015-08-05 21:59:03', '2015-08-05 22:19:49', NULL),
(306, 'Control activo de soporte de Motor (ACM)', 'Eliminador de Ruidos (ANC)', 1, 15, '2015-08-05 21:59:03', '2015-08-05 22:18:12', '2015-08-05 22:18:12'),
(307, 'Sist. de Traccion Integral Variable (VTM)', 'Transmision 5 vel. con Control Lógico', 1, 15, '2015-08-05 21:59:03', '2015-08-05 22:19:50', '2015-08-05 22:19:50'),
(308, 'Capacidad de Tanque de Combustible', '80 litros   -   22 Glns', 1, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(309, 'Longitud', '4850 mm', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(310, 'Altura', '1846 mm', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(311, 'Ancho', '1995 mm', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(312, 'Peso Vehicular', '2067 kgs.', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(313, 'Capacidad de Remolque', '2041 kgs.', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(314, 'Llantas toda estación', '245 / 65 R17 ', 2, 15, '2015-08-05 21:59:03', '2015-08-05 22:52:02', NULL),
(315, 'Rines', '17  * 7.5" de aleación', 2, 15, '2015-08-05 21:59:03', '2015-08-05 21:59:03', NULL),
(316, 'EXTERIOR', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(317, 'Estribos Negros con luces Touring', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(318, 'Protectores de guardafangos', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(319, 'Barras Transversales', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(320, 'Sunroof Eléctrico inclinable', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(321, 'Faros Neblineros', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(322, 'Sensores de Estacionamiento delantero y posterior', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(323, 'Faros Halógenos de Largo Alcance con encendido/apagado automático', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:22:10', '2015-08-05 22:22:10'),
(324, 'INTERIOR', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(325, 'Aire Acondicionado de 3 Zonas con control de humedad', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(326, 'Enlace manos libres HandsFreeLink con Bluetooth   -   i-Mid', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:21:10', NULL),
(327, 'Equipo de DVD', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(328, 'Controles iluminados montados en el timón (Crucero, Audio, Bluetooth)', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(329, 'Asientos de piloto y copiloto con ajuste electrico de 8 vias', '', 3, 15, '2015-08-05 22:16:24', '2015-08-05 22:16:24', NULL),
(330, 'Pisos de jebe ', '', 3, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(331, 'sistema de audio Am/Fm CD con 6 parlantes y Subwoofer', '', 3, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(332, '2GB Music Library', '', 3, 15, '2015-08-05 22:16:25', '2015-08-05 22:21:10', '2015-08-05 22:21:10'),
(333, 'i - Mid', '', 3, 15, '2015-08-05 22:16:25', '2015-08-05 22:21:10', '2015-08-05 22:21:10'),
(334, 'Estructura de Protección tridimensional (ACE)', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(335, 'Airbags Piloto, Copiloto, Laterales y Cortina', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(336, 'Cinturones de 3 puntos en todas las plazas', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(337, 'Cabezales Activos Delanteros', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(338, 'Anclajes para la silla del Bebé LATCH', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(339, 'Sistema inmovilizador antirobo', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(340, 'Asistencia de arranque en cuestas', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(341, 'Luces Diurnas (DRL)', '', 4, 15, '2015-08-05 22:16:25', '2015-08-05 22:16:25', NULL),
(342, 'Motor', '3471 cc 6 cilindros en V', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(343, 'Potencia Máxima (HP / rpm)', '250 HP @ 5700 rpm', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(344, 'Torque Máximo', '34.9 Kgm. @ 4800 rpm', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(345, 'Tren de valvulas', '24 Válvulas SOHC i-VTEC', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(346, 'Sistema de Cilindros Variable y Traccion Integral', 'VCM + VTM', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(347, 'Capacidad de Tanque de Combustible', '80 Litros   -  22 Glns', 1, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(348, 'Longitud', '4850 mm', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(349, 'Altura', '1846 mm', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(350, 'Ancho', '1995 mm', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(351, 'Peso Vehicular ', '2067 Kgs.', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(352, 'Capacidad de Remolque', '2041 Kgs.', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(353, 'Llantas toda estación', '245 / 65 R17', 2, 14, '2015-08-05 22:51:49', '2015-08-05 22:51:49', NULL),
(354, 'Rines', '17 * 7.5" de aleación', 2, 14, '2015-08-05 22:51:50', '2015-08-05 22:51:50', NULL),
(355, 'EXTERIOR', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(356, 'Sunroof Eléctrico inclinable', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(357, 'Faros Neblineros', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(358, 'Luces Diurnas (DRL)', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(359, 'INTERIOR', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(360, 'Aire Acondicionado de 3 Zonas con control de Humedad', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(361, 'Enlace manos libres HandsFreeLink con Bluetooth  -  i-Mid', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(362, 'Controles iluminados montados en el timón (Crucero, Audio, Bluetooth)', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(363, 'Asientos de Piloto y Copiloto con ajuste eléctrico ', '', 3, 14, '2015-08-05 22:59:07', '2015-08-05 22:59:07', NULL),
(364, 'Sistema de audio Am/Fm CD con 6 parlantes y Subwoofer', '', 3, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(365, 'Estructura de Protección Tridimensional (ACE)', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(366, 'Airbags Piloto, Copiloto, Laterales y Cortina', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(367, 'Cinturones de 3 puntos en todas las plazas', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(368, 'Cabezales Activos Delanteros', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(369, 'Anclajes para la Silla del Bebé LATCH', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(370, 'Sistema Inmovilizador antirobo', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(371, 'Asistencia de arranque en cuestas', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(372, 'Alarma Integrada de Fabrica', '', 4, 14, '2015-08-05 22:59:08', '2015-08-05 22:59:08', NULL),
(373, 'Motor', '3471 cc  6 cilindros en V', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(374, 'Potencia Máxima (HP / rpm)', '250 HP @  5700 rpm', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(375, 'Torque de Motor', '34.9 kgm. @ 4800 rpm', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(376, 'Tren de valvulas', '24 Valvulas SOHC    i-VTEC', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(377, 'Sistema de Cilindros Variable y Traccion Integral', 'VCM + VTM', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(378, 'Capacidad de Tanque de Combustible', '80 litros   -   22 Glns', 1, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(379, 'Longitud', '4850 mm', 2, 13, '2015-08-05 23:07:23', '2015-08-05 23:07:23', NULL),
(380, 'Altura', '1846 mm', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(381, 'Ancho', '1995 mm', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(382, 'Peso Vehicular ', '2067 Kgs.', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(383, 'Capacidad de Remolque', '2041 Kgs.', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(384, 'Llantas toda estación', '245 / 65 R17', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(385, 'Rines', '17 * 7.5" de aleación', 2, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(386, 'Exterior', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(387, 'Sunroof Eléctrico Inclinable', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(388, 'Luces Diurnas (DRL)', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(389, 'INTERIOR', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(390, 'Aire Acondicionado de 3 Zonas con control de Humedad', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(391, 'Enlace manos libres HandsFreeLink con Bluetooth  -  i-Mid', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(392, 'Controles iluminados montados en el Timón (Crucero, Audio, Bluetooth)', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(393, 'Sistema de audio Am/Fm CD con 6 parlantes y Subwoofer', '', 3, 13, '2015-08-05 23:07:24', '2015-08-05 23:07:24', NULL),
(394, 'Estructura de Protección tridimensional (ACE)', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(395, 'Airbags Piloto, Copiloto, Laterales y Cortina (10)', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(396, 'Cinturones de 3 puntos en todas las plazas (8)', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(397, 'Cabezales Activos Delanteros', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(398, 'Anclajes para la silla del Bebé LATCH', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(399, 'Sistema de Arranque en cuestas', '', 4, 13, '2015-08-05 23:10:05', '2015-08-05 23:10:05', NULL),
(400, 'Tipo de Motor', '1,498 c.c. - 4 cilindros en linea', 1, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(401, 'Potencia de Motor (HP)', '132 hp       @   6,600 rpm', 1, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(402, 'Torque de Motor (kgm)', '15,8 Kgm  @   4,600 rpm', 1, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(403, 'Tren de valvulas', '16 Válvulas DOHC   i - VTEC', 1, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(404, 'Transmision', 'Manual de 6  velocidades', 1, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(405, 'Longitud', '4,067 mm', 2, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(406, 'Ancho', '1,694 mm', 2, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(407, 'Altura', '1,524 mm', 2, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(408, 'Neumáticos', '185 / 60 R15 84H', 2, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(409, 'Rines', '15" de aleación', 2, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(410, 'EXTERIOR', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(411, 'Faros Halógenos con Multireflector y apagado automático', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(412, 'Luces Leds Posteriores', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(413, 'Espejos laterales eléctricos', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(414, 'INTERIOR', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(415, 'Aire Acondicionado con Sist. de Filtración', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(416, 'Hands FreeLink - Bluetooth', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(417, 'Asientos Con Ajuste manual de altura', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(418, 'Sistema de Audio de 160 Watts con 4 parlantes', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:58:32', NULL),
(419, 'Asientos posteriores abatibles 60/40 con Sist. Magic Seat.', '', 3, 5, '2015-08-14 19:23:49', '2015-08-14 19:23:49', NULL),
(420, 'Capacidad del tanque de Combustible', '40 litros', 1, 5, '2015-08-14 19:31:19', '2015-08-14 19:31:19', NULL),
(421, 'Sistemas de Freno asistido   ABS y EBD', '', 4, 5, '2015-08-14 19:31:19', '2015-08-14 19:31:19', NULL),
(422, 'Bolsas de aire Delanteras Piloto y copiloto', '', 4, 5, '2015-08-14 19:31:19', '2015-08-14 19:31:19', NULL),
(423, 'Cinturones de seguridad de 3 puntos en todas las plazas', '', 4, 5, '2015-08-14 19:31:20', '2015-08-14 19:31:20', NULL),
(424, 'Seguridad de puertas con acceso remoto', '', 4, 5, '2015-08-14 19:31:20', '2015-08-14 19:31:20', NULL),
(425, 'Seguridad LATCH para niños', '', 4, 5, '2015-08-14 19:31:20', '2015-08-14 19:31:20', NULL),
(426, 'Estructura de la carroceria con ingenierís de compatibilidad Avanzada ACE', '', 4, 5, '2015-08-14 19:31:20', '2015-08-14 19:31:20', NULL),
(427, 'Tipo de Motor', '1,498 c.c  -  4 cil. en linea', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(428, 'Potencia Máxima (HP / rpm)', '132    HP      @     6,600 rpm', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(429, 'Torque de Motor', '15,8   Kgm.   @     4,600 rpm', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(430, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(431, 'Transmision', 'Automática CVT variable Continua', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(432, 'Capacidad del Tanque de Combustible', '40 Litros', 1, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(433, 'Longitud', '4,067 mm', 2, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(434, 'Altura', '1,524 mm', 2, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(435, 'Ancho', '1,694 mm', 2, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(436, 'Neumáticos', '185 / 60  R15  84H', 2, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(437, 'Rines', '15" de aleación', 2, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(438, 'EXTERIOR', '', 3, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(439, 'Faros Halógenos con Multireflector y apagado automático', '', 3, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(440, 'Luces leds Posteriores', '', 3, 6, '2015-08-14 19:54:41', '2015-08-14 19:54:41', NULL),
(441, 'Faros Diurnos (DRL)', '', 3, 6, '2015-08-14 19:58:08', '2015-08-14 19:58:08', NULL),
(442, 'INTERIOR', '', 3, 6, '2015-08-14 19:58:09', '2015-08-14 19:58:09', NULL),
(443, 'Aire Acondicionado con Sist. de Filtración', '', 3, 6, '2015-08-14 19:58:09', '2015-08-14 19:58:09', NULL),
(444, 'Hands FreeLink  -  Bluetooth', '', 3, 6, '2015-08-14 19:58:09', '2015-08-14 19:58:09', NULL),
(445, 'Asientos con ajuste manual de altura', '', 3, 6, '2015-08-14 19:58:09', '2015-08-14 19:58:09', NULL),
(446, 'Sistema de Audio de 160 Watts con 4 parlantes', '', 3, 6, '2015-08-14 19:58:09', '2015-08-14 19:58:09', NULL),
(447, 'Asientos Posteriores abatibles 60/40 con Sist. Magic Seat', '', 3, 6, '2015-08-14 20:09:34', '2015-08-14 20:09:34', NULL),
(448, 'Sistemas de Freno Asistidos ABS', '', 4, 6, '2015-08-14 20:09:34', '2015-08-14 20:09:34', NULL),
(449, 'Distribucion Electrónica del freno EBD', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(450, 'Bolsas de Aire Delanteras Piloto y Copiloto', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(451, 'Cinturones de seguridad de 3 puntos en todas las plazas', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(452, 'Seguridad de puertas con acceso remoto', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(453, 'Seguridad LATCH para la silla de niños', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(454, 'Estructura de la Carroceria con Ingeniería de Compatibilidad Avanzada ACE', '', 4, 6, '2015-08-14 20:09:35', '2015-08-14 20:09:35', NULL),
(455, 'Tipo de Motor', '1,498 c.c  -  4 cil. en linea', 1, 7, '2015-08-14 20:20:33', '2015-08-14 20:20:33', NULL),
(456, 'Potencia Máxima (HP / rpm)', '132    HP      @     6,600 rpm', 1, 7, '2015-08-14 20:20:33', '2015-08-14 20:20:33', NULL),
(457, 'Torque de Motor', '15,8   Kgm.   @     4,600 rpm', 1, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(458, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(459, 'Transmision', 'Automática CVT variable Continua', 1, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(460, 'Capacidad del Tanque de Combustible', '40 Litros', 1, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(461, 'Longitud', '4,067 mm', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(462, 'Altura', '1,524 mm', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(463, 'Ancho', '1,694 mm', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(464, 'Neumáticos', '185 / 55  R16  83V', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(465, 'Rines', '16" de aleación', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(466, 'Peso vehicular Kg.', '1,166 Kgs.', 2, 7, '2015-08-14 20:20:34', '2015-08-14 20:20:34', NULL),
(467, 'Peso Vehicular (Kgs.)', '1,155 Kgs.', 2, 6, '2015-08-14 20:21:09', '2015-08-14 20:21:09', NULL),
(468, 'Peso Vehicular (Kgs.)', '1,134 Kgs.', 2, 5, '2015-08-14 20:21:47', '2015-08-14 20:21:47', NULL),
(469, 'EXTERIOR', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL),
(470, 'Faros Halógenos con Multireflector y apagado automático', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL);
INSERT INTO `features` (`id`, `name`, `value`, `feature_group_id`, `catalog_car_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(471, 'Luces leds Posteriores', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL),
(472, 'Faros de Día (DRL)', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL),
(473, 'INTERIOR', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL),
(474, 'Aire Acondicionado con climatizador', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:36:40', NULL),
(475, 'Hands Free Link - Bluetooth', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:24:06', NULL),
(476, 'Asientos posteriores abatibles 60/40 con Sist. Magic Seat.', '', 3, 7, '2015-08-14 20:24:06', '2015-08-14 20:36:40', NULL),
(477, 'Sistema de audio Am/Fm  con 6 parlantes y pantalla LCD  5" a color', '', 3, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(478, 'Camara de retroceso', '', 3, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(479, 'iluminacion en los controles de l volante (Veloc. Crucero, Audio, Bluetooth)', '', 3, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(480, 'Sistema de Feno asistido Antibloqueo (ABS)', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(481, 'Distribucion Electrónica del Freno (EBD)', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(482, 'Cámara de vista Trasera con ángulos múltiples con guía', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(483, 'Bolsas de aire Piloto, copiloto', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(484, 'Bolsas de aire Laterales  y Tipo Cortina', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(485, 'Cinturones de seguridad de 3 puntos en todas las plazas', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(486, 'Estructura de la carrocería con ingeniería de compatibilidad avanzada  (ACE)', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(487, 'Seguridad para la silla de niños (LATCH)', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(488, 'Seguridad de puertas con acceso remoto', '', 4, 7, '2015-08-14 20:36:40', '2015-08-14 20:36:40', NULL),
(489, 'Tipo de Motor', '2,354 c.c.  4 cil. en Línea', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(490, 'Potencia Máxima (HP / rpm)', '202  HP       @   7,000  rpm', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(491, 'Torque de Motor', '23.78 Kgm. @   4,400  rpm', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(492, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(493, 'Transmision', 'Mecánica de 6 Velocidades', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(494, 'Capacidad del Tanque de Combustible', '50 Litros', 1, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(495, 'Longitud', '4,550 mm', 2, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(496, 'Altura', '1,415 mm', 2, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(497, 'Ancho', '1,755 mm', 2, 12, '2015-08-14 21:04:43', '2015-08-14 21:04:43', NULL),
(498, 'Neumáticos', 'P225 / 40  R18  92V', 2, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(499, 'Rines', '18" aleación de Aluminio', 2, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(500, 'Peso vehicular Kg.', '1,379 Kgs.', 2, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(501, 'EXTERIOR', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(502, 'Faros Halógenos Multireflectivos automáticos', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(503, 'Techo Corredizo aut. inclinable one touch', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(504, 'Sistema de seguridad con acceso remoto', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(505, 'Alerón del color de la carrocería', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(506, 'Faros de niebla', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(507, 'INTERIOR', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(508, 'Sistema de Audio Primium de 360 Watts con 7 bocinas', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(509, 'Sistema de control climatico', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(510, 'Enlace manos libres Hands Free Link Bluetooth', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(511, 'Tablero de instrumentos de 2 niveles', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(512, 'Controles iluminados en el volante (Vel. de crucero, Audio, Teléfono, i-Mid)', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(513, 'Columna de Direccion telescópica y ajustable.', '', 3, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(514, 'Cinturones de 3 puntos en todas las plazas', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(515, 'Anclajes para niños LATCH', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(516, 'Bolsas de aire delanteros doble etapa doble umbral', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(517, 'Bolsas de aire Laterales y tipo cortina', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(518, 'Estructura de la carrocería con Ingeniería de Compatibilidad avanzada ACE', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(519, 'Faros de Día (DRL)', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(520, 'Frenos asistidos - antibloqueo (ABS)', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(521, 'Distribucion electrónica del freno (EBD)', '', 4, 12, '2015-08-14 21:04:44', '2015-08-14 21:04:44', NULL),
(522, 'Sistema estabilizador del vehículo (VSA) con control de tracción', '', 4, 12, '2015-08-14 21:06:43', '2015-08-14 21:06:43', NULL),
(523, 'Vigas en las puertas contra impactos laterales', '', 4, 12, '2015-08-14 21:06:43', '2015-08-14 21:06:43', NULL),
(524, 'Tipo de Motor', '2,354 c.c.', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(525, 'Potencia Máxima (HP / rpm)', '202  HP      @   7,000 rpm', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(526, 'Torque de Motor', '23.78 Kgm. @  4,400 rpm', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(527, 'Tren de valvulas', '16    DOHC     i  -  VTEC', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(528, 'Transmision', 'mecánica de 6 velocidades', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(529, 'Capacidad del Tanque de Combustible', '50 Litros', 1, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(530, 'Longitud', '4,560 mm', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(531, 'Altura', '1,450 mm', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(532, 'Ancho', '1,755 mm', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(533, 'Neumáticos', 'P225 / 40 R18  92V', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(534, 'Rines', '18" aleación de Aluminio', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(535, 'Peso vehicular Kg.', '1,379 Kgs.', 2, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(536, 'EXTERIOR', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(537, 'Faros Halógenos con Multireflector y apagado automático', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(538, 'Techo Corredizo aut. inclinable one touch', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(539, 'Sistema de seguridad con acceso remoto', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(540, 'Alerón posterior del color de la carrocería', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(541, 'Faros de niebla', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(542, 'INTERIOR', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(543, 'Sistema de Audio Primium de 360 Watts con 7 bocinas', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(544, 'Sistema de Control Climático', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(545, 'Enlace Manos Libres Hands Free Link Blutooth', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(546, 'Tablero de Instrumentos de 2 niveles (Vel. Crucero, Audio, Teléfono, i-Mid)', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(547, 'Columna de Dirección telescópica y Ajustable', '', 3, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(548, 'Cinturones de seguridad de 3 puntos en todas las plazas', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(549, 'Cabezales Activos delanteros', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(550, 'Anclajes para silla de niños LATCH', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(551, 'Bolsas de aire delanteros doble etapa, doble umbral', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(552, 'Bolsas de aire laterales y tipo cortina', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(553, 'Estructura e la carrocería con ingeniería de Compatibilidad Avanzada ACE', '', 4, 11, '2015-08-14 21:28:39', '2015-08-14 21:28:39', NULL),
(554, 'Faros de Día (DRL)', '', 4, 11, '2015-08-14 21:28:40', '2015-08-14 21:28:40', NULL),
(555, 'Frenos asistidos - Antibloqueo (ABS)', '', 4, 11, '2015-08-14 21:28:40', '2015-08-14 21:28:40', NULL),
(556, 'Distribución electrónica del freno (EBD)', '', 4, 11, '2015-08-14 21:28:40', '2015-08-14 21:28:40', NULL),
(557, 'Sistema estabilizador del vehículo (VSA) con control de tracción', '', 4, 11, '2015-08-14 21:28:40', '2015-08-14 21:28:40', NULL),
(558, 'Vigas en las puertas contra impactos laterales', '', 4, 11, '2015-08-14 21:28:40', '2015-08-14 21:28:40', NULL),
(559, 'Tipo de Motor', '1,799 c.c.   4 cil. en linea', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(560, 'Potencia Máxima (HP / rpm)', '140 HP      @   6,500 rpm', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(561, 'Torque de Motor', '17.7 Kgm. @   4,300 rpm', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(562, 'Tren de valvulas', '16   Valv.   SOHC     i  -  VTEC', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(563, 'Transmision', 'Automática de 5 velocidades', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(564, 'Capacidad del Tanque de Combustible', '50 Litros', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(565, 'Sistema Eco Assist', '', 1, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(566, 'Longitud', '4,460 mm', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(567, 'Altura', '1,415 mm', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(568, 'Ancho', '1,755 mm', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(569, 'Neumáticos', '205 / 55  R16', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(570, 'Rines', '16" de aleación', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(571, 'Peso vehicular Kg.', '1,256 Kgs.', 2, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(572, 'EXTERIOR', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(573, 'Sunroof', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(574, 'Sistema de ingreso a control remoto', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(575, 'Faros Halogenos Multi-reflectantes', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(576, 'INTERIOR', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(577, 'Sistema de control de aire acondicionado y filtración de aire', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(578, 'Enlace Manos libres Hands Free Link Bluetooth', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(579, 'Tablero de instrumentos de 2 niveles', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(580, 'Controles iluminados en el volante (Vel. Crucero, Audio, Teléfono, Bluetooth)', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(581, 'Columna de dirección telescópica y ajustable', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(582, 'Botón de arranque Start/stop', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(583, 'Asientos y timón forrados en cuero', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(584, 'Sistema de audio de 160 Watts con 6 bocinas', '', 3, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(585, 'Cinturones de 3 puntos en todas las plazas', '', 4, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(586, 'Cabezales Activos delanteros', '', 4, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(587, 'Anclajes para la Silla de Bebés LATCH', '', 4, 10, '2015-08-14 21:45:00', '2015-08-14 21:45:00', NULL),
(588, 'Alarma Integrada', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(589, 'Estructura de Protección tridimensional (ACE)', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(590, 'Frenos asistidos - antibloqueo (ABS)', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(591, 'Distribucion electrónica del freno (EBD)', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(592, 'Sistema de entrada de control remoto', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(593, 'Sistema estabilizador del vehículo (VSA) con control de tracción', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(594, 'Luces Diurnas (DRL)', '', 4, 10, '2015-08-14 21:45:01', '2015-08-14 21:45:01', NULL),
(595, 'Tipo de Motor', '1,799 c.c  4 cil. en linea', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(596, 'Potencia Máxima (HP / rpm)', '140 HP        @  6,500 rpm', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(597, 'Torque de Motor', '17.7 kgm.    @  4,300 rpm', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(598, 'Tren de valvulas', '16  Válvulas   SOHC     i  -  VTEC', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(599, 'Transmision', 'Automática de 5 velocidades', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(600, 'Capacidad del Tanque de Combustible', '50 Litros', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(601, 'Sistema Eco Assist', '', 1, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(602, 'Longitud', '4,505 mm', 2, 9, '2015-08-14 23:55:44', '2015-08-14 23:55:44', NULL),
(603, 'Altura', '1,450 mm', 2, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(604, 'Ancho', '1,755 mm', 2, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(605, 'Neumáticos', '205 / 55   R16', 2, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(606, 'Rines', '16" de aleacion', 2, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(607, 'Peso Vehicular Kg.', '1,229 Kg.', 2, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(608, 'EXTERIOR', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(609, 'Sunroof', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(610, 'Sistema de ingreso a control remoto', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(611, 'Faros Halogenos Multi-reflectantes', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(612, 'INTERIOR', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(613, 'Sistema de control de aire acondicionado y filtración de aire', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(614, 'Enlace Manos libres Hands Free Link Bluetooth', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(615, 'Tablero de instrumentos de 2 niveles', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(616, 'Controles iluminados en el volante (Vel. Crucero, Audio, Teléfono, Bluetooth)', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(617, 'Columna de dirección telescópica y ajustable', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(618, 'Botón de arranque Start/stop', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(619, 'Asientos y timón forrados en cuero', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(620, 'Sistema de audio de 160 Watts con 6 bocinas', '', 3, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(621, 'Cinturones de 3 puntos en todas las plazas', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(622, 'Cabezales Activos Delanteros', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(623, 'Anclajes para la Silla del Bebé LATCH', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(624, 'Alarma Integrada', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(625, 'Estructura de proteccion tridimensional (ACE)', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(626, 'Frenos Asistidos - Antibloqueo (ABS)', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(627, 'Distribución electrónica del Freno (EBD)', '', 4, 9, '2015-08-14 23:55:45', '2015-08-14 23:55:45', NULL),
(628, 'Sistema de entrada de control remoto', '', 4, 9, '2015-08-14 23:55:46', '2015-08-14 23:55:46', NULL),
(629, 'Sistema de Protección al peaton (SPP)', '', 4, 9, '2015-08-14 23:55:46', '2015-08-14 23:56:56', NULL),
(630, 'Sistema Estabilizador del vehículo (VSA) con control de tracción', '', 4, 9, '2015-08-14 23:56:56', '2015-08-14 23:56:56', NULL),
(631, 'Luces Diurnas (DRL)', '', 4, 9, '2015-08-14 23:56:56', '2015-08-14 23:56:56', NULL),
(632, 'Tipo de Motor', '1,799 c.c. 4 Cil. en Línea', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(633, 'Potencia Máxima (HP / rpm)', '140 HP       @  6,500 rpm', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(634, 'Torque de Motor', '17.7 Kgm.  @  4,300 rpm', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(635, 'Tren de valvulas', '16 Valv. SOHC   i-Vtec', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(636, 'Transmision', 'Automática de 5 velocidades', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(637, 'Capacidad del Tanque de Combustible', '50 Litros', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(638, 'Sistema Eco Assist', '', 1, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(639, 'Longitud', '4,505 mm', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(640, 'Altura', '1,450 mm', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(641, 'Ancho', '1,755 mm', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(642, 'Neumáticos', '205 / 55 / R16', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(643, 'Rines', '16" de aleación', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(644, 'Peso vehicular', '1,256 Kgs.', 2, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(645, 'EXTERIOR', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(646, 'Faros Diurnos (DRL)', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(647, 'Sistema de ingreso a control remoto', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(648, 'Faros Halogenos Multi-reflectantes', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(649, 'INTERIOR', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(650, 'Sistema de control de aire acondicionado y filtración de aire', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(651, 'Enlace Manos libres Hands Free Link Bluetooth', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(652, 'Tablero de instrumentos de 2 niveles', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(653, 'Controles iluminados en el volante ( Audio, Teléfono, Bluetooth)', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(654, 'Columna de dirección telescópica y ajustable', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(655, 'Sistema de audio de 160 Watts con 6 bocinas', '', 3, 8, '2015-08-15 00:07:08', '2015-08-15 00:07:08', NULL),
(656, 'Cinturones de seguridad de 3 puntos en todas las plazas', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(657, 'Cabezales Activos delanteros', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(658, 'Estructura de Ingenieria de Compatibilidad Avanzada (ACE II)', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(659, 'Airbags Piloto, Copiloto y Laterales ', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:39', NULL),
(660, 'Anclajes para la Silla del Bebé LATCH', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(661, 'Alarma Integrada', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(662, 'Sistema estabilizador del vehiculo (VSA) con control de tracción', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(663, 'Sistema de Frenos Asistidos Antibloqueo (ABS)', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(664, 'Distribución electrónica del Freno (EBD)', '', 4, 8, '2015-08-15 00:14:11', '2015-08-15 00:14:11', NULL),
(665, 'Tipo de Motor', '3,471 c.c. 6 Cil. en V', 1, 20, '2015-09-04 20:02:46', '2015-09-04 20:02:46', NULL),
(666, 'Potencia Máxima (HP / rpm)', '280 HP       6,000 rpm', 1, 20, '2015-09-04 20:02:46', '2015-09-04 20:02:46', NULL),
(667, 'Torque de Motor', '36.2 Kgm.  4,700 rpm', 1, 20, '2015-09-04 20:02:47', '2015-09-04 20:02:47', NULL),
(668, 'Tren de valvulas', '24   SOHC   i-VTEC', 1, 20, '2015-09-04 20:02:47', '2015-09-04 20:02:47', NULL),
(669, 'Capacidad del tanque de combustible', '74  Lts.     23 Glns.', 1, 20, '2015-09-04 20:02:47', '2015-09-04 20:02:47', NULL),
(670, 'Sistemas de Frenado', 'ABS  +   EBD + VSA', 1, 20, '2015-09-04 20:02:47', '2015-09-04 20:02:47', NULL),
(671, 'Sistema Drive by Wire            ', 'Sistema de Cilindros Variable (VCM)', 1, 20, '2015-09-04 20:02:47', '2015-09-04 20:16:30', NULL),
(672, 'Longitud', '4,941 m.m.', 2, 20, '2015-09-04 20:16:30', '2015-09-04 20:16:30', NULL),
(673, 'Altura', '1,773 m.m.', 2, 20, '2015-09-04 20:16:30', '2015-09-04 20:16:30', NULL),
(674, 'Ancho', '1,996 m.m.', 2, 20, '2015-09-04 20:16:30', '2015-09-04 20:16:30', NULL),
(675, 'Distancia al Suelo', '185 m.m.', 2, 20, '2015-09-04 20:16:30', '2015-09-04 20:16:30', NULL),
(676, 'Peso vehicular Kg.', '1,863 Kgs.', 2, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(677, 'Neumáticos', '245/ 60  R18', 2, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(678, 'Aros', 'Aleación de 18 "', 2, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(679, 'EXTERIOR', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(680, 'Faros halógenos', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(681, 'INTERIOR', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(682, 'Enlace Manos Libres HandsFreeLink con Bluetooth', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(683, 'Sistema de control de aire acondicionado de 3 Zonas y filtración de aire', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(684, 'Control de Velocidad de crucero', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(685, 'Visualizador de cámara de retroceso de 3 angulos', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(686, 'Sistema de audio con 6 parlantes + Subwoofer', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(687, 'Controles iluminados en el volante (cruce control, audio, bluetooth)', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(688, 'Novedoso panel de instrumentos', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(689, 'Pantalla de Multi-información i-Mid', '', 3, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(690, 'Airbags Piloto, Copiloto, Laterales y Cortina', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(691, 'Cinturones de 3 puntos en todas las plazas', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(692, 'Anclajes para silla de niños LATCH en 2 y 3 fila', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(693, 'Cabezales activos  delanteros', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(694, 'Inmovilizador de Motor', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(695, 'Alarma Integrada', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(696, 'Apertura remota de puertas', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(697, 'Estructura de protección tridimensional (ACE)', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(698, 'Asistencia de Arranque en cuestas', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(699, 'Vigas en las puertas contra impactos laterales', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(700, 'Tercera Luz de Freno', '', 4, 20, '2015-09-04 20:16:31', '2015-09-04 20:16:31', NULL),
(701, 'Tipo de Motor', '3,471 c.c.', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(702, 'Potencia Máxima (HP / rpm)', '280 HP         6,000 rpm', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(703, 'Torque de Motor', '36.2  Kgm.   4,700 rpm', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(704, 'Tren de valvulas', '24   SOHC   i-VTEC', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(705, 'Capacidad del tanque de combustible', '74  Lts.          23 Glns.', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(706, 'Sistemas de Frenado', 'ABS  +   EBD + VSA', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(707, 'Sistema Drive by Wire             ', 'Sistema de Cilindros Variable (VCM)', 1, 21, '2015-09-04 20:32:50', '2015-09-04 20:32:50', NULL),
(708, 'Longitud', '4,941 m.m.', 2, 21, '2015-09-04 20:37:00', '2015-09-04 20:37:00', NULL),
(709, 'Altura', '1,773 m.m.', 2, 21, '2015-09-04 20:37:00', '2015-09-04 20:37:00', NULL),
(710, 'Ancho', '1,996 m.m.', 2, 21, '2015-09-04 20:37:00', '2015-09-04 20:37:00', NULL),
(711, 'Distancia al Suelo', '185   m.m.', 2, 21, '2015-09-04 20:37:00', '2015-09-04 20:37:00', NULL),
(712, 'Peso vehicular Kg.', '1,962 Kgs.', 2, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(713, 'Neumáticos', '245/60   R18', 2, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(714, 'Aros', 'Aleación de 18"', 2, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(715, 'EXTERIOR', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(716, 'Sistema de ingreso a control remoto', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(717, 'Sunroof eléctrico inclinable', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(718, 'Maletera con apertura eléctrica', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(719, 'Cámara de punto ciego (Lanewatch)', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(720, 'Sensores de estacionamiento', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(721, 'INTERIOR', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(722, 'Enlace de Manos Libres HandsFreeLink con Bluetooth', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:37:01', NULL),
(723, 'Sist. de Control de Temperatura de 3 zonas', '', 3, 21, '2015-09-04 20:37:01', '2015-09-04 20:42:52', NULL),
(724, 'Encendido remoto de motor', '', 3, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(725, 'Botón de encendido/apagado  & Smart Entry', '', 3, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(726, 'Pantalla Touch screen de 8"', '', 3, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(727, 'Asientos regulables eléctricamente con memoria ', '', 3, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(728, 'Airbags Piloto, Copiloto, laterales y tipo cortina con sensor de vuelco', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(729, 'Cinturones de 3 puntos en todas las plazas', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(730, 'Anclajes para niños LATCH en 2 y 3 fila', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(731, 'Cabezales Activos delanteros', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(732, 'Estructura de Portección Tridimensional (ACE)', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(733, 'Luces Diurnas Led (DRL)', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(734, 'Asistencia de Arranque en cuestas (HSA)', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(735, 'Vigas en las puertas contra impactos laterales', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(736, 'Alarma Integrada', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(737, 'Apertura Remota de Puertas y Arranque de Motor', '', 4, 21, '2015-09-04 20:42:52', '2015-09-04 20:42:52', NULL),
(738, 'Inmovilizador de Motor', '', 4, 21, '2015-09-04 20:42:53', '2015-09-04 20:42:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `feature_groups`
--

CREATE TABLE IF NOT EXISTS `feature_groups` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template` enum('primaryLeft','primaryRight','in','out') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `feature_groups`
--

INSERT INTO `feature_groups` (`id`, `name`, `template`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Especificaciones Técnicas Principales :', 'primaryLeft', '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(2, 'Dimensiones :', 'primaryRight', '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(3, 'CONFORT Y TECNOLOGIA :', 'in', '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(4, 'SISTEMA DE SEGURIDAD :', 'out', '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `id_types`
--

CREATE TABLE IF NOT EXISTS `id_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `id_types`
--

INSERT INTO `id_types` (`id`, `name`, `symbol`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'REGISTRO UNICO DE CONTRIBUYENTE', 'RUC', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(2, 'DOCUMENTO NACIONAL DE IDENTIDAD', 'DNI', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(3, 'CARNÉ DE EXTRANJERÍA', 'CEX', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(4, 'PASAPORTE', 'PAS', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `jobs`
--

INSERT INTO `jobs` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ANALISTA DE SISTEMAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(2, 'ASESOR DE VENTAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(3, 'ADMINISTRADOR DE VENTAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(4, 'TECNICO', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(5, 'JEFE DE TALLER', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(6, 'ASESOR DE SERVICIO', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(7, 'COORDINADOR DE POSTVENTA', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(8, 'JEFE DE POSTVENTA', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(9, 'CONTROL DE CALIDAD', '', '2015-10-02 15:55:34', '2015-10-02 15:55:34', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2015_04_02_194225_create_permission_groups_table', 1),
('2015_04_24_151758_create_permissions_table', 1),
('2015_04_29_204621_create_roles_table', 1),
('2015_05_02_192307_create_ubigeos_table', 1),
('2015_05_04_204827_create_role_permissions_table', 1),
('2015_05_05_150520_create_user_roles_table', 1),
('2015_05_07_192848_create_id_types_table', 1),
('2015_05_07_200125_create_jobs_table', 1),
('2015_05_07_200126_create_employees_table', 1),
('2015_05_09_014708_create_unit_types_table', 1),
('2015_05_09_022903_create_units_table', 1),
('2015_05_09_051224_create_currencies_table', 1),
('2015_05_09_051225_create_exchanges_table', 1),
('2015_05_12_145519_create_companies_table', 1),
('2015_05_12_220917_create_warehouses_table', 1),
('2015_05_13_035148_create_brands_table', 1),
('2015_05_13_041022_create_categories_table', 1),
('2015_05_13_041055_create_sub_categories_table', 1),
('2015_05_13_041056_create_products_table', 1),
('2015_05_14_040512_create_stocks_table', 1),
('2015_05_20_150612_create_document_types_table', 1),
('2015_05_20_150704_create_payment_conditions_table', 1),
('2015_05_20_150705_create_purchases_table', 1),
('2015_05_22_160202_create_purchase_details_table', 1),
('2015_06_03_024646_create_colors_table', 1),
('2015_06_03_024647_create_modelos_table', 1),
('2015_06_03_024659_create_versions_table', 1),
('2015_06_03_033028_create_catalog_cars_table', 1),
('2015_06_04_174308_create_feature_groups_table', 1),
('2015_06_04_174309_create_features_table', 1),
('2015_06_05_143840_create_car_quotes_table', 1),
('2015_07_03_230859_create_checkitem_groups_table', 1),
('2015_07_03_230933_create_checkitems_table', 1),
('2015_07_03_231727_create_service_checklists_table', 1),
('2015_07_04_001010_create_service_checklist_checkitem', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelos`
--

CREATE TABLE IF NOT EXISTS `modelos` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `brand_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `modelos`
--

INSERT INTO `modelos` (`id`, `name`, `description`, `image`, `brand_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FIT', '', 'logo_fit.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(2, 'CIVIC', '', 'logo_civic.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(3, 'CIVIC COUPE', '', 'logo_civic.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(4, 'ACCORD', '', 'logo_accord.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(5, 'ACCORD COUPE', '', 'logo_accord.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(6, 'CR-V', '', 'logo_crv.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(7, 'PILOT', '', 'logo_pilot.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(8, 'ODYSSEY', '', 'logo_odyssey.png', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('noel.logan@gmail.com', '2e8d29c0d358ab815c958fc4fcaccf172e2e97d80e7976da6784f35476ff11a8', '2015-09-26 02:24:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_conditions`
--

CREATE TABLE IF NOT EXISTS `payment_conditions` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_sales` tinyint(1) NOT NULL,
  `to_purchases` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `payment_conditions`
--

INSERT INTO `payment_conditions` (`id`, `name`, `description`, `to_sales`, `to_purchases`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CONTADO', '', 1, 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(2, 'CRÉDITO', '', 1, 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permission_group_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `action`, `description`, `permission_group_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Usuarios Listar', 'guard.users.index', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(2, 'Usuarios Ver', 'guard.users.show', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(3, 'Usuarios Crear', 'guard.users.create', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(4, 'Usuarios Editar', 'guard.users.edit', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(5, 'Usuarios Eliminar', 'guard.users.destroy', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(6, 'Roles Listar', 'guard.roles.index', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(7, 'Roles Ver', 'guard.roles.show', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(8, 'Roles Crear', 'guard.roles.create', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(9, 'Roles Editar', 'guard.roles.edit', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(10, 'Roles Eliminar', 'guard.roles.destroy', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(11, 'Grupos Listar', 'guard.permission_groups.index', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(12, 'Grupos Ver', 'guard.permission_groups.show', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(13, 'Grupos Crear', 'guard.permission_groups.create', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(14, 'Grupos Editar', 'guard.permission_groups.edit', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(15, 'Grupos Eliminar', 'guard.permission_groups.destroy', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(16, 'Permisos Listar', 'guard.permissions.index', '', 1, '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(17, 'Permisos Ver', 'guard.permissions.show', '', 1, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(18, 'Permisos Crear', 'guard.permissions.create', '', 1, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(19, 'Permisos Editar', 'guard.permissions.edit', '', 1, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(20, 'Permisos Eliminar', 'guard.permissions.destroy', '', 1, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(21, 'Items Hoja Semáforo Listar', 'autorepair.checkitem_groups.index', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(22, 'Items Hoja Semáforo Ver', 'autorepair.checkitem_groups.show', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(23, 'Items Hoja Semáforo Crear', 'autorepair.checkitem_groups.create', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(24, 'Items Hoja Semáforo Editar', 'autorepair.checkitem_groups.edit', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(25, 'Items Hoja Semáforo Eliminar', 'autorepair.checkitem_groups.destroy', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(26, 'Hoja Semáforo Listar', 'autorepair.service_checklists.index', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(27, 'Hoja Semáforo Ver', 'autorepair.service_checklists.show', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(28, 'Hoja Semáforo Crear', 'autorepair.service_checklists.create', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(29, 'Hoja Semáforo Editar', 'autorepair.service_checklists.edit', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(30, 'Hoja Semáforo Eliminar', 'autorepair.service_checklists.destroy', '', 6, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(31, 'Hoja Semáforo Imprimir', 'autorepair.service_checklists.print', '', 6, '2015-07-17 07:32:49', '2015-07-17 08:18:25', NULL),
(32, 'Colores Listar', 'sales.colors.index', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(33, 'Colores Ver', 'sales.colors.show', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(34, 'Colores Crear', 'sales.colors.create', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(35, 'Colores Editar', 'sales.colors.edit', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(36, 'Colores Eliminar', 'sales.colors.destroy', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(37, 'Grupo de Especificaciones Listar', 'sales.feature_groups.index', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(38, 'Grupo de Especificaciones Ver', 'sales.feature_groups.show', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(39, 'Grupo de Especificaciones Crear', 'sales.feature_groups.create', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(40, 'Grupo de Especificaciones Editar', 'sales.feature_groups.edit', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(41, 'Grupo de Especificaciones Eliminar', 'sales.feature_groups.destroy', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(42, 'Modelos Listar', 'sales.modelos.index', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(43, 'Modelos Ver', 'sales.modelos.show', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(44, 'Modelos Crear', 'sales.modelos.create', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(45, 'Modelos Editar', 'sales.modelos.edit', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(46, 'Modelos Eliminar', 'sales.modelos.destroy', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(47, 'Versiones Listar', 'sales.versions.index', '', 4, '2015-07-17 07:32:49', '2015-07-17 07:32:49', NULL),
(48, 'Versiones Ver', 'sales.versions.show', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(49, 'Versiones Crear', 'sales.versions.create', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(50, 'Versiones Editar', 'sales.versions.edit', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(51, 'Versiones Eliminar', 'sales.versions.destroy', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(52, 'Catálogo de Vehículos Listar', 'sales.catalog_cars.index', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(53, 'Catálogo de Vehículos Ver', 'sales.catalog_cars.show', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(54, 'Catálogo de Vehículos Crear', 'sales.catalog_cars.create', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(55, 'Catálogo de Vehículos Editar', 'sales.catalog_cars.edit', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(56, 'Catálogo de Vehículos Eliminar', 'sales.catalog_cars.destroy', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(57, 'Cotización Listar', 'sales.car_quotes.index', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(58, 'Cotización Ver', 'sales.car_quotes.show', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(59, 'Cotización Crear', 'sales.car_quotes.create', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(60, 'Cotización Editar', 'sales.car_quotes.edit', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(61, 'Cotización Eliminar', 'sales.car_quotes.destroy', '', 4, '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(62, 'Cotización Imprimir', 'sales.car_quotes.print', '', 4, '2015-07-17 07:32:50', '2015-07-17 08:18:07', NULL),
(63, 'HOJA SEMÁFORO PARTE 01', 'autorepair.service_checklists.part01', 'Ingresa o edita la primera parte de la hoja semaforo', 6, '2015-09-14 18:24:01', '2015-09-14 18:24:01', NULL),
(64, 'HOJA SEMÁFORO PARTE 02', 'autorepair.service_checklists.part02', 'Ingresa o edita la segunda parte de la hoja semaforo', 6, '2015-09-14 18:24:46', '2015-09-14 18:24:46', NULL),
(65, 'PROXIMOS SERVICIOS', 'autorepair.vehicles.nextservice', '', 5, '2015-09-21 22:54:23', '2015-09-21 22:54:23', NULL),
(66, 'HOJA SEMAFORO CAMBIAR ESTADO SOLO DESDE INSPECT', 'autorepair.service_checklists.change_status_from_inspect', '', 6, '2015-10-02 15:12:17', '2015-10-02 15:17:07', NULL),
(67, 'HOJA SEMAFORO CAMBIAR ESTADO', 'autorepair.service_checklists.change_status', '', 6, '2015-10-02 15:12:52', '2015-10-02 15:16:44', NULL),
(68, 'HOJA SEMAFORO GRABAR ESTADO', 'autorepair.service_checklists.save_status', '', 6, '2015-10-02 15:19:28', '2015-10-02 15:19:28', NULL),
(69, 'HOJA SEMAFORO EDICION SOLO COMO TECNICO', 'autorepair.service_checklists.edit_as_technical', '', 6, '2015-10-09 17:48:46', '2015-10-09 17:48:46', NULL),
(70, 'HOJA SEMAFORO EDICION SOLO COMO ASESEOR', 'autorepair.service_checklists.edit_as_adviser', '', 6, '2015-10-09 17:49:45', '2015-10-09 17:49:45', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permission_groups`
--

CREATE TABLE IF NOT EXISTS `permission_groups` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `permission_groups`
--

INSERT INTO `permission_groups` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SISTEMAS', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(2, 'ALMACEN', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(3, 'LOGISTICA', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(4, 'VENTAS', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(5, 'POSTVENTA', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(6, 'TALLER', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(7, 'FINANZAS', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(8, 'CONTABILIDAD', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL),
(9, 'ADMINISTRACION', '', '2015-07-17 07:32:48', '2015-07-17 07:32:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL,
  `intern_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manufacturer_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sub_category_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `brand_id` int(10) unsigned NOT NULL DEFAULT '1',
  `currency_id` int(10) unsigned NOT NULL,
  `last_purchase` decimal(15,4) NOT NULL,
  `profit_margin` decimal(10,4) NOT NULL,
  `price` decimal(15,4) NOT NULL,
  `set_price` decimal(15,4) NOT NULL,
  `use_set_price` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases`
--

CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `document_type_id` int(10) unsigned NOT NULL,
  `series` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `dispatch_note_date` date NOT NULL,
  `dispatch_note_series` int(11) NOT NULL,
  `dispatch_note_number` int(11) NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `payment_condition_id` int(10) unsigned NOT NULL,
  `due_date` date NOT NULL,
  `currency_id` int(10) unsigned NOT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `igv` decimal(14,2) NOT NULL,
  `total` decimal(14,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_details`
--

CREATE TABLE IF NOT EXISTS `purchase_details` (
  `id` int(10) unsigned NOT NULL,
  `purchase_id` int(10) unsigned NOT NULL,
  `stock_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADMINISTRADOR DE SISTEMA', '', '2015-07-17 07:32:46', '2015-07-17 07:32:46', NULL),
(2, 'JEFE DE ALMACEN', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(3, 'ASISTENTE DE ALMACEN', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(4, 'JEFE DE COMPRAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(5, 'ASISTENTE DE ADV', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(6, 'ADMINISTRADOR DE VENTAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(7, 'VENDEDOR', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(8, 'JEFE DE VENTAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(9, 'RECEPCIONISTA', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(10, 'ASESOR DE SERVICIO', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(11, 'COORDINADOR DE POSTVENTA', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(12, 'JEFE DE POSTVENTA', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(13, 'JEFE DE TALLER', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(14, 'TECNICO 1', '', '2015-07-17 07:32:47', '2015-09-14 15:40:05', NULL),
(15, 'CAJERO', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(16, 'JEFE DE FINANZAS', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(17, 'ASISTENTE CONTABLE', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(18, 'CONTADOR GENERAL', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(19, 'ASISTENTE ADMINISTRATIVO', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(20, 'GERENTE GENERAL', '', '2015-07-17 07:32:47', '2015-07-17 07:32:47', NULL),
(21, 'TECNICO 2', 'TECNICO 1 + PRUEBA DE MANEJO Y/O INSPECCIÓN FINAL', '2015-09-14 15:37:16', '2015-09-14 15:37:16', NULL),
(22, 'CONTROL DE CALIDAD', '', '2015-09-14 15:39:35', '2015-09-14 15:39:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(10) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 6, 52, '2015-07-17 07:36:24', '2015-07-17 07:36:24'),
(2, 6, 53, '2015-07-17 07:36:24', '2015-07-17 07:36:24'),
(3, 6, 54, '2015-07-17 07:36:24', '2015-07-17 07:36:24'),
(4, 6, 55, '2015-07-17 07:36:25', '2015-07-17 07:36:25'),
(5, 6, 56, '2015-07-17 07:36:25', '2015-07-17 07:36:25'),
(6, 7, 57, '2015-07-17 07:36:38', '2015-07-17 07:36:38'),
(7, 7, 58, '2015-07-17 07:36:39', '2015-07-17 07:36:39'),
(8, 7, 59, '2015-07-17 07:36:39', '2015-07-17 07:36:39'),
(9, 7, 60, '2015-07-17 07:36:39', '2015-07-17 07:36:39'),
(10, 7, 61, '2015-07-17 07:36:39', '2015-07-17 07:36:39'),
(11, 7, 62, '2015-07-17 07:36:39', '2015-07-17 07:36:39'),
(24, 12, 26, '2015-07-17 07:38:18', '2015-07-17 07:38:18'),
(25, 12, 27, '2015-07-17 07:38:18', '2015-07-17 07:38:18'),
(26, 12, 28, '2015-07-17 07:38:18', '2015-07-17 07:38:18'),
(27, 12, 29, '2015-07-17 07:38:18', '2015-07-17 07:38:18'),
(28, 12, 30, '2015-07-17 07:38:19', '2015-07-17 07:38:19'),
(29, 12, 31, '2015-07-17 07:38:19', '2015-07-17 07:38:19'),
(36, 8, 57, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(37, 8, 58, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(38, 8, 59, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(39, 8, 60, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(40, 8, 61, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(41, 8, 62, '2015-07-17 15:45:00', '2015-07-17 15:45:00'),
(77, 1, 1, '2015-09-14 18:03:56', '2015-09-14 18:03:56'),
(83, 21, 26, '2015-09-14 18:27:05', '2015-09-14 18:27:05'),
(84, 21, 27, '2015-09-14 18:27:05', '2015-09-14 18:27:05'),
(86, 21, 29, '2015-09-14 18:27:05', '2015-09-14 18:27:05'),
(87, 21, 63, '2015-09-14 18:27:05', '2015-09-14 18:27:05'),
(88, 21, 64, '2015-09-14 18:27:05', '2015-09-14 18:27:05'),
(90, 14, 26, '2015-09-14 18:28:15', '2015-09-14 18:28:15'),
(91, 14, 27, '2015-09-14 18:28:15', '2015-09-14 18:28:15'),
(92, 14, 28, '2015-09-14 18:28:15', '2015-09-14 18:28:15'),
(93, 14, 29, '2015-09-14 18:28:15', '2015-09-14 18:28:15'),
(94, 14, 63, '2015-09-14 18:28:15', '2015-09-14 18:28:15'),
(95, 10, 26, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(96, 10, 27, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(97, 10, 28, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(98, 10, 29, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(99, 10, 31, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(100, 10, 63, '2015-09-14 18:28:57', '2015-09-14 18:28:57'),
(103, 11, 65, '2015-09-21 23:05:53', '2015-09-21 23:05:53'),
(104, 11, 26, '2015-09-21 23:05:53', '2015-09-21 23:05:53'),
(105, 11, 31, '2015-09-21 23:05:53', '2015-09-21 23:05:53'),
(106, 11, 29, '2015-09-21 23:05:53', '2015-09-21 23:05:53'),
(113, 13, 26, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(114, 13, 27, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(115, 13, 28, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(116, 13, 29, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(117, 13, 31, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(118, 13, 63, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(119, 13, 64, '2015-09-23 16:31:02', '2015-09-23 16:31:02'),
(120, 11, 27, '2015-09-23 16:32:17', '2015-09-23 16:32:17'),
(121, 22, 26, '2015-09-23 16:34:01', '2015-09-23 16:34:01'),
(122, 22, 29, '2015-09-23 16:34:01', '2015-09-23 16:34:01'),
(123, 22, 31, '2015-09-23 16:34:01', '2015-09-23 16:34:01'),
(124, 22, 63, '2015-09-23 16:34:01', '2015-09-23 16:34:01'),
(125, 22, 27, '2015-09-23 16:35:09', '2015-09-23 16:35:09'),
(126, 1, 26, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(127, 1, 27, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(128, 1, 28, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(129, 1, 29, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(130, 1, 30, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(131, 1, 31, '2015-09-25 15:25:43', '2015-09-25 15:25:43'),
(132, 1, 63, '2015-09-25 15:25:44', '2015-09-25 15:25:44'),
(133, 1, 64, '2015-09-25 15:25:44', '2015-09-25 15:25:44'),
(134, 22, 64, '2015-09-25 16:31:12', '2015-09-25 16:31:12'),
(135, 10, 70, '2015-10-10 00:46:11', '2015-10-10 00:46:11'),
(136, 14, 69, '2015-10-10 00:46:26', '2015-10-10 00:46:26'),
(137, 10, 67, '2015-10-10 00:48:28', '2015-10-10 00:48:28'),
(138, 10, 68, '2015-10-10 00:48:28', '2015-10-10 00:48:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_checklists`
--

CREATE TABLE IF NOT EXISTS `service_checklists` (
  `id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `plate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `observation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adviser_id` int(10) unsigned NOT NULL,
  `technician_id` int(10) unsigned NOT NULL,
  `status` enum('process','inspect','rework','approved','printed') COLLATE utf8_unicode_ci DEFAULT 'process',
  `created_by_id` int(10) unsigned NOT NULL,
  `updated_by_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `service_checklists`
--

INSERT INTO `service_checklists` (`id`, `order_id`, `company_name`, `plate`, `date`, `observation`, `adviser_id`, `technician_id`, `status`, `created_by_id`, `updated_by_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 79790, 'RIMACHI JIMENEZ MARTHA', 'D5V-171', '0000-00-00', 'LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT. EAQUE EUM, UT IPSAM QUAM NUMQUAM AUTEM OMNIS MINIMA SUSCIPIT, OFFICIIS VOLUPTATES DELECTUS OFFICIA IPSA NOBIS VOLUPTATE SIMILIQUE, EIUS ALIQUID UNDE EST.', 19, 3, 'process', 0, 1, '2015-07-17 19:18:56', '2015-10-16 09:04:40', NULL),
(2, 81327, 'TISMART PERU S.A.C.', 'ABO-393', '0000-00-00', 'REVISION DE BATERIA EN EL PROXIMO SERVICIO', 18, 3, 'rework', 0, 26, '2015-09-25 16:23:57', '2015-10-16 08:59:19', NULL),
(3, 81322, 'DALY VILLAVICENCIO ANDREA', 'C4B-451', '0000-00-00', '', 21, 10, 'process', 1, 19, '2015-10-16 07:10:09', '2015-10-16 07:13:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `service_checklist_checkitem`
--

CREATE TABLE IF NOT EXISTS `service_checklist_checkitem` (
  `id` int(10) unsigned NOT NULL,
  `service_checklist_id` int(11) NOT NULL,
  `checkitem_id` int(11) NOT NULL,
  `status` enum('success','warning','danger','info') COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `service_checklist_checkitem`
--

INSERT INTO `service_checklist_checkitem` (`id`, `service_checklist_id`, `checkitem_id`, `status`, `value`, `created_at`, `updated_at`) VALUES
(33, 1, 35, 'warning', '34.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(34, 1, 36, 'warning', '4.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(35, 1, 37, 'warning', '3.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(37, 1, 39, 'warning', '1.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(38, 1, 40, 'warning', '0.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(40, 1, 43, 'warning', '2.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(41, 1, 44, 'warning', '1.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(42, 1, 46, 'warning', '4.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(43, 1, 47, 'warning', '6.00', '2015-07-17 19:18:58', '2015-10-16 09:04:40'),
(49, 1, 1, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(50, 1, 2, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(51, 1, 3, 'danger', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(52, 1, 4, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(53, 1, 5, 'warning', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(54, 1, 6, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(55, 1, 7, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(56, 1, 8, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(57, 1, 9, 'warning', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(58, 1, 16, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(59, 1, 17, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(60, 1, 18, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(61, 1, 19, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(62, 1, 20, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(63, 1, 21, 'warning', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(64, 1, 22, 'warning', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(65, 1, 23, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(66, 1, 24, 'success', '0.00', '2015-09-11 15:24:07', '2015-10-16 09:04:40'),
(67, 1, 25, 'danger', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(68, 1, 27, 'success', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(69, 1, 28, 'success', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(70, 1, 29, 'warning', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(71, 1, 30, 'warning', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(72, 1, 31, 'danger', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(73, 1, 32, 'success', '0.00', '2015-09-11 15:24:08', '2015-10-16 09:04:40'),
(81, 1, 38, NULL, '2.00', '2015-09-11 17:59:01', '2015-10-16 09:04:40'),
(82, 1, 41, NULL, '3.00', '2015-09-11 17:59:01', '2015-10-16 09:04:40'),
(128, 2, 1, 'success', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(129, 2, 2, 'success', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(130, 2, 3, 'success', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(131, 2, 4, 'success', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(132, 2, 5, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(133, 2, 6, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:19'),
(134, 2, 7, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(135, 2, 8, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(136, 2, 9, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(137, 2, 16, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(138, 2, 17, 'warning', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(139, 2, 18, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(140, 2, 19, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(141, 2, 20, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(142, 2, 21, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(143, 2, 22, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(144, 2, 23, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(145, 2, 24, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(146, 2, 25, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(147, 2, 27, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(148, 2, 28, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(149, 2, 29, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(150, 2, 30, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(151, 2, 31, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(152, 2, 32, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(153, 2, 35, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(154, 2, 36, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(155, 2, 37, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(156, 2, 38, NULL, '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(157, 2, 39, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(158, 2, 40, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(159, 2, 41, NULL, '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(160, 2, 43, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(161, 2, 44, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(162, 2, 46, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(163, 2, 47, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(164, 2, 48, '', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(165, 2, 49, 'success', '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(166, 2, 50, NULL, '0.00', '2015-10-03 07:02:23', '2015-10-16 08:59:20'),
(167, 2, 51, NULL, '0.00', '2015-10-03 07:02:24', '2015-10-16 08:59:20'),
(168, 3, 1, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(169, 3, 2, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(170, 3, 3, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(171, 3, 4, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(172, 3, 5, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(173, 3, 6, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(174, 3, 7, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(175, 3, 8, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(176, 3, 9, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(177, 3, 16, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(178, 3, 17, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(179, 3, 18, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(180, 3, 19, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(181, 3, 20, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(182, 3, 21, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(183, 3, 22, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(184, 3, 23, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(185, 3, 24, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(186, 3, 25, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(187, 3, 27, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(188, 3, 28, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:35'),
(189, 3, 29, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(190, 3, 30, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(191, 3, 31, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(192, 3, 32, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(193, 3, 35, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(194, 3, 36, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(195, 3, 37, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(196, 3, 38, NULL, '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(197, 3, 39, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(198, 3, 40, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(199, 3, 41, NULL, '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(200, 3, 43, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(201, 3, 44, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(202, 3, 46, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(203, 3, 47, '', '0.00', '2015-10-16 07:10:09', '2015-10-16 07:13:36'),
(209, 2, 0, NULL, '0.00', '2015-10-16 08:59:20', '2015-10-16 08:59:20'),
(216, 1, 48, 'success', '0.00', '2015-10-16 09:04:40', '2015-10-16 09:04:40'),
(217, 1, 49, '', '0.00', '2015-10-16 09:04:40', '2015-10-16 09:04:40'),
(218, 1, 50, NULL, '0.00', '2015-10-16 09:04:40', '2015-10-16 09:04:40'),
(219, 1, 51, NULL, '0.00', '2015-10-16 09:04:40', '2015-10-16 09:04:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stocks`
--

CREATE TABLE IF NOT EXISTS `stocks` (
  `id` int(10) unsigned NOT NULL,
  `warehouse_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `stock` decimal(15,4) NOT NULL,
  `currency_id` int(10) unsigned NOT NULL DEFAULT '1',
  `avarage_value` decimal(8,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sub_categories`
--

CREATE TABLE IF NOT EXISTS `sub_categories` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `name`, `description`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ACCESORIOS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(2, 'ACEITES', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(3, 'CARROCERIA', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(4, 'CHASIS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(5, 'CUERPO / AIRE ACONDICIONADO', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(6, 'DIRECCION', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(7, 'ELECTRICIDAD', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(8, 'ELECTRICO/ESCAPE/CALENT/SUMIN COMBU', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(9, 'EMBRAGUE', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(10, 'FLUIDO', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(11, 'FRENOS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(12, 'INTERIOR / DEFENSAS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(13, 'LLANTAS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(14, 'LUBRICANTES', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(15, 'MATERIALES', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(16, 'MOTOR', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(17, 'PEGAMENTO', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(18, 'REFRIGERACION', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(19, 'SUSPENSION', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(20, 'TRANSMISION', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(21, 'TRANSMISION- AUTOMATICO', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(22, 'TRANSMISION-MANUAL', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(23, 'VARIOS', '', 1, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeos`
--

CREATE TABLE IF NOT EXISTS `ubigeos` (
  `id` int(10) unsigned NOT NULL,
  `departamento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provincia` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `distrito` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=179022214 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ubigeos`
--

INSERT INTO `ubigeos` (`id`, `departamento`, `provincia`, `distrito`) VALUES
(1111, 'AMAZONAS', 'CHACHAPOYAS', 'CHACHAPOYAS'),
(2112, 'AMAZONAS', 'CHACHAPOYAS', 'ASUNCION'),
(3113, 'AMAZONAS', 'CHACHAPOYAS', 'BALSAS'),
(4114, 'AMAZONAS', 'CHACHAPOYAS', 'CHETO'),
(5115, 'AMAZONAS', 'CHACHAPOYAS', 'CHILIQUIN'),
(6116, 'AMAZONAS', 'CHACHAPOYAS', 'CHUQUIBAMBA'),
(7117, 'AMAZONAS', 'CHACHAPOYAS', 'GRANADA'),
(8118, 'AMAZONAS', 'CHACHAPOYAS', 'HUANCAS'),
(9119, 'AMAZONAS', 'CHACHAPOYAS', 'LA JALCA'),
(22121, 'AMAZONAS', 'BAGUA', 'LA PECA'),
(23122, 'AMAZONAS', 'BAGUA', 'ARAMANGO'),
(24123, 'AMAZONAS', 'BAGUA', 'COPALLIN'),
(25124, 'AMAZONAS', 'BAGUA', 'EL PARCO'),
(26126, 'AMAZONAS', 'BAGUA', 'IMAZA'),
(27131, 'AMAZONAS', 'BONGARA', 'JUMBILLA'),
(28132, 'AMAZONAS', 'BONGARA', 'COROSHA'),
(29133, 'AMAZONAS', 'BONGARA', 'CUISPES'),
(30134, 'AMAZONAS', 'BONGARA', 'CHISQUILLA'),
(31135, 'AMAZONAS', 'BONGARA', 'CHURUJA'),
(32136, 'AMAZONAS', 'BONGARA', 'FLORIDA'),
(33137, 'AMAZONAS', 'BONGARA', 'RECTA'),
(34138, 'AMAZONAS', 'BONGARA', 'SAN CARLOS'),
(35139, 'AMAZONAS', 'BONGARA', 'SHIPASBAMBA'),
(39141, 'AMAZONAS', 'LUYA', 'LAMUD'),
(40142, 'AMAZONAS', 'LUYA', 'CAMPORREDONDO'),
(41143, 'AMAZONAS', 'LUYA', 'COCABAMBA'),
(42144, 'AMAZONAS', 'LUYA', 'COLCAMAR'),
(43145, 'AMAZONAS', 'LUYA', 'CONILA'),
(44146, 'AMAZONAS', 'LUYA', 'INGUILPATA'),
(45147, 'AMAZONAS', 'LUYA', 'LONGUITA'),
(46148, 'AMAZONAS', 'LUYA', 'LONYA CHICO'),
(47149, 'AMAZONAS', 'LUYA', 'LUYA'),
(62151, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'SAN NICOLAS'),
(63152, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'COCHAMAL'),
(64153, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'CHIRIMOTO'),
(65154, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'HUAMBO'),
(66155, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'LIMABAMBA'),
(67156, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'LONGAR'),
(68157, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'MILPUC'),
(69158, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'MCAL BENAVIDES'),
(70159, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'OMIA'),
(74161, 'AMAZONAS', 'CONDORCANQUI', 'NIEVA'),
(75162, 'AMAZONAS', 'CONDORCANQUI', 'RIO SANTIAGO'),
(76163, 'AMAZONAS', 'CONDORCANQUI', 'EL CENEPA'),
(77171, 'AMAZONAS', 'UTCUBAMBA', 'BAGUA GRANDE'),
(78172, 'AMAZONAS', 'UTCUBAMBA', 'CAJARURO'),
(79173, 'AMAZONAS', 'UTCUBAMBA', 'CUMBA'),
(80174, 'AMAZONAS', 'UTCUBAMBA', 'EL MILAGRO'),
(81175, 'AMAZONAS', 'UTCUBAMBA', 'JAMALCA'),
(82176, 'AMAZONAS', 'UTCUBAMBA', 'LONYA GRANDE'),
(83177, 'AMAZONAS', 'UTCUBAMBA', 'YAMON'),
(84211, 'ANCASH', 'HUARAZ', 'HUARAZ'),
(85212, 'ANCASH', 'HUARAZ', 'INDEPENDENCIA'),
(86213, 'ANCASH', 'HUARAZ', 'COCHABAMBA'),
(87214, 'ANCASH', 'HUARAZ', 'COLCABAMBA'),
(88215, 'ANCASH', 'HUARAZ', 'HUANCHAY'),
(89216, 'ANCASH', 'HUARAZ', 'JANGAS'),
(90217, 'ANCASH', 'HUARAZ', 'LA LIBERTAD'),
(91218, 'ANCASH', 'HUARAZ', 'OLLEROS'),
(92219, 'ANCASH', 'HUARAZ', 'PAMPAS'),
(96221, 'ANCASH', 'AIJA', 'AIJA'),
(97223, 'ANCASH', 'AIJA', 'CORIS'),
(98225, 'ANCASH', 'AIJA', 'HUACLLAN'),
(99226, 'ANCASH', 'AIJA', 'LA MERCED'),
(100228, 'ANCASH', 'AIJA', 'SUCCHA'),
(101110, 'AMAZONAS', 'CHACHAPOYAS', 'LEIMEBAMBA'),
(101231, 'ANCASH', 'BOLOGNESI', 'CHIQUIAN'),
(102232, 'ANCASH', 'BOLOGNESI', 'A PARDO LEZAMETA'),
(103234, 'ANCASH', 'BOLOGNESI', 'AQUIA'),
(104235, 'ANCASH', 'BOLOGNESI', 'CAJACAY'),
(111111, 'AMAZONAS', 'CHACHAPOYAS', 'LEVANTO'),
(116241, 'ANCASH', 'CARHUAZ', 'CARHUAZ'),
(117242, 'ANCASH', 'CARHUAZ', 'ACOPAMPA'),
(118243, 'ANCASH', 'CARHUAZ', 'AMASHCA'),
(119244, 'ANCASH', 'CARHUAZ', 'ANTA'),
(120245, 'ANCASH', 'CARHUAZ', 'ATAQUERO'),
(121112, 'AMAZONAS', 'CHACHAPOYAS', 'MAGDALENA'),
(121246, 'ANCASH', 'CARHUAZ', 'MARCARA'),
(122247, 'ANCASH', 'CARHUAZ', 'PARIAHUANCA'),
(123248, 'ANCASH', 'CARHUAZ', 'SAN MIGUEL DE ACO'),
(124249, 'ANCASH', 'CARHUAZ', 'SHILLA'),
(127251, 'ANCASH', 'CASMA', 'CASMA'),
(128252, 'ANCASH', 'CASMA', 'BUENA VISTA ALTA'),
(129253, 'ANCASH', 'CASMA', 'COMANDANTE NOEL'),
(130255, 'ANCASH', 'CASMA', 'YAUTAN'),
(131113, 'AMAZONAS', 'CHACHAPOYAS', 'MARISCAL CASTILLA'),
(131261, 'ANCASH', 'CORONGO', 'CORONGO'),
(132262, 'ANCASH', 'CORONGO', 'ACO'),
(133263, 'ANCASH', 'CORONGO', 'BAMBAS'),
(134264, 'ANCASH', 'CORONGO', 'CUSCA'),
(135265, 'ANCASH', 'CORONGO', 'LA PAMPA'),
(136266, 'ANCASH', 'CORONGO', 'YANAC'),
(137267, 'ANCASH', 'CORONGO', 'YUPAN'),
(138271, 'ANCASH', 'HUAYLAS', 'CARAZ'),
(139272, 'ANCASH', 'HUAYLAS', 'HUALLANCA'),
(140273, 'ANCASH', 'HUAYLAS', 'HUATA'),
(141114, 'AMAZONAS', 'CHACHAPOYAS', 'MOLINOPAMPA'),
(141274, 'ANCASH', 'HUAYLAS', 'HUAYLAS'),
(142275, 'ANCASH', 'HUAYLAS', 'MATO'),
(143276, 'ANCASH', 'HUAYLAS', 'PAMPAROMAS'),
(144277, 'ANCASH', 'HUAYLAS', 'PUEBLO LIBRE'),
(145278, 'ANCASH', 'HUAYLAS', 'SANTA CRUZ'),
(146279, 'ANCASH', 'HUAYLAS', 'YURACMARCA'),
(148281, 'ANCASH', 'HUARI', 'HUARI'),
(149282, 'ANCASH', 'HUARI', 'CAJAY'),
(150283, 'ANCASH', 'HUARI', 'CHAVIN DE HUANTAR'),
(151115, 'AMAZONAS', 'CHACHAPOYAS', 'MONTEVIDEO'),
(151284, 'ANCASH', 'HUARI', 'HUACACHI'),
(152285, 'ANCASH', 'HUARI', 'HUACHIS'),
(153286, 'ANCASH', 'HUARI', 'HUACCHIS'),
(154287, 'ANCASH', 'HUARI', 'HUANTAR'),
(155288, 'ANCASH', 'HUARI', 'MASIN'),
(156289, 'ANCASH', 'HUARI', 'PAUCAS'),
(161116, 'AMAZONAS', 'CHACHAPOYAS', 'OLLEROS'),
(164291, 'ANCASH', 'MARISCAL LUZURIAGA', 'PISCOBAMBA'),
(165292, 'ANCASH', 'MARISCAL LUZURIAGA', 'CASCA'),
(166293, 'ANCASH', 'MARISCAL LUZURIAGA', 'LUCMA'),
(167294, 'ANCASH', 'MARISCAL LUZURIAGA', 'FIDEL OLIVAS ESCUDERO'),
(168295, 'ANCASH', 'MARISCAL LUZURIAGA', 'LLAMA'),
(169296, 'ANCASH', 'MARISCAL LUZURIAGA', 'LLUMPA'),
(170297, 'ANCASH', 'MARISCAL LUZURIAGA', 'MUSGA'),
(171117, 'AMAZONAS', 'CHACHAPOYAS', 'QUINJALCA'),
(171298, 'ANCASH', 'MARISCAL LUZURIAGA', 'ELEAZAR GUZMAN BARRON'),
(181118, 'AMAZONAS', 'CHACHAPOYAS', 'SAN FCO DE DAGUAS'),
(191119, 'AMAZONAS', 'CHACHAPOYAS', 'SAN ISIDRO DE MAINO'),
(201120, 'AMAZONAS', 'CHACHAPOYAS', 'SOLOCO'),
(211121, 'AMAZONAS', 'CHACHAPOYAS', 'SONCHE'),
(250311, 'APURIMAC', 'ABANCAY', 'ABANCAY'),
(251312, 'APURIMAC', 'ABANCAY', 'CIRCA'),
(252313, 'APURIMAC', 'ABANCAY', 'CURAHUASI'),
(253314, 'APURIMAC', 'ABANCAY', 'CHACOCHE'),
(254315, 'APURIMAC', 'ABANCAY', 'HUANIPACA'),
(255316, 'APURIMAC', 'ABANCAY', 'LAMBRAMA'),
(256317, 'APURIMAC', 'ABANCAY', 'PICHIRHUA'),
(257318, 'APURIMAC', 'ABANCAY', 'SAN PEDRO DE CACHORA'),
(258319, 'APURIMAC', 'ABANCAY', 'TAMBURCO'),
(259321, 'APURIMAC', 'AYMARAES', 'CHALHUANCA'),
(260322, 'APURIMAC', 'AYMARAES', 'CAPAYA'),
(261323, 'APURIMAC', 'AYMARAES', 'CARAYBAMBA'),
(262324, 'APURIMAC', 'AYMARAES', 'COLCABAMBA'),
(263325, 'APURIMAC', 'AYMARAES', 'COTARUSE'),
(264326, 'APURIMAC', 'AYMARAES', 'CHAPIMARCA'),
(265327, 'APURIMAC', 'AYMARAES', 'IHUAYLLO'),
(266328, 'APURIMAC', 'AYMARAES', 'LUCRE'),
(267329, 'APURIMAC', 'AYMARAES', 'POCOHUANCA'),
(276331, 'APURIMAC', 'ANDAHUAYLAS', 'ANDAHUAYLAS'),
(277332, 'APURIMAC', 'ANDAHUAYLAS', 'ANDARAPA'),
(278333, 'APURIMAC', 'ANDAHUAYLAS', 'CHIARA'),
(279334, 'APURIMAC', 'ANDAHUAYLAS', 'HUANCARAMA'),
(280335, 'APURIMAC', 'ANDAHUAYLAS', 'HUANCARAY'),
(281336, 'APURIMAC', 'ANDAHUAYLAS', 'KISHUARA'),
(282337, 'APURIMAC', 'ANDAHUAYLAS', 'PACOBAMBA'),
(283338, 'APURIMAC', 'ANDAHUAYLAS', 'PAMPACHIRI'),
(284339, 'APURIMAC', 'ANDAHUAYLAS', 'SAN ANTONIO DE CACHI'),
(295341, 'APURIMAC', 'ANTABAMBA', 'ANTABAMBA'),
(296342, 'APURIMAC', 'ANTABAMBA', 'EL ORO'),
(297343, 'APURIMAC', 'ANTABAMBA', 'HUAQUIRCA'),
(298344, 'APURIMAC', 'ANTABAMBA', 'JUAN ESPINOZA MEDRANO'),
(299345, 'APURIMAC', 'ANTABAMBA', 'OROPESA'),
(300346, 'APURIMAC', 'ANTABAMBA', 'PACHACONAS'),
(301347, 'APURIMAC', 'ANTABAMBA', 'SABAINO'),
(302351, 'APURIMAC', 'COTABAMBAS', 'TAMBOBAMBA'),
(303352, 'APURIMAC', 'COTABAMBAS', 'COYLLURQUI'),
(304353, 'APURIMAC', 'COTABAMBAS', 'COTABAMBAS'),
(305354, 'APURIMAC', 'COTABAMBAS', 'HAQUIRA'),
(306355, 'APURIMAC', 'COTABAMBAS', 'MARA'),
(307356, 'APURIMAC', 'COTABAMBAS', 'CHALLHUAHUACHO'),
(308361, 'APURIMAC', 'GRAU', 'CHUQUIBAMBILLA'),
(309362, 'APURIMAC', 'GRAU', 'CURPAHUASI'),
(310363, 'APURIMAC', 'GRAU', 'HUAILLATI'),
(311364, 'APURIMAC', 'GRAU', 'MAMARA'),
(312365, 'APURIMAC', 'GRAU', 'MARISCAL GAMARRA'),
(313366, 'APURIMAC', 'GRAU', 'MICAELA BASTIDAS'),
(314367, 'APURIMAC', 'GRAU', 'PROGRESO'),
(315368, 'APURIMAC', 'GRAU', 'PATAYPAMPA'),
(316369, 'APURIMAC', 'GRAU', 'SAN ANTONIO'),
(322371, 'APURIMAC', 'CHINCHEROS', 'CHINCHEROS'),
(323372, 'APURIMAC', 'CHINCHEROS', 'ONGOY'),
(324373, 'APURIMAC', 'CHINCHEROS', 'OCOBAMBA'),
(325374, 'APURIMAC', 'CHINCHEROS', 'COCHARCAS'),
(326375, 'APURIMAC', 'CHINCHEROS', 'ANCO HUALLO'),
(327376, 'APURIMAC', 'CHINCHEROS', 'HUACCANA'),
(328377, 'APURIMAC', 'CHINCHEROS', 'URANMARCA'),
(329378, 'APURIMAC', 'CHINCHEROS', 'RANRACANCHA'),
(330411, 'AREQUIPA', 'AREQUIPA', 'AREQUIPA'),
(331412, 'AREQUIPA', 'AREQUIPA', 'CAYMA'),
(332413, 'AREQUIPA', 'AREQUIPA', 'CERRO COLORADO'),
(333414, 'AREQUIPA', 'AREQUIPA', 'CHARACATO'),
(334415, 'AREQUIPA', 'AREQUIPA', 'CHIGUATA'),
(335416, 'AREQUIPA', 'AREQUIPA', 'LA JOYA'),
(336417, 'AREQUIPA', 'AREQUIPA', 'MIRAFLORES'),
(337418, 'AREQUIPA', 'AREQUIPA', 'MOLLEBAYA'),
(338419, 'AREQUIPA', 'AREQUIPA', 'PAUCARPATA'),
(359421, 'AREQUIPA', 'CAYLLOMA', 'CHIVAY'),
(360422, 'AREQUIPA', 'CAYLLOMA', 'ACHOMA'),
(361310, 'AMAZONAS', 'BONGARA', 'VALERA'),
(361423, 'AREQUIPA', 'CAYLLOMA', 'CABANACONDE'),
(362424, 'AREQUIPA', 'CAYLLOMA', 'CAYLLOMA'),
(363425, 'AREQUIPA', 'CAYLLOMA', 'CALLALLI'),
(364426, 'AREQUIPA', 'CAYLLOMA', 'COPORAQUE'),
(365427, 'AREQUIPA', 'CAYLLOMA', 'HUAMBO'),
(366428, 'AREQUIPA', 'CAYLLOMA', 'HUANCA'),
(367429, 'AREQUIPA', 'CAYLLOMA', 'ICHUPAMPA'),
(371311, 'AMAZONAS', 'BONGARA', 'YAMBRASBAMBA'),
(379431, 'AREQUIPA', 'CAMANA', 'CAMANA'),
(380432, 'AREQUIPA', 'CAMANA', 'JOSE MARIA QUIMPER'),
(381312, 'AMAZONAS', 'BONGARA', 'JAZAN'),
(381433, 'AREQUIPA', 'CAMANA', 'MARIANO N VALCARCEL'),
(382434, 'AREQUIPA', 'CAMANA', 'MARISCAL CACERES'),
(383435, 'AREQUIPA', 'CAMANA', 'NICOLAS DE PIEROLA'),
(384436, 'AREQUIPA', 'CAMANA', 'OCOÑA'),
(385437, 'AREQUIPA', 'CAMANA', 'QUILCA'),
(386438, 'AREQUIPA', 'CAMANA', 'SAMUEL PASTOR'),
(387441, 'AREQUIPA', 'CARAVELI', 'CARAVELI'),
(388442, 'AREQUIPA', 'CARAVELI', 'ACARI'),
(389443, 'AREQUIPA', 'CARAVELI', 'ATICO'),
(390444, 'AREQUIPA', 'CARAVELI', 'ATIQUIPA'),
(391445, 'AREQUIPA', 'CARAVELI', 'BELLA UNION'),
(392446, 'AREQUIPA', 'CARAVELI', 'CAHUACHO'),
(393447, 'AREQUIPA', 'CARAVELI', 'CHALA'),
(394448, 'AREQUIPA', 'CARAVELI', 'CHAPARRA'),
(395449, 'AREQUIPA', 'CARAVELI', 'HUANUHUANU'),
(400451, 'AREQUIPA', 'CASTILLA', 'APLAO'),
(401452, 'AREQUIPA', 'CASTILLA', 'ANDAGUA'),
(402453, 'AREQUIPA', 'CASTILLA', 'AYO'),
(403454, 'AREQUIPA', 'CASTILLA', 'CHACHAS'),
(404455, 'AREQUIPA', 'CASTILLA', 'CHILCAYMARCA'),
(405456, 'AREQUIPA', 'CASTILLA', 'CHOCO'),
(406457, 'AREQUIPA', 'CASTILLA', 'HUANCARQUI'),
(407458, 'AREQUIPA', 'CASTILLA', 'MACHAGUAY'),
(408459, 'AREQUIPA', 'CASTILLA', 'ORCOPAMPA'),
(414461, 'AREQUIPA', 'CONDESUYOS', 'CHUQUIBAMBA'),
(415462, 'AREQUIPA', 'CONDESUYOS', 'ANDARAY'),
(416463, 'AREQUIPA', 'CONDESUYOS', 'CAYARANI'),
(417464, 'AREQUIPA', 'CONDESUYOS', 'CHICHAS'),
(418465, 'AREQUIPA', 'CONDESUYOS', 'IRAY'),
(419466, 'AREQUIPA', 'CONDESUYOS', 'SALAMANCA'),
(420467, 'AREQUIPA', 'CONDESUYOS', 'YANAQUIHUA'),
(421468, 'AREQUIPA', 'CONDESUYOS', 'RIO GRANDE'),
(422471, 'AREQUIPA', 'ISLAY', 'MOLLENDO'),
(423472, 'AREQUIPA', 'ISLAY', 'COCACHACRA'),
(424473, 'AREQUIPA', 'ISLAY', 'DEAN VALDIVIA'),
(425474, 'AREQUIPA', 'ISLAY', 'ISLAY'),
(426475, 'AREQUIPA', 'ISLAY', 'MEJIA'),
(427476, 'AREQUIPA', 'ISLAY', 'PUNTA DE BOMBON'),
(428481, 'AREQUIPA', 'LA UNION', 'COTAHUASI'),
(429482, 'AREQUIPA', 'LA UNION', 'ALCA'),
(430483, 'AREQUIPA', 'LA UNION', 'CHARCANA'),
(431484, 'AREQUIPA', 'LA UNION', 'HUAYNACOTAS'),
(432485, 'AREQUIPA', 'LA UNION', 'PAMPAMARCA'),
(433486, 'AREQUIPA', 'LA UNION', 'PUYCA'),
(434487, 'AREQUIPA', 'LA UNION', 'QUECHUALLA'),
(435488, 'AREQUIPA', 'LA UNION', 'SAYLA'),
(436489, 'AREQUIPA', 'LA UNION', 'TAURIA'),
(439511, 'AYACUCHO', 'HUAMANGA', 'AYACUCHO'),
(440512, 'AYACUCHO', 'HUAMANGA', 'ACOS VINCHOS'),
(441513, 'AYACUCHO', 'HUAMANGA', 'CARMEN ALTO'),
(442514, 'AYACUCHO', 'HUAMANGA', 'CHIARA'),
(443515, 'AYACUCHO', 'HUAMANGA', 'QUINUA'),
(444516, 'AYACUCHO', 'HUAMANGA', 'SAN JOSE DE TICLLAS'),
(445517, 'AYACUCHO', 'HUAMANGA', 'SAN JUAN BAUTISTA'),
(446518, 'AYACUCHO', 'HUAMANGA', 'SANTIAGO DE PISCHA'),
(447519, 'AYACUCHO', 'HUAMANGA', 'VINCHOS'),
(454521, 'AYACUCHO', 'CANGALLO', 'CANGALLO'),
(455524, 'AYACUCHO', 'CANGALLO', 'CHUSCHI'),
(456526, 'AYACUCHO', 'CANGALLO', 'LOS MOROCHUCOS'),
(457527, 'AYACUCHO', 'CANGALLO', 'PARAS'),
(458528, 'AYACUCHO', 'CANGALLO', 'TOTOS'),
(460531, 'AYACUCHO', 'HUANTA', 'HUANTA'),
(461532, 'AYACUCHO', 'HUANTA', 'AYAHUANCO'),
(462533, 'AYACUCHO', 'HUANTA', 'HUAMANGUILLA'),
(463534, 'AYACUCHO', 'HUANTA', 'IGUAIN'),
(464535, 'AYACUCHO', 'HUANTA', 'LURICOCHA'),
(465537, 'AYACUCHO', 'HUANTA', 'SANTILLANA'),
(466538, 'AYACUCHO', 'HUANTA', 'SIVIA'),
(467539, 'AYACUCHO', 'HUANTA', 'LLOCHEGUA'),
(468541, 'AYACUCHO', 'LA MAR', 'SAN MIGUEL'),
(469542, 'AYACUCHO', 'LA MAR', 'ANCO'),
(470543, 'AYACUCHO', 'LA MAR', 'AYNA'),
(471544, 'AYACUCHO', 'LA MAR', 'CHILCAS'),
(472545, 'AYACUCHO', 'LA MAR', 'CHUNGUI'),
(473546, 'AYACUCHO', 'LA MAR', 'TAMBO'),
(474547, 'AYACUCHO', 'LA MAR', 'LUIS CARRANZA'),
(475548, 'AYACUCHO', 'LA MAR', 'SANTA ROSA'),
(476551, 'AYACUCHO', 'LUCANAS', 'PUQUIO'),
(477552, 'AYACUCHO', 'LUCANAS', 'AUCARA'),
(478553, 'AYACUCHO', 'LUCANAS', 'CABANA'),
(479554, 'AYACUCHO', 'LUCANAS', 'CARMEN SALCEDO'),
(480556, 'AYACUCHO', 'LUCANAS', 'CHAVIÑA'),
(481410, 'AMAZONAS', 'LUYA', 'LUYA VIEJO'),
(481558, 'AYACUCHO', 'LUCANAS', 'CHIPAO'),
(491411, 'AMAZONAS', 'LUYA', 'MARIA'),
(497561, 'AYACUCHO', 'PARINACOCHAS', 'CORACORA'),
(498564, 'AYACUCHO', 'PARINACOCHAS', 'CORONEL CASTAÑEDA'),
(499565, 'AYACUCHO', 'PARINACOCHAS', 'CHUMPI'),
(500568, 'AYACUCHO', 'PARINACOCHAS', 'PACAPAUSA'),
(501412, 'AMAZONAS', 'LUYA', 'OCALLI'),
(505571, 'AYACUCHO', 'VICTOR FAJARDO', 'HUANCAPI'),
(506572, 'AYACUCHO', 'VICTOR FAJARDO', 'ALCAMENCA'),
(507573, 'AYACUCHO', 'VICTOR FAJARDO', 'APONGO'),
(508574, 'AYACUCHO', 'VICTOR FAJARDO', 'CANARIA'),
(509576, 'AYACUCHO', 'VICTOR FAJARDO', 'CAYARA'),
(510577, 'AYACUCHO', 'VICTOR FAJARDO', 'COLCA'),
(511413, 'AMAZONAS', 'LUYA', 'OCUMAL'),
(511578, 'AYACUCHO', 'VICTOR FAJARDO', 'HUAYA'),
(512579, 'AYACUCHO', 'VICTOR FAJARDO', 'HUAMANQUIQUIA'),
(517581, 'AYACUCHO', 'HUANCA SANCOS', 'SANCOS'),
(518582, 'AYACUCHO', 'HUANCA SANCOS', 'SACSAMARCA'),
(519583, 'AYACUCHO', 'HUANCA SANCOS', 'SANTIAGO DE LUCANAMARCA'),
(520584, 'AYACUCHO', 'HUANCA SANCOS', 'CARAPO'),
(521414, 'AMAZONAS', 'LUYA', 'PISUQUIA'),
(521591, 'AYACUCHO', 'VILCAS HUAMAN', 'VILCAS HUAMAN'),
(522592, 'AYACUCHO', 'VILCAS HUAMAN', 'VISCHONGO'),
(523593, 'AYACUCHO', 'VILCAS HUAMAN', 'ACCOMARCA'),
(524594, 'AYACUCHO', 'VILCAS HUAMAN', 'CARHUANCA'),
(525595, 'AYACUCHO', 'VILCAS HUAMAN', 'CONCEPCION'),
(526596, 'AYACUCHO', 'VILCAS HUAMAN', 'HUAMBALPA'),
(527597, 'AYACUCHO', 'VILCAS HUAMAN', 'SAURAMA'),
(528598, 'AYACUCHO', 'VILCAS HUAMAN', 'INDEPENDENCIA'),
(531415, 'AMAZONAS', 'LUYA', 'SAN CRISTOBAL'),
(541416, 'AMAZONAS', 'LUYA', 'SAN FRANCISCO DE YESO'),
(550611, 'CAJAMARCA', 'CAJAMARCA', 'CAJAMARCA'),
(551417, 'AMAZONAS', 'LUYA', 'SAN JERONIMO'),
(551612, 'CAJAMARCA', 'CAJAMARCA', 'ASUNCION'),
(552613, 'CAJAMARCA', 'CAJAMARCA', 'COSPAN'),
(553614, 'CAJAMARCA', 'CAJAMARCA', 'CHETILLA'),
(554615, 'CAJAMARCA', 'CAJAMARCA', 'ENCAÑADA'),
(555616, 'CAJAMARCA', 'CAJAMARCA', 'JESUS'),
(556617, 'CAJAMARCA', 'CAJAMARCA', 'LOS BAÑOS DEL INCA'),
(557618, 'CAJAMARCA', 'CAJAMARCA', 'LLACANORA'),
(558619, 'CAJAMARCA', 'CAJAMARCA', 'MAGDALENA'),
(561418, 'AMAZONAS', 'LUYA', 'SAN JUAN DE LOPECANCHA'),
(562621, 'CAJAMARCA', 'CAJABAMBA', 'CAJABAMBA'),
(563622, 'CAJAMARCA', 'CAJABAMBA', 'CACHACHI'),
(564623, 'CAJAMARCA', 'CAJABAMBA', 'CONDEBAMBA'),
(565625, 'CAJAMARCA', 'CAJABAMBA', 'SITACOCHA'),
(566631, 'CAJAMARCA', 'CELENDIN', 'CELENDIN'),
(567632, 'CAJAMARCA', 'CELENDIN', 'CORTEGANA'),
(568633, 'CAJAMARCA', 'CELENDIN', 'CHUMUCH'),
(569634, 'CAJAMARCA', 'CELENDIN', 'HUASMIN'),
(570635, 'CAJAMARCA', 'CELENDIN', 'JORGE CHAVEZ'),
(571419, 'AMAZONAS', 'LUYA', 'SANTA CATALINA'),
(571636, 'CAJAMARCA', 'CELENDIN', 'JOSE GALVEZ'),
(572637, 'CAJAMARCA', 'CELENDIN', 'MIGUEL IGLESIAS'),
(573638, 'CAJAMARCA', 'CELENDIN', 'OXAMARCA'),
(574639, 'CAJAMARCA', 'CELENDIN', 'SOROCHUCO'),
(578641, 'CAJAMARCA', 'CONTUMAZA', 'CONTUMAZA'),
(579643, 'CAJAMARCA', 'CONTUMAZA', 'CHILETE'),
(580644, 'CAJAMARCA', 'CONTUMAZA', 'GUZMANGO'),
(581420, 'AMAZONAS', 'LUYA', 'SANTO TOMAS'),
(581645, 'CAJAMARCA', 'CONTUMAZA', 'SAN BENITO'),
(582646, 'CAJAMARCA', 'CONTUMAZA', 'CUPISNIQUE'),
(583647, 'CAJAMARCA', 'CONTUMAZA', 'TANTARICA'),
(584648, 'CAJAMARCA', 'CONTUMAZA', 'YONAN'),
(585649, 'CAJAMARCA', 'CONTUMAZA', 'STA CRUZ DE TOLEDO'),
(586651, 'CAJAMARCA', 'CUTERVO', 'CUTERVO'),
(587652, 'CAJAMARCA', 'CUTERVO', 'CALLAYUC'),
(588653, 'CAJAMARCA', 'CUTERVO', 'CUJILLO'),
(589654, 'CAJAMARCA', 'CUTERVO', 'CHOROS'),
(590655, 'CAJAMARCA', 'CUTERVO', 'LA RAMADA'),
(591421, 'AMAZONAS', 'LUYA', 'TINGO'),
(591656, 'CAJAMARCA', 'CUTERVO', 'PIMPINGOS'),
(592657, 'CAJAMARCA', 'CUTERVO', 'QUEROCOTILLO'),
(593658, 'CAJAMARCA', 'CUTERVO', 'SAN ANDRES DE CUTERVO'),
(594659, 'CAJAMARCA', 'CUTERVO', 'SAN JUAN DE CUTERVO'),
(601422, 'AMAZONAS', 'LUYA', 'TRITA'),
(601661, 'CAJAMARCA', 'CHOTA', 'CHOTA'),
(602662, 'CAJAMARCA', 'CHOTA', 'ANGUIA'),
(603663, 'CAJAMARCA', 'CHOTA', 'COCHABAMBA'),
(604664, 'CAJAMARCA', 'CHOTA', 'CONCHAN'),
(605665, 'CAJAMARCA', 'CHOTA', 'CHADIN'),
(606666, 'CAJAMARCA', 'CHOTA', 'CHIGUIRIP'),
(607667, 'CAJAMARCA', 'CHOTA', 'CHIMBAN'),
(608668, 'CAJAMARCA', 'CHOTA', 'HUAMBOS'),
(609669, 'CAJAMARCA', 'CHOTA', 'LAJAS'),
(611423, 'AMAZONAS', 'LUYA', 'PROVIDENCIA'),
(620671, 'CAJAMARCA', 'HUALGAYOC', 'BAMBAMARCA'),
(621672, 'CAJAMARCA', 'HUALGAYOC', 'CHUGUR'),
(622673, 'CAJAMARCA', 'HUALGAYOC', 'HUALGAYOC'),
(623681, 'CAJAMARCA', 'JAEN', 'JAEN'),
(624682, 'CAJAMARCA', 'JAEN', 'BELLAVISTA'),
(625683, 'CAJAMARCA', 'JAEN', 'COLASAY'),
(626684, 'CAJAMARCA', 'JAEN', 'CHONTALI'),
(627685, 'CAJAMARCA', 'JAEN', 'POMAHUACA'),
(628686, 'CAJAMARCA', 'JAEN', 'PUCARA'),
(629687, 'CAJAMARCA', 'JAEN', 'SALLIQUE'),
(630688, 'CAJAMARCA', 'JAEN', 'SAN FELIPE'),
(631689, 'CAJAMARCA', 'JAEN', 'SAN JOSE DEL ALTO'),
(635691, 'CAJAMARCA', 'SANTA CRUZ', 'SANTA CRUZ'),
(636692, 'CAJAMARCA', 'SANTA CRUZ', 'CATACHE'),
(637693, 'CAJAMARCA', 'SANTA CRUZ', 'CHANCAIBAÑOS'),
(638694, 'CAJAMARCA', 'SANTA CRUZ', 'LA ESPERANZA'),
(639695, 'CAJAMARCA', 'SANTA CRUZ', 'NINABAMBA'),
(640696, 'CAJAMARCA', 'SANTA CRUZ', 'PULAN'),
(641697, 'CAJAMARCA', 'SANTA CRUZ', 'SEXI'),
(642698, 'CAJAMARCA', 'SANTA CRUZ', 'UTICYACU'),
(643699, 'CAJAMARCA', 'SANTA CRUZ', 'YAUYUCAN'),
(677711, 'CUSCO', 'CUSCO', 'CUSCO'),
(678712, 'CUSCO', 'CUSCO', 'CCORCA'),
(679713, 'CUSCO', 'CUSCO', 'POROY'),
(680714, 'CUSCO', 'CUSCO', 'SAN JERONIMO'),
(681715, 'CUSCO', 'CUSCO', 'SAN SEBASTIAN'),
(682716, 'CUSCO', 'CUSCO', 'SANTIAGO'),
(683717, 'CUSCO', 'CUSCO', 'SAYLLA'),
(684718, 'CUSCO', 'CUSCO', 'WANCHAQ'),
(685721, 'CUSCO', 'ACOMAYO', 'ACOMAYO'),
(686722, 'CUSCO', 'ACOMAYO', 'ACOPIA'),
(687723, 'CUSCO', 'ACOMAYO', 'ACOS'),
(688724, 'CUSCO', 'ACOMAYO', 'POMACANCHI'),
(689725, 'CUSCO', 'ACOMAYO', 'RONDOCAN'),
(690726, 'CUSCO', 'ACOMAYO', 'SANGARARA'),
(691727, 'CUSCO', 'ACOMAYO', 'MOSOC LLACTA'),
(692731, 'CUSCO', 'ANTA', 'ANTA'),
(693732, 'CUSCO', 'ANTA', 'CHINCHAYPUJIO'),
(694733, 'CUSCO', 'ANTA', 'HUAROCONDO'),
(695734, 'CUSCO', 'ANTA', 'LIMATAMBO'),
(696735, 'CUSCO', 'ANTA', 'MOLLEPATA'),
(697736, 'CUSCO', 'ANTA', 'PUCYURA'),
(698737, 'CUSCO', 'ANTA', 'ZURITE'),
(699738, 'CUSCO', 'ANTA', 'CACHIMAYO'),
(700739, 'CUSCO', 'ANTA', 'ANCAHUASI'),
(701741, 'CUSCO', 'CALCA', 'CALCA'),
(702742, 'CUSCO', 'CALCA', 'COYA'),
(703743, 'CUSCO', 'CALCA', 'LAMAY'),
(704744, 'CUSCO', 'CALCA', 'LARES'),
(705745, 'CUSCO', 'CALCA', 'PISAC'),
(706746, 'CUSCO', 'CALCA', 'SAN SALVADOR'),
(707747, 'CUSCO', 'CALCA', 'TARAY'),
(708748, 'CUSCO', 'CALCA', 'YANATILE'),
(709751, 'CUSCO', 'CANAS', 'YANAOCA'),
(710752, 'CUSCO', 'CANAS', 'CHECCA'),
(711510, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'SANTA ROSA'),
(711753, 'CUSCO', 'CANAS', 'KUNTURKANKI'),
(712754, 'CUSCO', 'CANAS', 'LANGUI'),
(713755, 'CUSCO', 'CANAS', 'LAYO'),
(714756, 'CUSCO', 'CANAS', 'PAMPAMARCA'),
(715757, 'CUSCO', 'CANAS', 'QUEHUE'),
(716758, 'CUSCO', 'CANAS', 'TUPAC AMARU'),
(717761, 'CUSCO', 'CANCHIS', 'SICUANI'),
(718762, 'CUSCO', 'CANCHIS', 'COMBAPATA'),
(719763, 'CUSCO', 'CANCHIS', 'CHECACUPE'),
(720764, 'CUSCO', 'CANCHIS', 'MARANGANI'),
(721511, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'TOTORA'),
(721765, 'CUSCO', 'CANCHIS', 'PITUMARCA'),
(722766, 'CUSCO', 'CANCHIS', 'SAN PABLO'),
(723767, 'CUSCO', 'CANCHIS', 'SAN PEDRO'),
(724768, 'CUSCO', 'CANCHIS', 'TINTA'),
(725771, 'CUSCO', 'CHUMBIVILCAS', 'SANTO TOMAS'),
(726772, 'CUSCO', 'CHUMBIVILCAS', 'CAPACMARCA'),
(727773, 'CUSCO', 'CHUMBIVILCAS', 'COLQUEMARCA'),
(728774, 'CUSCO', 'CHUMBIVILCAS', 'CHAMACA'),
(729775, 'CUSCO', 'CHUMBIVILCAS', 'LIVITACA'),
(730776, 'CUSCO', 'CHUMBIVILCAS', 'LLUSCO'),
(731512, 'AMAZONAS', 'RODRIGUEZ DE MENDOZA', 'VISTA ALEGRE'),
(731777, 'CUSCO', 'CHUMBIVILCAS', 'QUIÑOTA'),
(732778, 'CUSCO', 'CHUMBIVILCAS', 'VELILLE'),
(733781, 'CUSCO', 'ESPINAR', 'ESPINAR'),
(734782, 'CUSCO', 'ESPINAR', 'CONDOROMA'),
(735783, 'CUSCO', 'ESPINAR', 'COPORAQUE'),
(736784, 'CUSCO', 'ESPINAR', 'OCORURO'),
(737785, 'CUSCO', 'ESPINAR', 'PALLPATA'),
(738786, 'CUSCO', 'ESPINAR', 'PICHIGUA'),
(739787, 'CUSCO', 'ESPINAR', 'SUYKUTAMBO'),
(740788, 'CUSCO', 'ESPINAR', 'ALTO PICHIGUA'),
(741791, 'CUSCO', 'LA CONVENCION', 'SANTA ANA'),
(742792, 'CUSCO', 'LA CONVENCION', 'ECHARATE'),
(743793, 'CUSCO', 'LA CONVENCION', 'HUAYOPATA'),
(744794, 'CUSCO', 'LA CONVENCION', 'MARANURA'),
(745795, 'CUSCO', 'LA CONVENCION', 'OCOBAMBA'),
(746796, 'CUSCO', 'LA CONVENCION', 'SANTA TERESA'),
(747797, 'CUSCO', 'LA CONVENCION', 'VILCABAMBA'),
(748798, 'CUSCO', 'LA CONVENCION', 'QUELLOUNO'),
(749799, 'CUSCO', 'LA CONVENCION', 'KIMBIRI'),
(785811, 'HUANCAVELICA', 'HUANCAVELICA', 'HUANCAVELICA'),
(786812, 'HUANCAVELICA', 'HUANCAVELICA', 'ACOBAMBILLA'),
(787813, 'HUANCAVELICA', 'HUANCAVELICA', 'ACORIA'),
(788814, 'HUANCAVELICA', 'HUANCAVELICA', 'CONAYCA'),
(789815, 'HUANCAVELICA', 'HUANCAVELICA', 'CUENCA'),
(790816, 'HUANCAVELICA', 'HUANCAVELICA', 'HUACHOCOLPA'),
(791818, 'HUANCAVELICA', 'HUANCAVELICA', 'HUAYLLAHUARA'),
(792819, 'HUANCAVELICA', 'HUANCAVELICA', 'IZCUCHACA'),
(804821, 'HUANCAVELICA', 'ACOBAMBA', 'ACOBAMBA'),
(805822, 'HUANCAVELICA', 'ACOBAMBA', 'ANTA'),
(806823, 'HUANCAVELICA', 'ACOBAMBA', 'ANDABAMBA'),
(807824, 'HUANCAVELICA', 'ACOBAMBA', 'CAJA'),
(808825, 'HUANCAVELICA', 'ACOBAMBA', 'MARCAS'),
(809826, 'HUANCAVELICA', 'ACOBAMBA', 'PAUCARA'),
(810827, 'HUANCAVELICA', 'ACOBAMBA', 'POMACOCHA'),
(811828, 'HUANCAVELICA', 'ACOBAMBA', 'ROSARIO'),
(812831, 'HUANCAVELICA', 'ANGARAES', 'LIRCAY'),
(813832, 'HUANCAVELICA', 'ANGARAES', 'ANCHONGA'),
(814833, 'HUANCAVELICA', 'ANGARAES', 'CALLANMARCA'),
(815834, 'HUANCAVELICA', 'ANGARAES', 'CONGALLA'),
(816835, 'HUANCAVELICA', 'ANGARAES', 'CHINCHO'),
(817836, 'HUANCAVELICA', 'ANGARAES', 'HUAYLLAY GRANDE'),
(818837, 'HUANCAVELICA', 'ANGARAES', 'HUANCA HUANCA'),
(819838, 'HUANCAVELICA', 'ANGARAES', 'JULCAMARCA'),
(820839, 'HUANCAVELICA', 'ANGARAES', 'SAN ANTONIO DE ANTAPARCO'),
(824841, 'HUANCAVELICA', 'CASTROVIRREYNA', 'CASTROVIRREYNA'),
(825842, 'HUANCAVELICA', 'CASTROVIRREYNA', 'ARMA'),
(826843, 'HUANCAVELICA', 'CASTROVIRREYNA', 'AURAHUA'),
(827845, 'HUANCAVELICA', 'CASTROVIRREYNA', 'CAPILLAS'),
(828846, 'HUANCAVELICA', 'CASTROVIRREYNA', 'COCAS'),
(829848, 'HUANCAVELICA', 'CASTROVIRREYNA', 'CHUPAMARCA'),
(830849, 'HUANCAVELICA', 'CASTROVIRREYNA', 'HUACHOS'),
(837851, 'HUANCAVELICA', 'TAYACAJA', 'PAMPAS'),
(838852, 'HUANCAVELICA', 'TAYACAJA', 'ACOSTAMBO'),
(839853, 'HUANCAVELICA', 'TAYACAJA', 'ACRAQUIA'),
(840854, 'HUANCAVELICA', 'TAYACAJA', 'AHUAYCHA'),
(841856, 'HUANCAVELICA', 'TAYACAJA', 'COLCABAMBA'),
(842859, 'HUANCAVELICA', 'TAYACAJA', 'DANIEL HERNANDEZ'),
(853861, 'HUANCAVELICA', 'HUAYTARA', 'AYAVI'),
(854862, 'HUANCAVELICA', 'HUAYTARA', 'CORDOVA'),
(855863, 'HUANCAVELICA', 'HUAYTARA', 'HUAYACUNDO ARMA'),
(856864, 'HUANCAVELICA', 'HUAYTARA', 'HUAYTARA'),
(857865, 'HUANCAVELICA', 'HUAYTARA', 'LARAMARCA'),
(858866, 'HUANCAVELICA', 'HUAYTARA', 'OCOYO'),
(859867, 'HUANCAVELICA', 'HUAYTARA', 'PILPICHACA'),
(860868, 'HUANCAVELICA', 'HUAYTARA', 'QUERCO'),
(861869, 'HUANCAVELICA', 'HUAYTARA', 'QUITO ARMA'),
(869871, 'HUANCAVELICA', 'CHURCAMPA', 'CHURCAMPA'),
(870872, 'HUANCAVELICA', 'CHURCAMPA', 'ANCO'),
(871873, 'HUANCAVELICA', 'CHURCAMPA', 'CHINCHIHUASI'),
(872874, 'HUANCAVELICA', 'CHURCAMPA', 'EL CARMEN'),
(873875, 'HUANCAVELICA', 'CHURCAMPA', 'LA MERCED'),
(874876, 'HUANCAVELICA', 'CHURCAMPA', 'LOCROJA'),
(875877, 'HUANCAVELICA', 'CHURCAMPA', 'PAUCARBAMBA'),
(876878, 'HUANCAVELICA', 'CHURCAMPA', 'SAN MIGUEL DE MAYOC'),
(877879, 'HUANCAVELICA', 'CHURCAMPA', 'SAN PEDRO DE CORIS'),
(879911, 'HUANUCO', 'HUANUCO', 'HUANUCO'),
(880912, 'HUANUCO', 'HUANUCO', 'CHINCHAO'),
(881913, 'HUANUCO', 'HUANUCO', 'CHURUBAMBA'),
(882914, 'HUANUCO', 'HUANUCO', 'MARGOS'),
(883915, 'HUANUCO', 'HUANUCO', 'QUISQUI'),
(884916, 'HUANUCO', 'HUANUCO', 'SAN FCO DE CAYRAN'),
(885917, 'HUANUCO', 'HUANUCO', 'SAN PEDRO DE CHAULAN'),
(886918, 'HUANUCO', 'HUANUCO', 'STA MARIA DEL VALLE'),
(887919, 'HUANUCO', 'HUANUCO', 'YARUMAYO'),
(890921, 'HUANUCO', 'AMBO', 'AMBO'),
(891922, 'HUANUCO', 'AMBO', 'CAYNA'),
(892923, 'HUANUCO', 'AMBO', 'COLPAS'),
(893924, 'HUANUCO', 'AMBO', 'CONCHAMARCA'),
(894925, 'HUANUCO', 'AMBO', 'HUACAR'),
(895926, 'HUANUCO', 'AMBO', 'SAN FRANCISCO'),
(896927, 'HUANUCO', 'AMBO', 'SAN RAFAEL'),
(897928, 'HUANUCO', 'AMBO', 'TOMAY KICHWA'),
(898931, 'HUANUCO', 'DOS DE MAYO', 'LA UNION'),
(899937, 'HUANUCO', 'DOS DE MAYO', 'CHUQUIS'),
(907941, 'HUANUCO', 'HUAMALIES', 'LLATA'),
(908942, 'HUANUCO', 'HUAMALIES', 'ARANCAY'),
(909943, 'HUANUCO', 'HUAMALIES', 'CHAVIN DE PARIARCA'),
(910944, 'HUANUCO', 'HUAMALIES', 'JACAS GRANDE'),
(911945, 'HUANUCO', 'HUAMALIES', 'JIRCAN'),
(912946, 'HUANUCO', 'HUAMALIES', 'MIRAFLORES'),
(913947, 'HUANUCO', 'HUAMALIES', 'MONZON'),
(914948, 'HUANUCO', 'HUAMALIES', 'PUNCHAO'),
(915949, 'HUANUCO', 'HUAMALIES', 'PUÑOS'),
(918951, 'HUANUCO', 'MARAÑON', 'HUACRACHUCO'),
(919952, 'HUANUCO', 'MARAÑON', 'CHOLON'),
(920955, 'HUANUCO', 'MARAÑON', 'SAN BUENAVENTURA'),
(921961, 'HUANUCO', 'LEONCIO PRADO', 'RUPA RUPA'),
(922962, 'HUANUCO', 'LEONCIO PRADO', 'DANIEL ALOMIA ROBLES'),
(923963, 'HUANUCO', 'LEONCIO PRADO', 'HERMILIO VALDIZAN'),
(924964, 'HUANUCO', 'LEONCIO PRADO', 'LUYANDO'),
(925965, 'HUANUCO', 'LEONCIO PRADO', 'MARIANO DAMASO BERAUN'),
(926966, 'HUANUCO', 'LEONCIO PRADO', 'JOSE CRESPO Y CASTILLO'),
(927971, 'HUANUCO', 'PACHITEA', 'PANAO'),
(928972, 'HUANUCO', 'PACHITEA', 'CHAGLLA'),
(929974, 'HUANUCO', 'PACHITEA', 'MOLINO'),
(930976, 'HUANUCO', 'PACHITEA', 'UMARI'),
(931981, 'HUANUCO', 'PUERTO INCA', 'HONORIA'),
(932110, 'ANCASH', 'HUARAZ', 'PARIACOTO'),
(932982, 'HUANUCO', 'PUERTO INCA', 'PUERTO INCA'),
(933983, 'HUANUCO', 'PUERTO INCA', 'CODO DEL POZUZO'),
(934984, 'HUANUCO', 'PUERTO INCA', 'TOURNAVISTA'),
(935985, 'HUANUCO', 'PUERTO INCA', 'YUYAPICHIS'),
(936991, 'HUANUCO', 'HUACAYBAMBA', 'HUACAYBAMBA'),
(937992, 'HUANUCO', 'HUACAYBAMBA', 'PINRA'),
(938993, 'HUANUCO', 'HUACAYBAMBA', 'CANCHABAMBA'),
(939994, 'HUANUCO', 'HUACAYBAMBA', 'COCHABAMBA'),
(942111, 'ANCASH', 'HUARAZ', 'PIRA'),
(952112, 'ANCASH', 'HUARAZ', 'TARICA'),
(1052310, 'ANCASH', 'BOLOGNESI', 'HUAYLLACAYAN'),
(1062311, 'ANCASH', 'BOLOGNESI', 'HUASTA'),
(1072313, 'ANCASH', 'BOLOGNESI', 'MANGAS'),
(1082315, 'ANCASH', 'BOLOGNESI', 'PACLLON'),
(1092317, 'ANCASH', 'BOLOGNESI', 'SAN MIGUEL DE CORPANQUI'),
(1102320, 'ANCASH', 'BOLOGNESI', 'TICLLOS'),
(1112321, 'ANCASH', 'BOLOGNESI', 'ANTONIO RAIMONDI'),
(1122322, 'ANCASH', 'BOLOGNESI', 'CANIS'),
(1132323, 'ANCASH', 'BOLOGNESI', 'COLQUIOC'),
(1142324, 'ANCASH', 'BOLOGNESI', 'LA PRIMAVERA'),
(1152325, 'ANCASH', 'BOLOGNESI', 'HUALLANCA'),
(1252410, 'ANCASH', 'CARHUAZ', 'TINCO'),
(1262411, 'ANCASH', 'CARHUAZ', 'YUNGAR'),
(1472710, 'ANCASH', 'HUAYLAS', 'SANTO TORIBIO'),
(1572810, 'ANCASH', 'HUARI', 'PONTO'),
(1582811, 'ANCASH', 'HUARI', 'RAHUAPAMPA'),
(1592812, 'ANCASH', 'HUARI', 'RAPAYAN'),
(1602813, 'ANCASH', 'HUARI', 'SAN MARCOS'),
(1612814, 'ANCASH', 'HUARI', 'SAN PEDRO DE CHANA'),
(1622815, 'ANCASH', 'HUARI', 'UCO'),
(1632816, 'ANCASH', 'HUARI', 'ANRA'),
(1722101, 'ANCASH', 'PALLASCA', 'CABANA'),
(1732102, 'ANCASH', 'PALLASCA', 'BOLOGNESI'),
(1742103, 'ANCASH', 'PALLASCA', 'CONCHUCOS'),
(1752104, 'ANCASH', 'PALLASCA', 'HUACASCHUQUE'),
(1762105, 'ANCASH', 'PALLASCA', 'HUANDOVAL'),
(1772106, 'ANCASH', 'PALLASCA', 'LACABAMBA'),
(1782107, 'ANCASH', 'PALLASCA', 'LLAPO'),
(1792108, 'ANCASH', 'PALLASCA', 'PALLASCA'),
(1802109, 'ANCASH', 'PALLASCA', 'PAMPAS'),
(1832111, 'ANCASH', 'POMABAMBA', 'POMABAMBA'),
(1834125, 'AMAZONAS', 'BAGUA', 'BAGUA'),
(1842112, 'ANCASH', 'POMABAMBA', 'HUAYLLAN'),
(1852113, 'ANCASH', 'POMABAMBA', 'PAROBAMBA'),
(1862114, 'ANCASH', 'POMABAMBA', 'QUINUABAMBA'),
(1872121, 'ANCASH', 'RECUAY', 'RECUAY'),
(1882122, 'ANCASH', 'RECUAY', 'COTAPARACO'),
(1892123, 'ANCASH', 'RECUAY', 'HUAYLLAPAMPA'),
(1902124, 'ANCASH', 'RECUAY', 'MARCA'),
(1912125, 'ANCASH', 'RECUAY', 'PAMPAS CHICO'),
(1922126, 'ANCASH', 'RECUAY', 'PARARIN'),
(1932127, 'ANCASH', 'RECUAY', 'TAPACOCHA'),
(1942128, 'ANCASH', 'RECUAY', 'TICAPAMPA'),
(1952129, 'ANCASH', 'RECUAY', 'LLACLLIN'),
(1972131, 'ANCASH', 'SANTA', 'CHIMBOTE'),
(1982132, 'ANCASH', 'SANTA', 'CACERES DEL PERU'),
(1992133, 'ANCASH', 'SANTA', 'MACATE'),
(2002134, 'ANCASH', 'SANTA', 'MORO'),
(2012135, 'ANCASH', 'SANTA', 'NEPEÑA'),
(2022136, 'ANCASH', 'SANTA', 'SAMANCO'),
(2032137, 'ANCASH', 'SANTA', 'SANTA'),
(2042138, 'ANCASH', 'SANTA', 'COISHCO'),
(2052139, 'ANCASH', 'SANTA', 'NUEVO CHIMBOTE'),
(2062141, 'ANCASH', 'SIHUAS', 'SIHUAS'),
(2072142, 'ANCASH', 'SIHUAS', 'ALFONSO UGARTE'),
(2082143, 'ANCASH', 'SIHUAS', 'CHINGALPO'),
(2092144, 'ANCASH', 'SIHUAS', 'HUAYLLABAMBA'),
(2102145, 'ANCASH', 'SIHUAS', 'QUICHES'),
(2112146, 'ANCASH', 'SIHUAS', 'SICSIBAMBA'),
(2122147, 'ANCASH', 'SIHUAS', 'ACOBAMBA'),
(2132148, 'ANCASH', 'SIHUAS', 'CASHAPAMPA'),
(2142149, 'ANCASH', 'SIHUAS', 'RAGASH'),
(2162151, 'ANCASH', 'YUNGAY', 'YUNGAY'),
(2172152, 'ANCASH', 'YUNGAY', 'CASCAPARA'),
(2182153, 'ANCASH', 'YUNGAY', 'MANCOS'),
(2192154, 'ANCASH', 'YUNGAY', 'MATACOTO'),
(2202155, 'ANCASH', 'YUNGAY', 'QUILLO'),
(2212156, 'ANCASH', 'YUNGAY', 'RANRAHIRCA'),
(2222157, 'ANCASH', 'YUNGAY', 'SHUPLUY'),
(2232158, 'ANCASH', 'YUNGAY', 'YANAMA'),
(2242161, 'ANCASH', 'ANTONIO RAIMONDI', 'LLAMELLIN'),
(2252162, 'ANCASH', 'ANTONIO RAIMONDI', 'ACZO'),
(2262163, 'ANCASH', 'ANTONIO RAIMONDI', 'CHACCHO'),
(2272164, 'ANCASH', 'ANTONIO RAIMONDI', 'CHINGAS'),
(2282165, 'ANCASH', 'ANTONIO RAIMONDI', 'MIRGAS'),
(2292166, 'ANCASH', 'ANTONIO RAIMONDI', 'SAN JUAN DE RONTOY'),
(2302171, 'ANCASH', 'CARLOS FERMIN FITZCARRALD', 'SAN LUIS'),
(2312172, 'ANCASH', 'CARLOS FERMIN FITZCARRALD', 'YAUYA'),
(2322173, 'ANCASH', 'CARLOS FERMIN FITZCARRALD', 'SAN NICOLAS'),
(2332181, 'ANCASH', 'ASUNCION', 'CHACAS'),
(2342182, 'ANCASH', 'ASUNCION', 'ACOCHACA'),
(2352191, 'ANCASH', 'HUARMEY', 'HUARMEY'),
(2362192, 'ANCASH', 'HUARMEY', 'COCHAPETI'),
(2372193, 'ANCASH', 'HUARMEY', 'HUAYAN'),
(2382194, 'ANCASH', 'HUARMEY', 'MALVAS'),
(2392195, 'ANCASH', 'HUARMEY', 'CULEBRAS'),
(2402201, 'ANCASH', 'OCROS', 'ACAS'),
(2412202, 'ANCASH', 'OCROS', 'CAJAMARQUILLA'),
(2422203, 'ANCASH', 'OCROS', 'CARHUAPAMPA'),
(2432204, 'ANCASH', 'OCROS', 'COCHAS'),
(2442205, 'ANCASH', 'OCROS', 'CONGAS'),
(2452206, 'ANCASH', 'OCROS', 'LLIPA'),
(2462207, 'ANCASH', 'OCROS', 'OCROS'),
(2472208, 'ANCASH', 'OCROS', 'SAN CRISTOBAL DE RAJAN'),
(2482209, 'ANCASH', 'OCROS', 'SAN PEDRO'),
(2683210, 'APURIMAC', 'AYMARAES', 'SAÑAICA'),
(2693211, 'APURIMAC', 'AYMARAES', 'SORAYA'),
(2703212, 'APURIMAC', 'AYMARAES', 'TAPAIRIHUA'),
(2713213, 'APURIMAC', 'AYMARAES', 'TINTAY'),
(2723214, 'APURIMAC', 'AYMARAES', 'TORAYA'),
(2733215, 'APURIMAC', 'AYMARAES', 'YANACA'),
(2743216, 'APURIMAC', 'AYMARAES', 'SAN JUAN DE CHACÑA'),
(2753217, 'APURIMAC', 'AYMARAES', 'JUSTO APU SAHUARAURA'),
(2853310, 'APURIMAC', 'ANDAHUAYLAS', 'SAN JERONIMO'),
(2863311, 'APURIMAC', 'ANDAHUAYLAS', 'TALAVERA'),
(2873312, 'APURIMAC', 'ANDAHUAYLAS', 'TURPO'),
(2883313, 'APURIMAC', 'ANDAHUAYLAS', 'PACUCHA'),
(2893314, 'APURIMAC', 'ANDAHUAYLAS', 'POMACOCHA'),
(2903315, 'APURIMAC', 'ANDAHUAYLAS', 'STA MARIA DE CHICMO'),
(2913316, 'APURIMAC', 'ANDAHUAYLAS', 'TUMAY HUARACA'),
(2923317, 'APURIMAC', 'ANDAHUAYLAS', 'HUAYANA'),
(2933318, 'APURIMAC', 'ANDAHUAYLAS', 'SAN MIGUEL DE CHACCRAMPA'),
(2943319, 'APURIMAC', 'ANDAHUAYLAS', 'KAQUIABAMBA'),
(3173610, 'APURIMAC', 'GRAU', 'TURPAY'),
(3183611, 'APURIMAC', 'GRAU', 'VILCABAMBA'),
(3193612, 'APURIMAC', 'GRAU', 'VIRUNDO'),
(3203613, 'APURIMAC', 'GRAU', 'SANTA ROSA'),
(3213614, 'APURIMAC', 'GRAU', 'CURASCO'),
(3394110, 'AREQUIPA', 'AREQUIPA', 'POCSI'),
(3404111, 'AREQUIPA', 'AREQUIPA', 'POLOBAYA'),
(3414112, 'AREQUIPA', 'AREQUIPA', 'QUEQUEÑA'),
(3424113, 'AREQUIPA', 'AREQUIPA', 'SABANDIA'),
(3434114, 'AREQUIPA', 'AREQUIPA', 'SACHACA'),
(3444115, 'AREQUIPA', 'AREQUIPA', 'SAN JUAN DE SIGUAS'),
(3454116, 'AREQUIPA', 'AREQUIPA', 'SAN JUAN DE TARUCANI'),
(3464117, 'AREQUIPA', 'AREQUIPA', 'SANTA ISABEL DE SIGUAS'),
(3474118, 'AREQUIPA', 'AREQUIPA', 'STA RITA DE SIGUAS'),
(3484119, 'AREQUIPA', 'AREQUIPA', 'SOCABAYA'),
(3494120, 'AREQUIPA', 'AREQUIPA', 'TIABAYA'),
(3504121, 'AREQUIPA', 'AREQUIPA', 'UCHUMAYO'),
(3514122, 'AREQUIPA', 'AREQUIPA', 'VITOR'),
(3524123, 'AREQUIPA', 'AREQUIPA', 'YANAHUARA'),
(3534124, 'AREQUIPA', 'AREQUIPA', 'YARABAMBA'),
(3544125, 'AREQUIPA', 'AREQUIPA', 'YURA'),
(3554126, 'AREQUIPA', 'AREQUIPA', 'MARIANO MELGAR'),
(3564127, 'AREQUIPA', 'AREQUIPA', 'JACOBO HUNTER'),
(3574128, 'AREQUIPA', 'AREQUIPA', 'ALTO SELVA ALEGRE'),
(3584129, 'AREQUIPA', 'AREQUIPA', 'JOSE LUIS BUSTAMANTE Y RIVERO'),
(3684210, 'AREQUIPA', 'CAYLLOMA', 'LARI'),
(3694211, 'AREQUIPA', 'CAYLLOMA', 'LLUTA'),
(3704212, 'AREQUIPA', 'CAYLLOMA', 'MACA'),
(3714213, 'AREQUIPA', 'CAYLLOMA', 'MADRIGAL'),
(3724214, 'AREQUIPA', 'CAYLLOMA', 'SAN ANTONIO DE CHUCA'),
(3734215, 'AREQUIPA', 'CAYLLOMA', 'SIBAYO'),
(3744216, 'AREQUIPA', 'CAYLLOMA', 'TAPAY'),
(3754217, 'AREQUIPA', 'CAYLLOMA', 'TISCO'),
(3764218, 'AREQUIPA', 'CAYLLOMA', 'TUTI'),
(3774219, 'AREQUIPA', 'CAYLLOMA', 'YANQUE'),
(3784220, 'AREQUIPA', 'CAYLLOMA', 'MAJES'),
(3964410, 'AREQUIPA', 'CARAVELI', 'JAQUI'),
(3974411, 'AREQUIPA', 'CARAVELI', 'LOMAS'),
(3984412, 'AREQUIPA', 'CARAVELI', 'QUICACHA'),
(3994413, 'AREQUIPA', 'CARAVELI', 'YAUCA'),
(4094510, 'AREQUIPA', 'CASTILLA', 'PAMPACOLCA'),
(4104511, 'AREQUIPA', 'CASTILLA', 'TIPAN'),
(4114512, 'AREQUIPA', 'CASTILLA', 'URACA'),
(4124513, 'AREQUIPA', 'CASTILLA', 'UÑON'),
(4134514, 'AREQUIPA', 'CASTILLA', 'VIRACO'),
(4374810, 'AREQUIPA', 'LA UNION', 'TOMEPAMPA'),
(4384811, 'AREQUIPA', 'LA UNION', 'TORO'),
(4485110, 'AYACUCHO', 'HUAMANGA', 'TAMBILLO'),
(4495111, 'AYACUCHO', 'HUAMANGA', 'ACOCRO'),
(4505112, 'AYACUCHO', 'HUAMANGA', 'SOCOS'),
(4515113, 'AYACUCHO', 'HUAMANGA', 'OCROS'),
(4525114, 'AYACUCHO', 'HUAMANGA', 'PACAYCASA'),
(4535115, 'AYACUCHO', 'HUAMANGA', 'JESUS NAZARENO'),
(4595211, 'AYACUCHO', 'CANGALLO', 'MARIA PARADO DE BELLIDO'),
(4825510, 'AYACUCHO', 'LUCANAS', 'HUAC-HUAS'),
(4835511, 'AYACUCHO', 'LUCANAS', 'LARAMATE'),
(4845512, 'AYACUCHO', 'LUCANAS', 'LEONCIO PRADO'),
(4855513, 'AYACUCHO', 'LUCANAS', 'LUCANAS'),
(4865514, 'AYACUCHO', 'LUCANAS', 'LLAUTA'),
(4875516, 'AYACUCHO', 'LUCANAS', 'OCAÑA'),
(4885517, 'AYACUCHO', 'LUCANAS', 'OTOCA'),
(4895520, 'AYACUCHO', 'LUCANAS', 'SANCOS'),
(4905521, 'AYACUCHO', 'LUCANAS', 'SAN JUAN'),
(4915522, 'AYACUCHO', 'LUCANAS', 'SAN PEDRO'),
(4925524, 'AYACUCHO', 'LUCANAS', 'STA ANA DE HUAYCAHUACHO'),
(4935525, 'AYACUCHO', 'LUCANAS', 'SANTA LUCIA'),
(4945529, 'AYACUCHO', 'LUCANAS', 'SAISA'),
(4955531, 'AYACUCHO', 'LUCANAS', 'SAN PEDRO DE PALCO'),
(4965532, 'AYACUCHO', 'LUCANAS', 'SAN CRISTOBAL'),
(5015611, 'AYACUCHO', 'PARINACOCHAS', 'PULLO'),
(5025612, 'AYACUCHO', 'PARINACOCHAS', 'PUYUSCA'),
(5035615, 'AYACUCHO', 'PARINACOCHAS', 'SAN FCO DE RAVACAYCO'),
(5045616, 'AYACUCHO', 'PARINACOCHAS', 'UPAHUACHO'),
(5135710, 'AYACUCHO', 'VICTOR FAJARDO', 'HUANCARAYLLA'),
(5145713, 'AYACUCHO', 'VICTOR FAJARDO', 'SARHUA'),
(5155714, 'AYACUCHO', 'VICTOR FAJARDO', 'VILCANCHOS'),
(5165715, 'AYACUCHO', 'VICTOR FAJARDO', 'ASQUIPATA'),
(5295101, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'PAUSA'),
(5305102, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'COLTA'),
(5315103, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'CORCULLA'),
(5325104, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'LAMPA'),
(5335105, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'MARCABAMBA'),
(5345106, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'OYOLO'),
(5355107, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'PARARCA'),
(5365108, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'SAN JAVIER DE ALPABAMBA'),
(5375109, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'SAN JOSE DE USHUA'),
(5395111, 'AYACUCHO', 'SUCRE', 'QUEROBAMBA'),
(5405112, 'AYACUCHO', 'SUCRE', 'BELEN'),
(5415113, 'AYACUCHO', 'SUCRE', 'CHALCOS'),
(5425114, 'AYACUCHO', 'SUCRE', 'SAN SALVADOR DE QUIJE'),
(5435115, 'AYACUCHO', 'SUCRE', 'PAICO'),
(5445116, 'AYACUCHO', 'SUCRE', 'SANTIAGO DE PAUCARAY'),
(5455117, 'AYACUCHO', 'SUCRE', 'SAN PEDRO DE LARCAY'),
(5465118, 'AYACUCHO', 'SUCRE', 'SORAS'),
(5475119, 'AYACUCHO', 'SUCRE', 'HUACAÑA'),
(5596110, 'CAJAMARCA', 'CAJAMARCA', 'MATARA'),
(5606111, 'CAJAMARCA', 'CAJAMARCA', 'NAMORA'),
(5616112, 'CAJAMARCA', 'CAJAMARCA', 'SAN JUAN'),
(5756310, 'CAJAMARCA', 'CELENDIN', 'SUCRE'),
(5766311, 'CAJAMARCA', 'CELENDIN', 'UTCO'),
(5776312, 'CAJAMARCA', 'CELENDIN', 'LA LIBERTAD DE PALLAN'),
(5956510, 'CAJAMARCA', 'CUTERVO', 'SAN LUIS DE LUCMA'),
(5966511, 'CAJAMARCA', 'CUTERVO', 'SANTA CRUZ'),
(5976512, 'CAJAMARCA', 'CUTERVO', 'SANTO DOMINGO DE LA CAPILLA'),
(5986513, 'CAJAMARCA', 'CUTERVO', 'SANTO TOMAS'),
(5996514, 'CAJAMARCA', 'CUTERVO', 'SOCOTA'),
(6006515, 'CAJAMARCA', 'CUTERVO', 'TORIBIO CASANOVA'),
(6106610, 'CAJAMARCA', 'CHOTA', 'LLAMA'),
(6116611, 'CAJAMARCA', 'CHOTA', 'MIRACOSTA'),
(6126612, 'CAJAMARCA', 'CHOTA', 'PACCHA'),
(6136613, 'CAJAMARCA', 'CHOTA', 'PION'),
(6146614, 'CAJAMARCA', 'CHOTA', 'QUEROCOTO'),
(6156615, 'CAJAMARCA', 'CHOTA', 'TACABAMBA'),
(6166616, 'CAJAMARCA', 'CHOTA', 'TOCMOCHE'),
(6176617, 'CAJAMARCA', 'CHOTA', 'SAN JUAN DE LICUPIS'),
(6186618, 'CAJAMARCA', 'CHOTA', 'CHOROPAMPA'),
(6196619, 'CAJAMARCA', 'CHOTA', 'CHALAMARCA'),
(6326810, 'CAJAMARCA', 'JAEN', 'SANTA ROSA'),
(6336811, 'CAJAMARCA', 'JAEN', 'LAS PIRIAS'),
(6346812, 'CAJAMARCA', 'JAEN', 'HUABAL'),
(6446910, 'CAJAMARCA', 'SANTA CRUZ', 'ANDABAMBA'),
(6456911, 'CAJAMARCA', 'SANTA CRUZ', 'SAUCEPAMPA'),
(6466101, 'CAJAMARCA', 'SAN MIGUEL', 'SAN MIGUEL'),
(6476102, 'CAJAMARCA', 'SAN MIGUEL', 'CALQUIS'),
(6486103, 'CAJAMARCA', 'SAN MIGUEL', 'LA FLORIDA'),
(6496104, 'CAJAMARCA', 'SAN MIGUEL', 'LLAPA'),
(6506105, 'CAJAMARCA', 'SAN MIGUEL', 'NANCHOC'),
(6516106, 'CAJAMARCA', 'SAN MIGUEL', 'NIEPOS'),
(6526107, 'CAJAMARCA', 'SAN MIGUEL', 'SAN GREGORIO'),
(6536108, 'CAJAMARCA', 'SAN MIGUEL', 'SAN SILVESTRE DE COCHAN'),
(6546109, 'CAJAMARCA', 'SAN MIGUEL', 'EL PRADO'),
(6596111, 'CAJAMARCA', 'SAN IGNACIO', 'SAN IGNACIO'),
(6606112, 'CAJAMARCA', 'SAN IGNACIO', 'CHIRINOS'),
(6616113, 'CAJAMARCA', 'SAN IGNACIO', 'HUARANGO'),
(6626114, 'CAJAMARCA', 'SAN IGNACIO', 'NAMBALLE'),
(6636115, 'CAJAMARCA', 'SAN IGNACIO', 'LA COIPA'),
(6646116, 'CAJAMARCA', 'SAN IGNACIO', 'SAN JOSE DE LOURDES'),
(6656117, 'CAJAMARCA', 'SAN IGNACIO', 'TABACONAS'),
(6666121, 'CAJAMARCA', 'SAN MARCOS', 'PEDRO GALVEZ'),
(6676122, 'CAJAMARCA', 'SAN MARCOS', 'ICHOCAN'),
(6686123, 'CAJAMARCA', 'SAN MARCOS', 'GREGORIO PITA'),
(6696124, 'CAJAMARCA', 'SAN MARCOS', 'JOSE MANUEL QUIROZ'),
(6706125, 'CAJAMARCA', 'SAN MARCOS', 'EDUARDO VILLANUEVA'),
(6716126, 'CAJAMARCA', 'SAN MARCOS', 'JOSE SABOGAL'),
(6726127, 'CAJAMARCA', 'SAN MARCOS', 'CHANCAY'),
(6736131, 'CAJAMARCA', 'SAN PABLO', 'SAN PABLO'),
(6746132, 'CAJAMARCA', 'SAN PABLO', 'SAN BERNARDINO'),
(6756133, 'CAJAMARCA', 'SAN PABLO', 'SAN LUIS'),
(6766134, 'CAJAMARCA', 'SAN PABLO', 'TUMBADEN'),
(7507910, 'CUSCO', 'LA CONVENCION', 'PICHARI'),
(7517101, 'CUSCO', 'PARURO', 'PARURO'),
(7527102, 'CUSCO', 'PARURO', 'ACCHA'),
(7537103, 'CUSCO', 'PARURO', 'CCAPI'),
(7547104, 'CUSCO', 'PARURO', 'COLCHA'),
(7557105, 'CUSCO', 'PARURO', 'HUANOQUITE'),
(7567106, 'CUSCO', 'PARURO', 'OMACHA'),
(7577107, 'CUSCO', 'PARURO', 'YAURISQUE'),
(7587108, 'CUSCO', 'PARURO', 'PACCARITAMBO'),
(7597109, 'CUSCO', 'PARURO', 'PILLPINTO'),
(7607111, 'CUSCO', 'PAUCARTAMBO', 'PAUCARTAMBO'),
(7617112, 'CUSCO', 'PAUCARTAMBO', 'CAICAY'),
(7627113, 'CUSCO', 'PAUCARTAMBO', 'COLQUEPATA'),
(7637114, 'CUSCO', 'PAUCARTAMBO', 'CHALLABAMBA'),
(7647115, 'CUSCO', 'PAUCARTAMBO', 'COSÑIPATA'),
(7657116, 'CUSCO', 'PAUCARTAMBO', 'HUANCARANI'),
(7667121, 'CUSCO', 'QUISPICANCHI', 'URCOS'),
(7677122, 'CUSCO', 'QUISPICANCHI', 'ANDAHUAYLILLAS'),
(7687123, 'CUSCO', 'QUISPICANCHI', 'CAMANTI'),
(7697124, 'CUSCO', 'QUISPICANCHI', 'CCARHUAYO'),
(7707125, 'CUSCO', 'QUISPICANCHI', 'CCATCA'),
(7717126, 'CUSCO', 'QUISPICANCHI', 'CUSIPATA'),
(7727127, 'CUSCO', 'QUISPICANCHI', 'HUARO'),
(7737128, 'CUSCO', 'QUISPICANCHI', 'LUCRE'),
(7747129, 'CUSCO', 'QUISPICANCHI', 'MARCAPATA'),
(7787131, 'CUSCO', 'URUBAMBA', 'URUBAMBA'),
(7797132, 'CUSCO', 'URUBAMBA', 'CHINCHERO'),
(7807133, 'CUSCO', 'URUBAMBA', 'HUAYLLABAMBA'),
(7817134, 'CUSCO', 'URUBAMBA', 'MACHUPICCHU'),
(7827135, 'CUSCO', 'URUBAMBA', 'MARAS'),
(7837136, 'CUSCO', 'URUBAMBA', 'OLLANTAYTAMBO'),
(7847137, 'CUSCO', 'URUBAMBA', 'YUCAY'),
(7938110, 'HUANCAVELICA', 'HUANCAVELICA', 'LARIA'),
(7948111, 'HUANCAVELICA', 'HUANCAVELICA', 'MANTA'),
(7958112, 'HUANCAVELICA', 'HUANCAVELICA', 'MARISCAL CACERES'),
(7968113, 'HUANCAVELICA', 'HUANCAVELICA', 'MOYA'),
(7978114, 'HUANCAVELICA', 'HUANCAVELICA', 'NUEVO OCCORO'),
(7988115, 'HUANCAVELICA', 'HUANCAVELICA', 'PALCA'),
(7998116, 'HUANCAVELICA', 'HUANCAVELICA', 'PILCHACA'),
(8008117, 'HUANCAVELICA', 'HUANCAVELICA', 'VILCA'),
(8018118, 'HUANCAVELICA', 'HUANCAVELICA', 'YAULI'),
(8028119, 'HUANCAVELICA', 'HUANCAVELICA', 'ASCENSION'),
(8038120, 'HUANCAVELICA', 'HUANCAVELICA', 'HUANDO'),
(8218310, 'HUANCAVELICA', 'ANGARAES', 'STO TOMAS DE PATA'),
(8228311, 'HUANCAVELICA', 'ANGARAES', 'SECCLLA'),
(8238312, 'HUANCAVELICA', 'ANGARAES', 'CCOCHACCASA'),
(8318410, 'HUANCAVELICA', 'CASTROVIRREYNA', 'HUAMATAMBO'),
(8328414, 'HUANCAVELICA', 'CASTROVIRREYNA', 'MOLLEPAMPA'),
(8338422, 'HUANCAVELICA', 'CASTROVIRREYNA', 'SAN JUAN'),
(8348427, 'HUANCAVELICA', 'CASTROVIRREYNA', 'TANTARA'),
(8358428, 'HUANCAVELICA', 'CASTROVIRREYNA', 'TICRAPO'),
(8368429, 'HUANCAVELICA', 'CASTROVIRREYNA', 'SANTA ANA'),
(8438511, 'HUANCAVELICA', 'TAYACAJA', 'HUACHOCOLPA'),
(8448512, 'HUANCAVELICA', 'TAYACAJA', 'HUARIBAMBA'),
(8458515, 'HUANCAVELICA', 'TAYACAJA', 'ÑAHUIMPUQUIO'),
(8468517, 'HUANCAVELICA', 'TAYACAJA', 'PAZOS'),
(8478518, 'HUANCAVELICA', 'TAYACAJA', 'QUISHUAR'),
(8488519, 'HUANCAVELICA', 'TAYACAJA', 'SALCABAMBA'),
(8498520, 'HUANCAVELICA', 'TAYACAJA', 'SAN MARCOS DE ROCCHAC'),
(8508523, 'HUANCAVELICA', 'TAYACAJA', 'SURCUBAMBA'),
(8518525, 'HUANCAVELICA', 'TAYACAJA', 'TINTAY PUNCU'),
(8528526, 'HUANCAVELICA', 'TAYACAJA', 'SALCAHUASI'),
(8628610, 'HUANCAVELICA', 'HUAYTARA', 'SAN ANTONIO DE CUSICANCHA'),
(8638611, 'HUANCAVELICA', 'HUAYTARA', 'SAN FRANCISCO DE SANGAYAICO'),
(8648612, 'HUANCAVELICA', 'HUAYTARA', 'SAN ISIDRO'),
(8658613, 'HUANCAVELICA', 'HUAYTARA', 'SANTIAGO DE CHOCORVOS'),
(8668614, 'HUANCAVELICA', 'HUAYTARA', 'SANTIAGO DE QUIRAHUARA'),
(8678615, 'HUANCAVELICA', 'HUAYTARA', 'SANTO DOMINGO DE CAPILLAS'),
(8688616, 'HUANCAVELICA', 'HUAYTARA', 'TAMBO'),
(8788710, 'HUANCAVELICA', 'CHURCAMPA', 'PACHAMARCA'),
(8889110, 'HUANUCO', 'HUANUCO', 'AMARILIS'),
(8899111, 'HUANUCO', 'HUANUCO', 'PILLCO MARCA'),
(9009312, 'HUANUCO', 'DOS DE MAYO', 'MARIAS'),
(9019314, 'HUANUCO', 'DOS DE MAYO', 'PACHAS'),
(9029316, 'HUANUCO', 'DOS DE MAYO', 'QUIVILLA'),
(9039317, 'HUANUCO', 'DOS DE MAYO', 'RIPAN'),
(9049321, 'HUANUCO', 'DOS DE MAYO', 'SHUNQUI'),
(9059322, 'HUANUCO', 'DOS DE MAYO', 'SILLAPATA'),
(9069323, 'HUANUCO', 'DOS DE MAYO', 'YANAS'),
(9169410, 'HUANUCO', 'HUAMALIES', 'SINGA'),
(9179411, 'HUANUCO', 'HUAMALIES', 'TANTAMAYO'),
(9409101, 'HUANUCO', 'LAURICOCHA', 'JESUS'),
(9419102, 'HUANUCO', 'LAURICOCHA', 'BAÑOS'),
(9429103, 'HUANUCO', 'LAURICOCHA', 'SAN FRANCISCO DE ASIS'),
(9439104, 'HUANUCO', 'LAURICOCHA', 'QUEROPALCA'),
(9449105, 'HUANUCO', 'LAURICOCHA', 'SAN MIGUEL DE CAURI'),
(9459106, 'HUANUCO', 'LAURICOCHA', 'RONDOS'),
(9469107, 'HUANUCO', 'LAURICOCHA', 'JIVIA'),
(9479111, 'HUANUCO', 'YAROWILCA', 'CHAVINILLO'),
(9489112, 'HUANUCO', 'YAROWILCA', 'APARICIO POMARES (CHUPAN);'),
(9499113, 'HUANUCO', 'YAROWILCA', 'CAHUAC'),
(9509114, 'HUANUCO', 'YAROWILCA', 'CHACABAMBA'),
(9519115, 'HUANUCO', 'YAROWILCA', 'JACAS CHICO'),
(9529116, 'HUANUCO', 'YAROWILCA', 'OBAS'),
(9539117, 'HUANUCO', 'YAROWILCA', 'PAMPAMARCA'),
(9549118, 'HUANUCO', 'YAROWILCA', 'CHORAS'),
(9551011, 'ICA', 'ICA', 'ICA'),
(9561012, 'ICA', 'ICA', 'LA TINGUIÑA'),
(9571013, 'ICA', 'ICA', 'LOS AQUIJES'),
(9581014, 'ICA', 'ICA', 'PARCONA'),
(9591015, 'ICA', 'ICA', 'PUEBLO NUEVO'),
(9601016, 'ICA', 'ICA', 'SALAS'),
(9611017, 'ICA', 'ICA', 'SAN JOSE DE LOS MOLINOS'),
(9621018, 'ICA', 'ICA', 'SAN JUAN BAUTISTA'),
(9631019, 'ICA', 'ICA', 'SANTIAGO'),
(9691021, 'ICA', 'CHINCHA', 'CHINCHA ALTA'),
(9701022, 'ICA', 'CHINCHA', 'CHAVIN'),
(9711023, 'ICA', 'CHINCHA', 'CHINCHA BAJA'),
(9721024, 'ICA', 'CHINCHA', 'EL CARMEN'),
(9731025, 'ICA', 'CHINCHA', 'GROCIO PRADO'),
(9741026, 'ICA', 'CHINCHA', 'SAN PEDRO DE HUACARPANA'),
(9751027, 'ICA', 'CHINCHA', 'SUNAMPE'),
(9761028, 'ICA', 'CHINCHA', 'TAMBO DE MORA'),
(9771029, 'ICA', 'CHINCHA', 'ALTO LARAN'),
(9801031, 'ICA', 'NAZCA', 'NAZCA'),
(9811032, 'ICA', 'NAZCA', 'CHANGUILLO'),
(9821033, 'ICA', 'NAZCA', 'EL INGENIO'),
(9831034, 'ICA', 'NAZCA', 'MARCONA'),
(9841035, 'ICA', 'NAZCA', 'VISTA ALEGRE'),
(9851041, 'ICA', 'PISCO', 'PISCO'),
(9861042, 'ICA', 'PISCO', 'HUANCANO'),
(9871043, 'ICA', 'PISCO', 'HUMAY'),
(9881044, 'ICA', 'PISCO', 'INDEPENDENCIA'),
(9891045, 'ICA', 'PISCO', 'PARACAS'),
(9901046, 'ICA', 'PISCO', 'SAN ANDRES'),
(9911047, 'ICA', 'PISCO', 'SAN CLEMENTE'),
(9921048, 'ICA', 'PISCO', 'TUPAC AMARU INCA'),
(9931051, 'ICA', 'PALPA', 'PALPA'),
(9941052, 'ICA', 'PALPA', 'LLIPATA'),
(9951053, 'ICA', 'PALPA', 'RIO GRANDE'),
(9961054, 'ICA', 'PALPA', 'SANTA CRUZ'),
(9971055, 'ICA', 'PALPA', 'TIBILLO'),
(9981111, 'JUNIN', 'HUANCAYO', 'HUANCAYO'),
(9991113, 'JUNIN', 'HUANCAYO', 'CARHUACALLANGA'),
(10001114, 'JUNIN', 'HUANCAYO', 'COLCA'),
(10011115, 'JUNIN', 'HUANCAYO', 'CULLHUAS'),
(10021116, 'JUNIN', 'HUANCAYO', 'CHACAPAMPA'),
(10031117, 'JUNIN', 'HUANCAYO', 'CHICCHE'),
(10041118, 'JUNIN', 'HUANCAYO', 'CHILCA'),
(10051119, 'JUNIN', 'HUANCAYO', 'CHONGOS ALTO'),
(10261121, 'JUNIN', 'CONCEPCION', 'CONCEPCION'),
(10271122, 'JUNIN', 'CONCEPCION', 'ACO'),
(10281123, 'JUNIN', 'CONCEPCION', 'ANDAMARCA'),
(10291124, 'JUNIN', 'CONCEPCION', 'COMAS'),
(10301125, 'JUNIN', 'CONCEPCION', 'COCHAS'),
(10311126, 'JUNIN', 'CONCEPCION', 'CHAMBARA'),
(10321127, 'JUNIN', 'CONCEPCION', 'HEROINAS TOLEDO'),
(10331128, 'JUNIN', 'CONCEPCION', 'MANZANARES'),
(10341129, 'JUNIN', 'CONCEPCION', 'MCAL CASTILLA'),
(10411131, 'JUNIN', 'JAUJA', 'JAUJA'),
(10421132, 'JUNIN', 'JAUJA', 'ACOLLA'),
(10431133, 'JUNIN', 'JAUJA', 'APATA'),
(10441134, 'JUNIN', 'JAUJA', 'ATAURA'),
(10451135, 'JUNIN', 'JAUJA', 'CANCHAILLO'),
(10461136, 'JUNIN', 'JAUJA', 'EL MANTARO'),
(10471137, 'JUNIN', 'JAUJA', 'HUAMALI'),
(10481138, 'JUNIN', 'JAUJA', 'HUARIPAMPA'),
(10491139, 'JUNIN', 'JAUJA', 'HUERTAS'),
(10751141, 'JUNIN', 'JUNIN', 'JUNIN'),
(10761142, 'JUNIN', 'JUNIN', 'CARHUAMAYO'),
(10771143, 'JUNIN', 'JUNIN', 'ONDORES'),
(10781144, 'JUNIN', 'JUNIN', 'ULCUMAYO'),
(10791151, 'JUNIN', 'TARMA', 'TARMA'),
(10801152, 'JUNIN', 'TARMA', 'ACOBAMBA'),
(10811153, 'JUNIN', 'TARMA', 'HUARICOLCA'),
(10821154, 'JUNIN', 'TARMA', 'HUASAHUASI'),
(10831155, 'JUNIN', 'TARMA', 'LA UNION'),
(10841156, 'JUNIN', 'TARMA', 'PALCA'),
(10851157, 'JUNIN', 'TARMA', 'PALCAMAYO'),
(10861158, 'JUNIN', 'TARMA', 'SAN PEDRO DE CAJAS'),
(10871159, 'JUNIN', 'TARMA', 'TAPO'),
(10881161, 'JUNIN', 'YAULI', 'LA OROYA'),
(10891162, 'JUNIN', 'YAULI', 'CHACAPALPA'),
(10901163, 'JUNIN', 'YAULI', 'HUAY HUAY'),
(10911164, 'JUNIN', 'YAULI', 'MARCAPOMACOCHA'),
(10921165, 'JUNIN', 'YAULI', 'MOROCOCHA'),
(10931166, 'JUNIN', 'YAULI', 'PACCHA'),
(10941167, 'JUNIN', 'YAULI', 'STA BARBARA DE CARHUACAYAN'),
(10951168, 'JUNIN', 'YAULI', 'SUITUCANCHA'),
(10961169, 'JUNIN', 'YAULI', 'YAULI'),
(10981171, 'JUNIN', 'SATIPO', 'SATIPO'),
(10991172, 'JUNIN', 'SATIPO', 'COVIRIALI'),
(11001173, 'JUNIN', 'SATIPO', 'LLAYLLA'),
(11011174, 'JUNIN', 'SATIPO', 'MAZAMARI'),
(11021175, 'JUNIN', 'SATIPO', 'PAMPA HERMOSA'),
(11031176, 'JUNIN', 'SATIPO', 'PANGOA'),
(11041177, 'JUNIN', 'SATIPO', 'RIO NEGRO'),
(11051178, 'JUNIN', 'SATIPO', 'RIO TAMBO'),
(11061181, 'JUNIN', 'CHANCHAMAYO', 'CHANCHAMAYO'),
(11071182, 'JUNIN', 'CHANCHAMAYO', 'SAN RAMON'),
(11081183, 'JUNIN', 'CHANCHAMAYO', 'VITOC'),
(11091184, 'JUNIN', 'CHANCHAMAYO', 'SAN LUIS DE SHUARO'),
(11101185, 'JUNIN', 'CHANCHAMAYO', 'PICHANAQUI'),
(11111186, 'JUNIN', 'CHANCHAMAYO', 'PERENE'),
(11121191, 'JUNIN', 'CHUPACA', 'CHUPACA'),
(11131192, 'JUNIN', 'CHUPACA', 'AHUAC'),
(11141193, 'JUNIN', 'CHUPACA', 'CHONGOS BAJO'),
(11151194, 'JUNIN', 'CHUPACA', 'HUACHAC'),
(11161195, 'JUNIN', 'CHUPACA', 'HUAMANCACA CHICO'),
(11171196, 'JUNIN', 'CHUPACA', 'SAN JUAN DE ISCOS'),
(11181197, 'JUNIN', 'CHUPACA', 'SAN JUAN DE JARPA'),
(11191198, 'JUNIN', 'CHUPACA', 'TRES DE DICIEMBRE'),
(11201199, 'JUNIN', 'CHUPACA', 'YANACANCHA'),
(11211211, 'LA LIBERTAD', 'TRUJILLO', 'TRUJILLO'),
(11221212, 'LA LIBERTAD', 'TRUJILLO', 'HUANCHACO'),
(11231213, 'LA LIBERTAD', 'TRUJILLO', 'LAREDO'),
(11241214, 'LA LIBERTAD', 'TRUJILLO', 'MOCHE'),
(11251215, 'LA LIBERTAD', 'TRUJILLO', 'SALAVERRY'),
(11261216, 'LA LIBERTAD', 'TRUJILLO', 'SIMBAL'),
(11271217, 'LA LIBERTAD', 'TRUJILLO', 'VICTOR LARCO HERRERA'),
(11281219, 'LA LIBERTAD', 'TRUJILLO', 'POROTO'),
(11321221, 'LA LIBERTAD', 'BOLIVAR', 'BOLIVAR'),
(11331222, 'LA LIBERTAD', 'BOLIVAR', 'BAMBAMARCA'),
(11341223, 'LA LIBERTAD', 'BOLIVAR', 'CONDORMARCA'),
(11351224, 'LA LIBERTAD', 'BOLIVAR', 'LONGOTEA'),
(11361225, 'LA LIBERTAD', 'BOLIVAR', 'UCUNCHA'),
(11371226, 'LA LIBERTAD', 'BOLIVAR', 'UCHUMARCA'),
(11381231, 'LA LIBERTAD', 'SANCHEZ CARRION', 'HUAMACHUCO'),
(11391232, 'LA LIBERTAD', 'SANCHEZ CARRION', 'COCHORCO'),
(11401233, 'LA LIBERTAD', 'SANCHEZ CARRION', 'CURGOS'),
(11411234, 'LA LIBERTAD', 'SANCHEZ CARRION', 'CHUGAY'),
(11421235, 'LA LIBERTAD', 'SANCHEZ CARRION', 'MARCABAL'),
(11431236, 'LA LIBERTAD', 'SANCHEZ CARRION', 'SANAGORAN'),
(11441237, 'LA LIBERTAD', 'SANCHEZ CARRION', 'SARIN'),
(11451238, 'LA LIBERTAD', 'SANCHEZ CARRION', 'SARTIMBAMBA'),
(11461241, 'LA LIBERTAD', 'OTUZCO', 'OTUZCO'),
(11471242, 'LA LIBERTAD', 'OTUZCO', 'AGALLPAMPA'),
(11481243, 'LA LIBERTAD', 'OTUZCO', 'CHARAT'),
(11491244, 'LA LIBERTAD', 'OTUZCO', 'HUARANCHAL'),
(11501245, 'LA LIBERTAD', 'OTUZCO', 'LA CUESTA'),
(11511248, 'LA LIBERTAD', 'OTUZCO', 'PARANDAY'),
(11521249, 'LA LIBERTAD', 'OTUZCO', 'SALPO'),
(11561251, 'LA LIBERTAD', 'PACASMAYO', 'SAN PEDRO DE LLOC'),
(11571253, 'LA LIBERTAD', 'PACASMAYO', 'GUADALUPE'),
(11581254, 'LA LIBERTAD', 'PACASMAYO', 'JEQUETEPEQUE'),
(11591256, 'LA LIBERTAD', 'PACASMAYO', 'PACASMAYO'),
(11601258, 'LA LIBERTAD', 'PACASMAYO', 'SAN JOSE'),
(11611261, 'LA LIBERTAD', 'PATAZ', 'TAYABAMBA'),
(11621262, 'LA LIBERTAD', 'PATAZ', 'BULDIBUYO'),
(11631263, 'LA LIBERTAD', 'PATAZ', 'CHILLIA'),
(11641264, 'LA LIBERTAD', 'PATAZ', 'HUAYLILLAS'),
(11651265, 'LA LIBERTAD', 'PATAZ', 'HUANCASPATA'),
(11661266, 'LA LIBERTAD', 'PATAZ', 'HUAYO'),
(11671267, 'LA LIBERTAD', 'PATAZ', 'ONGON'),
(11681268, 'LA LIBERTAD', 'PATAZ', 'PARCOY'),
(11691269, 'LA LIBERTAD', 'PATAZ', 'PATAZ'),
(11741271, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'SANTIAGO DE CHUCO'),
(11751272, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'CACHICADAN'),
(11761273, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'MOLLEBAMBA'),
(11771274, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'MOLLEPATA'),
(11781275, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'QUIRUVILCA'),
(11791276, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'SANTA CRUZ DE CHUCA');
INSERT INTO `ubigeos` (`id`, `departamento`, `provincia`, `distrito`) VALUES
(11801277, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'SITABAMBA'),
(11811278, 'LA LIBERTAD', 'SANTIAGO DE CHUCO', 'ANGASMARCA'),
(11821281, 'LA LIBERTAD', 'ASCOPE', 'ASCOPE'),
(11831282, 'LA LIBERTAD', 'ASCOPE', 'CHICAMA'),
(11841283, 'LA LIBERTAD', 'ASCOPE', 'CHOCOPE'),
(11851284, 'LA LIBERTAD', 'ASCOPE', 'SANTIAGO DE CAO'),
(11861285, 'LA LIBERTAD', 'ASCOPE', 'MAGDALENA DE CAO'),
(11871286, 'LA LIBERTAD', 'ASCOPE', 'PAIJAN'),
(11881287, 'LA LIBERTAD', 'ASCOPE', 'RAZURI'),
(11891288, 'LA LIBERTAD', 'ASCOPE', 'CASA GRANDE'),
(11901291, 'LA LIBERTAD', 'CHEPEN', 'CHEPEN'),
(11911292, 'LA LIBERTAD', 'CHEPEN', 'PACANGA'),
(11921293, 'LA LIBERTAD', 'CHEPEN', 'PUEBLO NUEVO'),
(12041311, 'LAMBAYEQUE', 'CHICLAYO', 'CHICLAYO'),
(12051312, 'LAMBAYEQUE', 'CHICLAYO', 'CHONGOYAPE'),
(12061313, 'LAMBAYEQUE', 'CHICLAYO', 'ETEN'),
(12071314, 'LAMBAYEQUE', 'CHICLAYO', 'ETEN PUERTO'),
(12081315, 'LAMBAYEQUE', 'CHICLAYO', 'LAGUNAS'),
(12091316, 'LAMBAYEQUE', 'CHICLAYO', 'MONSEFU'),
(12101317, 'LAMBAYEQUE', 'CHICLAYO', 'NUEVA ARICA'),
(12111318, 'LAMBAYEQUE', 'CHICLAYO', 'OYOTUN'),
(12121319, 'LAMBAYEQUE', 'CHICLAYO', 'PICSI'),
(12241321, 'LAMBAYEQUE', 'FERREÑAFE', 'FERREÑAFE'),
(12251322, 'LAMBAYEQUE', 'FERREÑAFE', 'INCAHUASI'),
(12261323, 'LAMBAYEQUE', 'FERREÑAFE', 'CAÑARIS'),
(12271324, 'LAMBAYEQUE', 'FERREÑAFE', 'PITIPO'),
(12281325, 'LAMBAYEQUE', 'FERREÑAFE', 'PUEBLO NUEVO'),
(12291326, 'LAMBAYEQUE', 'FERREÑAFE', 'MANUEL ANTONIO MESONES MURO'),
(12301331, 'LAMBAYEQUE', 'LAMBAYEQUE', 'LAMBAYEQUE'),
(12311332, 'LAMBAYEQUE', 'LAMBAYEQUE', 'CHOCHOPE'),
(12321333, 'LAMBAYEQUE', 'LAMBAYEQUE', 'ILLIMO'),
(12331334, 'LAMBAYEQUE', 'LAMBAYEQUE', 'JAYANCA'),
(12341335, 'LAMBAYEQUE', 'LAMBAYEQUE', 'MOCHUMI'),
(12351336, 'LAMBAYEQUE', 'LAMBAYEQUE', 'MORROPE'),
(12361337, 'LAMBAYEQUE', 'LAMBAYEQUE', 'MOTUPE'),
(12371338, 'LAMBAYEQUE', 'LAMBAYEQUE', 'OLMOS'),
(12381339, 'LAMBAYEQUE', 'LAMBAYEQUE', 'PACORA'),
(12421411, 'LIMA', 'LIMA', 'LIMA'),
(12431412, 'LIMA', 'LIMA', 'ANCON'),
(12441413, 'LIMA', 'LIMA', 'ATE'),
(12451414, 'LIMA', 'LIMA', 'BREÑA'),
(12461415, 'LIMA', 'LIMA', 'CARABAYLLO'),
(12471416, 'LIMA', 'LIMA', 'COMAS'),
(12481417, 'LIMA', 'LIMA', 'CHACLACAYO'),
(12491418, 'LIMA', 'LIMA', 'CHORRILLOS'),
(12501419, 'LIMA', 'LIMA', 'LA VICTORIA'),
(12851421, 'LIMA', 'CAJATAMBO', 'CAJATAMBO'),
(12861425, 'LIMA', 'CAJATAMBO', 'COPA'),
(12871426, 'LIMA', 'CAJATAMBO', 'GORGOR'),
(12881427, 'LIMA', 'CAJATAMBO', 'HUANCAPON'),
(12891428, 'LIMA', 'CAJATAMBO', 'MANAS'),
(12901431, 'LIMA', 'CANTA', 'CANTA'),
(12911432, 'LIMA', 'CANTA', 'ARAHUAY'),
(12921433, 'LIMA', 'CANTA', 'HUAMANTANGA'),
(12931434, 'LIMA', 'CANTA', 'HUAROS'),
(12941435, 'LIMA', 'CANTA', 'LACHAQUI'),
(12951436, 'LIMA', 'CANTA', 'SAN BUENAVENTURA'),
(12961437, 'LIMA', 'CANTA', 'SANTA ROSA DE QUIVES'),
(12971441, 'LIMA', 'CAÑETE', 'SAN VICENTE DE CAÑETE'),
(12981442, 'LIMA', 'CAÑETE', 'CALANGO'),
(12991443, 'LIMA', 'CAÑETE', 'CERRO AZUL'),
(13001444, 'LIMA', 'CAÑETE', 'COAYLLO'),
(13011445, 'LIMA', 'CAÑETE', 'CHILCA'),
(13021446, 'LIMA', 'CAÑETE', 'IMPERIAL'),
(13031447, 'LIMA', 'CAÑETE', 'LUNAHUANA'),
(13041448, 'LIMA', 'CAÑETE', 'MALA'),
(13051449, 'LIMA', 'CAÑETE', 'NUEVO IMPERIAL'),
(13131451, 'LIMA', 'HUAURA', 'HUACHO'),
(13141452, 'LIMA', 'HUAURA', 'AMBAR'),
(13151454, 'LIMA', 'HUAURA', 'CALETA DE CARQUIN'),
(13161455, 'LIMA', 'HUAURA', 'CHECRAS'),
(13171456, 'LIMA', 'HUAURA', 'HUALMAY'),
(13181457, 'LIMA', 'HUAURA', 'HUAURA'),
(13191458, 'LIMA', 'HUAURA', 'LEONCIO PRADO'),
(13201459, 'LIMA', 'HUAURA', 'PACCHO'),
(13251461, 'LIMA', 'HUAROCHIRI', 'MATUCANA'),
(13261462, 'LIMA', 'HUAROCHIRI', 'ANTIOQUIA'),
(13271463, 'LIMA', 'HUAROCHIRI', 'CALLAHUANCA'),
(13281464, 'LIMA', 'HUAROCHIRI', 'CARAMPOMA'),
(13291465, 'LIMA', 'HUAROCHIRI', 'CASTA'),
(13301466, 'LIMA', 'HUAROCHIRI', 'SAN JOSE DE LOS CHORRILLOS'),
(13311467, 'LIMA', 'HUAROCHIRI', 'CHICLA'),
(13321468, 'LIMA', 'HUAROCHIRI', 'HUANZA'),
(13331469, 'LIMA', 'HUAROCHIRI', 'HUAROCHIRI'),
(13571471, 'LIMA', 'YAUYOS', 'YAUYOS'),
(13581472, 'LIMA', 'YAUYOS', 'ALIS'),
(13591473, 'LIMA', 'YAUYOS', 'AYAUCA'),
(13601474, 'LIMA', 'YAUYOS', 'AYAVIRI'),
(13611475, 'LIMA', 'YAUYOS', 'AZANGARO'),
(13621476, 'LIMA', 'YAUYOS', 'CACRA'),
(13631477, 'LIMA', 'YAUYOS', 'CARANIA'),
(13641478, 'LIMA', 'YAUYOS', 'COCHAS'),
(13651479, 'LIMA', 'YAUYOS', 'COLONIA'),
(13901481, 'LIMA', 'HUARAL', 'HUARAL'),
(13911482, 'LIMA', 'HUARAL', 'ATAVILLOS ALTO'),
(13921483, 'LIMA', 'HUARAL', 'ATAVILLOS BAJO'),
(13931484, 'LIMA', 'HUARAL', 'AUCALLAMA'),
(13941485, 'LIMA', 'HUARAL', 'CHANCAY'),
(13951486, 'LIMA', 'HUARAL', 'IHUARI'),
(13961487, 'LIMA', 'HUARAL', 'LAMPIAN'),
(13971488, 'LIMA', 'HUARAL', 'PACARAOS'),
(13981489, 'LIMA', 'HUARAL', 'SAN MIGUEL DE ACOS'),
(14021491, 'LIMA', 'BARRANCA', 'BARRANCA'),
(14031492, 'LIMA', 'BARRANCA', 'PARAMONGA'),
(14041493, 'LIMA', 'BARRANCA', 'PATIVILCA'),
(14051494, 'LIMA', 'BARRANCA', 'SUPE'),
(14061495, 'LIMA', 'BARRANCA', 'SUPE PUERTO'),
(14131511, 'LORETO', 'MAYNAS', 'IQUITOS'),
(14141512, 'LORETO', 'MAYNAS', 'ALTO NANAY'),
(14151513, 'LORETO', 'MAYNAS', 'FERNANDO LORES'),
(14161514, 'LORETO', 'MAYNAS', 'LAS AMAZONAS'),
(14171515, 'LORETO', 'MAYNAS', 'MAZAN'),
(14181516, 'LORETO', 'MAYNAS', 'NAPO'),
(14191517, 'LORETO', 'MAYNAS', 'PUTUMAYO'),
(14201518, 'LORETO', 'MAYNAS', 'TORRES CAUSANA'),
(14261521, 'LORETO', 'ALTO AMAZONAS', 'YURIMAGUAS'),
(14271522, 'LORETO', 'ALTO AMAZONAS', 'BALSAPUERTO'),
(14281525, 'LORETO', 'ALTO AMAZONAS', 'JEBEROS'),
(14291526, 'LORETO', 'ALTO AMAZONAS', 'LAGUNAS'),
(14321531, 'LORETO', 'LORETO', 'NAUTA'),
(14331532, 'LORETO', 'LORETO', 'PARINARI'),
(14341533, 'LORETO', 'LORETO', 'TIGRE'),
(14351534, 'LORETO', 'LORETO', 'URARINAS'),
(14361535, 'LORETO', 'LORETO', 'TROMPETEROS'),
(14371541, 'LORETO', 'REQUENA', 'REQUENA'),
(14381542, 'LORETO', 'REQUENA', 'ALTO TAPICHE'),
(14391543, 'LORETO', 'REQUENA', 'CAPELO'),
(14401544, 'LORETO', 'REQUENA', 'EMILIO SAN MARTIN'),
(14411545, 'LORETO', 'REQUENA', 'MAQUIA'),
(14421546, 'LORETO', 'REQUENA', 'PUINAHUA'),
(14431547, 'LORETO', 'REQUENA', 'SAPUENA'),
(14441548, 'LORETO', 'REQUENA', 'SOPLIN'),
(14451549, 'LORETO', 'REQUENA', 'TAPICHE'),
(14481551, 'LORETO', 'UCAYALI', 'CONTAMANA'),
(14491552, 'LORETO', 'UCAYALI', 'VARGAS GUERRA'),
(14501553, 'LORETO', 'UCAYALI', 'PADRE MARQUEZ'),
(14511554, 'LORETO', 'UCAYALI', 'PAMPA HERMOZA'),
(14521555, 'LORETO', 'UCAYALI', 'SARAYACU'),
(14531556, 'LORETO', 'UCAYALI', 'INAHUAYA'),
(14541561, 'LORETO', 'MARISCAL RAMON CASTILLA', 'MARISCAL RAMON CASTILLA'),
(14551562, 'LORETO', 'MARISCAL RAMON CASTILLA', 'PEBAS'),
(14561563, 'LORETO', 'MARISCAL RAMON CASTILLA', 'YAVARI'),
(14571564, 'LORETO', 'MARISCAL RAMON CASTILLA', 'SAN PABLO'),
(14581571, 'LORETO', 'DATEM DEL MARAÑON', 'BARRANCA'),
(14591572, 'LORETO', 'DATEM DEL MARAÑON', 'ANDOAS'),
(14601573, 'LORETO', 'DATEM DEL MARAÑON', 'CAHUAPANAS'),
(14611574, 'LORETO', 'DATEM DEL MARAÑON', 'MANSERICHE'),
(14621575, 'LORETO', 'DATEM DEL MARAÑON', 'MORONA'),
(14631576, 'LORETO', 'DATEM DEL MARAÑON', 'PASTAZA'),
(14641611, 'MADRE DE DIOS', 'TAMBOPATA', 'TAMBOPATA'),
(14651612, 'MADRE DE DIOS', 'TAMBOPATA', 'INAMBARI'),
(14661613, 'MADRE DE DIOS', 'TAMBOPATA', 'LAS PIEDRAS'),
(14671614, 'MADRE DE DIOS', 'TAMBOPATA', 'LABERINTO'),
(14681621, 'MADRE DE DIOS', 'MANU', 'MANU'),
(14691622, 'MADRE DE DIOS', 'MANU', 'FITZCARRALD'),
(14701623, 'MADRE DE DIOS', 'MANU', 'MADRE DE DIOS'),
(14711624, 'MADRE DE DIOS', 'MANU', 'HUEPETUHE'),
(14721631, 'MADRE DE DIOS', 'TAHUAMANU', 'IÑAPARI'),
(14731632, 'MADRE DE DIOS', 'TAHUAMANU', 'IBERIA'),
(14741633, 'MADRE DE DIOS', 'TAHUAMANU', 'TAHUAMANU'),
(14751711, 'MOQUEGUA', 'MARISCAL NIETO', 'MOQUEGUA'),
(14761712, 'MOQUEGUA', 'MARISCAL NIETO', 'CARUMAS'),
(14771713, 'MOQUEGUA', 'MARISCAL NIETO', 'CUCHUMBAYA'),
(14781714, 'MOQUEGUA', 'MARISCAL NIETO', 'SAN CRISTOBAL'),
(14791715, 'MOQUEGUA', 'MARISCAL NIETO', 'TORATA'),
(14801716, 'MOQUEGUA', 'MARISCAL NIETO', 'SAMEGUA'),
(14811721, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'OMATE'),
(14821722, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'COALAQUE'),
(14831723, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'CHOJATA'),
(14841724, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'ICHUÑA'),
(14851725, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'LA CAPILLA'),
(14861726, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'LLOQUE'),
(14871727, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'MATALAQUE'),
(14881728, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'PUQUINA'),
(14891729, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'QUINISTAQUILLAS'),
(14921731, 'MOQUEGUA', 'ILO', 'ILO'),
(14931732, 'MOQUEGUA', 'ILO', 'EL ALGARROBAL'),
(14941733, 'MOQUEGUA', 'ILO', 'PACOCHA'),
(14951811, 'PASCO', 'PASCO', 'CHAUPIMARCA'),
(14961813, 'PASCO', 'PASCO', 'HUACHON'),
(14971814, 'PASCO', 'PASCO', 'HUARIACA'),
(14981815, 'PASCO', 'PASCO', 'HUAYLLAY'),
(14991816, 'PASCO', 'PASCO', 'NINACACA'),
(15001817, 'PASCO', 'PASCO', 'PALLANCHACRA'),
(15011818, 'PASCO', 'PASCO', 'PAUCARTAMBO'),
(15021819, 'PASCO', 'PASCO', 'SAN FCO DE ASIS DE YARUSYACAN'),
(15081821, 'PASCO', 'DANIEL ALCIDES CARRION', 'YANAHUANCA'),
(15091822, 'PASCO', 'DANIEL ALCIDES CARRION', 'CHACAYAN'),
(15101823, 'PASCO', 'DANIEL ALCIDES CARRION', 'GOYLLARISQUIZGA'),
(15111824, 'PASCO', 'DANIEL ALCIDES CARRION', 'PAUCAR'),
(15121825, 'PASCO', 'DANIEL ALCIDES CARRION', 'SAN PEDRO DE PILLAO'),
(15131826, 'PASCO', 'DANIEL ALCIDES CARRION', 'SANTA ANA DE TUSI'),
(15141827, 'PASCO', 'DANIEL ALCIDES CARRION', 'TAPUC'),
(15151828, 'PASCO', 'DANIEL ALCIDES CARRION', 'VILCABAMBA'),
(15161831, 'PASCO', 'OXAPAMPA', 'OXAPAMPA'),
(15171832, 'PASCO', 'OXAPAMPA', 'CHONTABAMBA'),
(15181833, 'PASCO', 'OXAPAMPA', 'HUANCABAMBA'),
(15191834, 'PASCO', 'OXAPAMPA', 'PUERTO BERMUDEZ'),
(15201835, 'PASCO', 'OXAPAMPA', 'VILLA RICA'),
(15211836, 'PASCO', 'OXAPAMPA', 'POZUZO'),
(15221837, 'PASCO', 'OXAPAMPA', 'PALCAZU'),
(15231911, 'PIURA', 'PIURA', 'PIURA'),
(15241913, 'PIURA', 'PIURA', 'CASTILLA'),
(15251914, 'PIURA', 'PIURA', 'CATACAOS'),
(15261915, 'PIURA', 'PIURA', 'LA ARENA'),
(15271916, 'PIURA', 'PIURA', 'LA UNION'),
(15281917, 'PIURA', 'PIURA', 'LAS LOMAS'),
(15291919, 'PIURA', 'PIURA', 'TAMBO GRANDE'),
(15321921, 'PIURA', 'AYABACA', 'AYABACA'),
(15331922, 'PIURA', 'AYABACA', 'FRIAS'),
(15341923, 'PIURA', 'AYABACA', 'LAGUNAS'),
(15351924, 'PIURA', 'AYABACA', 'MONTERO'),
(15361925, 'PIURA', 'AYABACA', 'PACAIPAMPA'),
(15371926, 'PIURA', 'AYABACA', 'SAPILLICA'),
(15381927, 'PIURA', 'AYABACA', 'SICCHEZ'),
(15391928, 'PIURA', 'AYABACA', 'SUYO'),
(15401929, 'PIURA', 'AYABACA', 'JILILI'),
(15421931, 'PIURA', 'HUANCABAMBA', 'HUANCABAMBA'),
(15431932, 'PIURA', 'HUANCABAMBA', 'CANCHAQUE'),
(15441933, 'PIURA', 'HUANCABAMBA', 'HUARMACA'),
(15451934, 'PIURA', 'HUANCABAMBA', 'SONDOR'),
(15461935, 'PIURA', 'HUANCABAMBA', 'SONDORILLO'),
(15471936, 'PIURA', 'HUANCABAMBA', 'EL CARMEN DE LA FRONTERA'),
(15481937, 'PIURA', 'HUANCABAMBA', 'SAN MIGUEL DE EL FAIQUE'),
(15491938, 'PIURA', 'HUANCABAMBA', 'LALAQUIZ'),
(15501941, 'PIURA', 'MORROPON', 'CHULUCANAS'),
(15511942, 'PIURA', 'MORROPON', 'BUENOS AIRES'),
(15521943, 'PIURA', 'MORROPON', 'CHALACO'),
(15531944, 'PIURA', 'MORROPON', 'MORROPON'),
(15541945, 'PIURA', 'MORROPON', 'SALITRAL'),
(15551946, 'PIURA', 'MORROPON', 'SANTA CATALINA DE MOSSA'),
(15561947, 'PIURA', 'MORROPON', 'SANTO DOMINGO'),
(15571948, 'PIURA', 'MORROPON', 'LA MATANZA'),
(15581949, 'PIURA', 'MORROPON', 'YAMANGO'),
(15601951, 'PIURA', 'PAITA', 'PAITA'),
(15611952, 'PIURA', 'PAITA', 'AMOTAPE'),
(15621953, 'PIURA', 'PAITA', 'ARENAL'),
(15631954, 'PIURA', 'PAITA', 'LA HUACA'),
(15641955, 'PIURA', 'PAITA', 'PUEBLO NUEVO DE COLAN'),
(15651956, 'PIURA', 'PAITA', 'TAMARINDO'),
(15661957, 'PIURA', 'PAITA', 'VICHAYAL'),
(15671961, 'PIURA', 'SULLANA', 'SULLANA'),
(15681962, 'PIURA', 'SULLANA', 'BELLAVISTA'),
(15691963, 'PIURA', 'SULLANA', 'LANCONES'),
(15701964, 'PIURA', 'SULLANA', 'MARCAVELICA'),
(15711965, 'PIURA', 'SULLANA', 'MIGUEL CHECA'),
(15721966, 'PIURA', 'SULLANA', 'QUERECOTILLO'),
(15731967, 'PIURA', 'SULLANA', 'SALITRAL'),
(15741968, 'PIURA', 'SULLANA', 'IGNACIO ESCUDERO'),
(15751971, 'PIURA', 'TALARA', 'PARIÑAS'),
(15761972, 'PIURA', 'TALARA', 'EL ALTO'),
(15771973, 'PIURA', 'TALARA', 'LA BREA'),
(15781974, 'PIURA', 'TALARA', 'LOBITOS'),
(15791975, 'PIURA', 'TALARA', 'MANCORA'),
(15801976, 'PIURA', 'TALARA', 'LOS ORGANOS'),
(15811981, 'PIURA', 'SECHURA', 'SECHURA'),
(15821982, 'PIURA', 'SECHURA', 'VICE'),
(15831983, 'PIURA', 'SECHURA', 'BERNAL'),
(15841984, 'PIURA', 'SECHURA', 'BELLAVISTA DE LA UNION'),
(15851985, 'PIURA', 'SECHURA', 'CRISTO NOS VALGA'),
(15861986, 'PIURA', 'SECHURA', 'RINCONADA LLICUAR'),
(15872011, 'PUNO', 'PUNO', 'PUNO'),
(15882012, 'PUNO', 'PUNO', 'ACORA'),
(15892013, 'PUNO', 'PUNO', 'ATUNCOLLA'),
(15902014, 'PUNO', 'PUNO', 'CAPACHICA'),
(15912015, 'PUNO', 'PUNO', 'COATA'),
(15922016, 'PUNO', 'PUNO', 'CHUCUITO'),
(15932017, 'PUNO', 'PUNO', 'HUATA'),
(15942018, 'PUNO', 'PUNO', 'MAÑAZO'),
(15952019, 'PUNO', 'PUNO', 'PAUCARCOLLA'),
(16022021, 'PUNO', 'AZANGARO', 'AZANGARO'),
(16032022, 'PUNO', 'AZANGARO', 'ACHAYA'),
(16042023, 'PUNO', 'AZANGARO', 'ARAPA'),
(16052024, 'PUNO', 'AZANGARO', 'ASILLO'),
(16062025, 'PUNO', 'AZANGARO', 'CAMINACA'),
(16072026, 'PUNO', 'AZANGARO', 'CHUPA'),
(16082027, 'PUNO', 'AZANGARO', 'JOSE DOMINGO CHOQUEHUANCA'),
(16092028, 'PUNO', 'AZANGARO', 'MUÑANI'),
(16172031, 'PUNO', 'CARABAYA', 'MACUSANI'),
(16182032, 'PUNO', 'CARABAYA', 'AJOYANI'),
(16192033, 'PUNO', 'CARABAYA', 'AYAPATA'),
(16202034, 'PUNO', 'CARABAYA', 'COASA'),
(16212035, 'PUNO', 'CARABAYA', 'CORANI'),
(16222036, 'PUNO', 'CARABAYA', 'CRUCERO'),
(16232037, 'PUNO', 'CARABAYA', 'ITUATA'),
(16242038, 'PUNO', 'CARABAYA', 'OLLACHEA'),
(16252039, 'PUNO', 'CARABAYA', 'SAN GABAN'),
(16272041, 'PUNO', 'CHUCUITO', 'JULI'),
(16282042, 'PUNO', 'CHUCUITO', 'DESAGUADERO'),
(16292043, 'PUNO', 'CHUCUITO', 'HUACULLANI'),
(16302046, 'PUNO', 'CHUCUITO', 'PISACOMA'),
(16312047, 'PUNO', 'CHUCUITO', 'POMATA'),
(16342051, 'PUNO', 'HUANCANE', 'HUANCANE'),
(16352052, 'PUNO', 'HUANCANE', 'COJATA'),
(16362054, 'PUNO', 'HUANCANE', 'INCHUPALLA'),
(16372056, 'PUNO', 'HUANCANE', 'PUSI'),
(16382057, 'PUNO', 'HUANCANE', 'ROSASPATA'),
(16392058, 'PUNO', 'HUANCANE', 'TARACO'),
(16402059, 'PUNO', 'HUANCANE', 'VILQUE CHICO'),
(16422061, 'PUNO', 'LAMPA', 'LAMPA'),
(16432062, 'PUNO', 'LAMPA', 'CABANILLA'),
(16442063, 'PUNO', 'LAMPA', 'CALAPUJA'),
(16452064, 'PUNO', 'LAMPA', 'NICASIO'),
(16462065, 'PUNO', 'LAMPA', 'OCUVIRI'),
(16472066, 'PUNO', 'LAMPA', 'PALCA'),
(16482067, 'PUNO', 'LAMPA', 'PARATIA'),
(16492068, 'PUNO', 'LAMPA', 'PUCARA'),
(16502069, 'PUNO', 'LAMPA', 'SANTA LUCIA'),
(16522071, 'PUNO', 'MELGAR', 'AYAVIRI'),
(16532072, 'PUNO', 'MELGAR', 'ANTAUTA'),
(16542073, 'PUNO', 'MELGAR', 'CUPI'),
(16552074, 'PUNO', 'MELGAR', 'LLALLI'),
(16562075, 'PUNO', 'MELGAR', 'MACARI'),
(16572076, 'PUNO', 'MELGAR', 'NUÑOA'),
(16582077, 'PUNO', 'MELGAR', 'ORURILLO'),
(16592078, 'PUNO', 'MELGAR', 'SANTA ROSA'),
(16602079, 'PUNO', 'MELGAR', 'UMACHIRI'),
(16612081, 'PUNO', 'SANDIA', 'SANDIA'),
(16622083, 'PUNO', 'SANDIA', 'CUYOCUYO'),
(16632084, 'PUNO', 'SANDIA', 'LIMBANI'),
(16642085, 'PUNO', 'SANDIA', 'PHARA'),
(16652086, 'PUNO', 'SANDIA', 'PATAMBUCO'),
(16662087, 'PUNO', 'SANDIA', 'QUIACA'),
(16672088, 'PUNO', 'SANDIA', 'SAN JUAN DEL ORO'),
(16712091, 'PUNO', 'SAN ROMAN', 'JULIACA'),
(16722092, 'PUNO', 'SAN ROMAN', 'CABANA'),
(16732093, 'PUNO', 'SAN ROMAN', 'CABANILLAS'),
(16742094, 'PUNO', 'SAN ROMAN', 'CARACOTO'),
(16962111, 'SAN MARTIN', 'MOYOBAMBA', 'MOYOBAMBA'),
(16972112, 'SAN MARTIN', 'MOYOBAMBA', 'CALZADA'),
(16982113, 'SAN MARTIN', 'MOYOBAMBA', 'HABANA'),
(16992114, 'SAN MARTIN', 'MOYOBAMBA', 'JEPELACIO'),
(17002115, 'SAN MARTIN', 'MOYOBAMBA', 'SORITOR'),
(17012116, 'SAN MARTIN', 'MOYOBAMBA', 'YANTALO'),
(17022121, 'SAN MARTIN', 'HUALLAGA', 'SAPOSOA'),
(17032122, 'SAN MARTIN', 'HUALLAGA', 'PISCOYACU'),
(17042123, 'SAN MARTIN', 'HUALLAGA', 'SACANCHE'),
(17052124, 'SAN MARTIN', 'HUALLAGA', 'TINGO DE SAPOSOA'),
(17062125, 'SAN MARTIN', 'HUALLAGA', 'ALTO SAPOSOA'),
(17072126, 'SAN MARTIN', 'HUALLAGA', 'EL ESLABON'),
(17082131, 'SAN MARTIN', 'LAMAS', 'LAMAS'),
(17092133, 'SAN MARTIN', 'LAMAS', 'BARRANQUITA'),
(17102134, 'SAN MARTIN', 'LAMAS', 'CAYNARACHI'),
(17112135, 'SAN MARTIN', 'LAMAS', 'CUÑUMBUQUI'),
(17122136, 'SAN MARTIN', 'LAMAS', 'PINTO RECODO'),
(17132137, 'SAN MARTIN', 'LAMAS', 'RUMISAPA'),
(17192141, 'SAN MARTIN', 'MARISCAL CACERES', 'JUANJUI'),
(17202142, 'SAN MARTIN', 'MARISCAL CACERES', 'CAMPANILLA'),
(17212143, 'SAN MARTIN', 'MARISCAL CACERES', 'HUICUNGO'),
(17222144, 'SAN MARTIN', 'MARISCAL CACERES', 'PACHIZA'),
(17232145, 'SAN MARTIN', 'MARISCAL CACERES', 'PAJARILLO'),
(17242151, 'SAN MARTIN', 'RIOJA', 'RIOJA'),
(17252152, 'SAN MARTIN', 'RIOJA', 'POSIC'),
(17262153, 'SAN MARTIN', 'RIOJA', 'YORONGOS'),
(17272154, 'SAN MARTIN', 'RIOJA', 'YURACYACU'),
(17282155, 'SAN MARTIN', 'RIOJA', 'NUEVA CAJAMARCA'),
(17292156, 'SAN MARTIN', 'RIOJA', 'ELIAS SOPLIN'),
(17302157, 'SAN MARTIN', 'RIOJA', 'SAN FERNANDO'),
(17312158, 'SAN MARTIN', 'RIOJA', 'PARDO MIGUEL'),
(17322159, 'SAN MARTIN', 'RIOJA', 'AWAJUN'),
(17332161, 'SAN MARTIN', 'SAN MARTIN', 'TARAPOTO'),
(17342162, 'SAN MARTIN', 'SAN MARTIN', 'ALBERTO LEVEAU'),
(17352164, 'SAN MARTIN', 'SAN MARTIN', 'CACATACHI'),
(17362166, 'SAN MARTIN', 'SAN MARTIN', 'CHAZUTA'),
(17372167, 'SAN MARTIN', 'SAN MARTIN', 'CHIPURANA'),
(17382168, 'SAN MARTIN', 'SAN MARTIN', 'EL PORVENIR'),
(17392169, 'SAN MARTIN', 'SAN MARTIN', 'HUIMBAYOC'),
(17472171, 'SAN MARTIN', 'BELLAVISTA', 'BELLAVISTA'),
(17482172, 'SAN MARTIN', 'BELLAVISTA', 'SAN RAFAEL'),
(17492173, 'SAN MARTIN', 'BELLAVISTA', 'SAN PABLO'),
(17502174, 'SAN MARTIN', 'BELLAVISTA', 'ALTO BIAVO'),
(17512175, 'SAN MARTIN', 'BELLAVISTA', 'HUALLAGA'),
(17522176, 'SAN MARTIN', 'BELLAVISTA', 'BAJO BIAVO'),
(17532181, 'SAN MARTIN', 'TOCACHE', 'TOCACHE'),
(17542182, 'SAN MARTIN', 'TOCACHE', 'NUEVO PROGRESO'),
(17552183, 'SAN MARTIN', 'TOCACHE', 'POLVORA'),
(17562184, 'SAN MARTIN', 'TOCACHE', 'SHUNTE'),
(17572185, 'SAN MARTIN', 'TOCACHE', 'UCHIZA'),
(17582191, 'SAN MARTIN', 'PICOTA', 'PICOTA'),
(17592192, 'SAN MARTIN', 'PICOTA', 'BUENOS AIRES'),
(17602193, 'SAN MARTIN', 'PICOTA', 'CASPIZAPA'),
(17612194, 'SAN MARTIN', 'PICOTA', 'PILLUANA'),
(17622195, 'SAN MARTIN', 'PICOTA', 'PUCACACA'),
(17632196, 'SAN MARTIN', 'PICOTA', 'SAN CRISTOBAL'),
(17642197, 'SAN MARTIN', 'PICOTA', 'SAN HILARION'),
(17652198, 'SAN MARTIN', 'PICOTA', 'TINGO DE PONASA'),
(17662199, 'SAN MARTIN', 'PICOTA', 'TRES UNIDOS'),
(17732211, 'TACNA', 'TACNA', 'TACNA'),
(17742212, 'TACNA', 'TACNA', 'CALANA'),
(17752214, 'TACNA', 'TACNA', 'INCLAN'),
(17762217, 'TACNA', 'TACNA', 'PACHIA'),
(17772218, 'TACNA', 'TACNA', 'PALCA'),
(17782219, 'TACNA', 'TACNA', 'POCOLLAY'),
(17832221, 'TACNA', 'TARATA', 'TARATA'),
(17842225, 'TACNA', 'TARATA', 'HEROES ALBARRACIN'),
(17852226, 'TACNA', 'TARATA', 'ESTIQUE'),
(17862227, 'TACNA', 'TARATA', 'ESTIQUE PAMPA'),
(17912231, 'TACNA', 'JORGE BASADRE', 'LOCUMBA'),
(17922232, 'TACNA', 'JORGE BASADRE', 'ITE'),
(17932233, 'TACNA', 'JORGE BASADRE', 'ILABAYA'),
(17942241, 'TACNA', 'CANDARAVE', 'CANDARAVE'),
(17952242, 'TACNA', 'CANDARAVE', 'CAIRANI'),
(17962243, 'TACNA', 'CANDARAVE', 'CURIBAYA'),
(17972244, 'TACNA', 'CANDARAVE', 'HUANUARA'),
(17982245, 'TACNA', 'CANDARAVE', 'QUILAHUANI'),
(17992246, 'TACNA', 'CANDARAVE', 'CAMILACA'),
(18002311, 'TUMBES', 'TUMBES', 'TUMBES'),
(18012312, 'TUMBES', 'TUMBES', 'CORRALES'),
(18022313, 'TUMBES', 'TUMBES', 'LA CRUZ'),
(18032314, 'TUMBES', 'TUMBES', 'PAMPAS DE HOSPITAL'),
(18042315, 'TUMBES', 'TUMBES', 'SAN JACINTO'),
(18052316, 'TUMBES', 'TUMBES', 'SAN JUAN DE LA VIRGEN'),
(18062321, 'TUMBES', 'CONTRALMIRANTE VILLAR', 'ZORRITOS'),
(18072322, 'TUMBES', 'CONTRALMIRANTE VILLAR', 'CASITAS'),
(18082323, 'TUMBES', 'CONTRALMIRANTE VILLAR', 'CANOAS DE PUNTA SAL'),
(18092331, 'TUMBES', 'ZARUMILLA', 'ZARUMILLA'),
(18102332, 'TUMBES', 'ZARUMILLA', 'MATAPALO'),
(18112333, 'TUMBES', 'ZARUMILLA', 'PAPAYAL'),
(18121010, 'ANCASH', 'PALLASCA', 'SANTA ROSA'),
(18122334, 'TUMBES', 'ZARUMILLA', 'AGUAS VERDES'),
(18132411, 'REGION CALLAO', 'CALLAO', 'CALLAO'),
(18142412, 'REGION CALLAO', 'CALLAO', 'BELLAVISTA'),
(18152413, 'REGION CALLAO', 'CALLAO', 'LA PUNTA'),
(18162414, 'REGION CALLAO', 'CALLAO', 'CARMEN DE LA LEGUA-REYNOSO'),
(18172415, 'REGION CALLAO', 'CALLAO', 'LA PERLA'),
(18182416, 'REGION CALLAO', 'CALLAO', 'VENTANILLA'),
(18192511, 'UCAYALI', 'CORONEL PORTILLO', 'CALLERIA'),
(18202512, 'UCAYALI', 'CORONEL PORTILLO', 'YARINACOCHA'),
(18212513, 'UCAYALI', 'CORONEL PORTILLO', 'MASISEA'),
(18221011, 'ANCASH', 'PALLASCA', 'TAUCA'),
(18222514, 'UCAYALI', 'CORONEL PORTILLO', 'CAMPOVERDE'),
(18232515, 'UCAYALI', 'CORONEL PORTILLO', 'IPARIA'),
(18242516, 'UCAYALI', 'CORONEL PORTILLO', 'NUEVA REQUENA'),
(18252517, 'UCAYALI', 'CORONEL PORTILLO', 'MANANTAY'),
(18262521, 'UCAYALI', 'PADRE ABAD', 'PADRE ABAD'),
(18272522, 'UCAYALI', 'PADRE ABAD', 'YRAZOLA'),
(18282523, 'UCAYALI', 'PADRE ABAD', 'CURIMANA'),
(18292531, 'UCAYALI', 'ATALAYA', 'RAIMONDI'),
(18302532, 'UCAYALI', 'ATALAYA', 'TAHUANIA'),
(18312533, 'UCAYALI', 'ATALAYA', 'YURUA'),
(18322534, 'UCAYALI', 'ATALAYA', 'SEPAHUA'),
(18332541, 'UCAYALI', 'PURUS', 'PURUS'),
(19621210, 'ANCASH', 'RECUAY', 'CATAC'),
(21521410, 'ANCASH', 'SIHUAS', 'SAN JUAN'),
(24922010, 'ANCASH', 'OCROS', 'SANTIAGO DE CHILCAS'),
(53851010, 'AYACUCHO', 'PAUCAR DEL SARA SARA', 'SARA SARA'),
(54851110, 'AYACUCHO', 'SUCRE', 'CHILCAYOC'),
(54951111, 'AYACUCHO', 'SUCRE', 'MORCOLLA'),
(65561010, 'CAJAMARCA', 'SAN MIGUEL', 'UNION AGUA BLANCA'),
(65661011, 'CAJAMARCA', 'SAN MIGUEL', 'TONGOD'),
(65761012, 'CAJAMARCA', 'SAN MIGUEL', 'CATILLUC'),
(65861013, 'CAJAMARCA', 'SAN MIGUEL', 'BOLIVAR'),
(77571210, 'CUSCO', 'QUISPICANCHI', 'OCONGATE'),
(77671211, 'CUSCO', 'QUISPICANCHI', 'OROPESA'),
(77771212, 'CUSCO', 'QUISPICANCHI', 'QUIQUIJANA'),
(96410110, 'ICA', 'ICA', 'SUBTANJALLA'),
(96510111, 'ICA', 'ICA', 'YAUCA DEL ROSARIO'),
(96610112, 'ICA', 'ICA', 'TATE'),
(96710113, 'ICA', 'ICA', 'PACHACUTEC'),
(96810114, 'ICA', 'ICA', 'OCUCAJE'),
(97810210, 'ICA', 'CHINCHA', 'PUEBLO NUEVO'),
(97910211, 'ICA', 'CHINCHA', 'SAN JUAN DE YANAC'),
(100611112, 'JUNIN', 'HUANCAYO', 'CHUPURO'),
(100711113, 'JUNIN', 'HUANCAYO', 'EL TAMBO'),
(100811114, 'JUNIN', 'HUANCAYO', 'HUACRAPUQUIO'),
(100911116, 'JUNIN', 'HUANCAYO', 'HUALHUAS'),
(101011118, 'JUNIN', 'HUANCAYO', 'HUANCAN'),
(101111119, 'JUNIN', 'HUANCAYO', 'HUASICANCHA'),
(101211120, 'JUNIN', 'HUANCAYO', 'HUAYUCACHI'),
(101311121, 'JUNIN', 'HUANCAYO', 'INGENIO'),
(101411122, 'JUNIN', 'HUANCAYO', 'PARIAHUANCA'),
(101511123, 'JUNIN', 'HUANCAYO', 'PILCOMAYO'),
(101611124, 'JUNIN', 'HUANCAYO', 'PUCARA'),
(101711125, 'JUNIN', 'HUANCAYO', 'QUICHUAY'),
(101811126, 'JUNIN', 'HUANCAYO', 'QUILCAS'),
(101911127, 'JUNIN', 'HUANCAYO', 'SAN AGUSTIN'),
(102011128, 'JUNIN', 'HUANCAYO', 'SAN JERONIMO DE TUNAN'),
(102111131, 'JUNIN', 'HUANCAYO', 'STO DOMINGO DE ACOBAMBA'),
(102211132, 'JUNIN', 'HUANCAYO', 'SAÑO'),
(102311133, 'JUNIN', 'HUANCAYO', 'SAPALLANGA'),
(102411134, 'JUNIN', 'HUANCAYO', 'SICAYA'),
(102511136, 'JUNIN', 'HUANCAYO', 'VIQUES'),
(103511210, 'JUNIN', 'CONCEPCION', 'MATAHUASI'),
(103611211, 'JUNIN', 'CONCEPCION', 'MITO'),
(103711212, 'JUNIN', 'CONCEPCION', 'NUEVE DE JULIO'),
(103811213, 'JUNIN', 'CONCEPCION', 'ORCOTUNA'),
(103911214, 'JUNIN', 'CONCEPCION', 'STA ROSA DE OCOPA'),
(104011215, 'JUNIN', 'CONCEPCION', 'SAN JOSE DE QUERO'),
(105011310, 'JUNIN', 'JAUJA', 'JANJAILLO'),
(105111311, 'JUNIN', 'JAUJA', 'JULCAN'),
(105211312, 'JUNIN', 'JAUJA', 'LEONOR ORDOÑEZ'),
(105311313, 'JUNIN', 'JAUJA', 'LLOCLLAPAMPA'),
(105411314, 'JUNIN', 'JAUJA', 'MARCO'),
(105511315, 'JUNIN', 'JAUJA', 'MASMA'),
(105611316, 'JUNIN', 'JAUJA', 'MOLINOS'),
(105711317, 'JUNIN', 'JAUJA', 'MONOBAMBA'),
(105811318, 'JUNIN', 'JAUJA', 'MUQUI'),
(105911319, 'JUNIN', 'JAUJA', 'MUQUIYAUYO'),
(106011320, 'JUNIN', 'JAUJA', 'PACA'),
(106111321, 'JUNIN', 'JAUJA', 'PACCHA'),
(106211322, 'JUNIN', 'JAUJA', 'PANCAN'),
(106311323, 'JUNIN', 'JAUJA', 'PARCO'),
(106411324, 'JUNIN', 'JAUJA', 'POMACANCHA'),
(106511325, 'JUNIN', 'JAUJA', 'RICRAN'),
(106611326, 'JUNIN', 'JAUJA', 'SAN LORENZO'),
(106711327, 'JUNIN', 'JAUJA', 'SAN PEDRO DE CHUNAN'),
(106811328, 'JUNIN', 'JAUJA', 'SINCOS'),
(106911329, 'JUNIN', 'JAUJA', 'TUNAN MARCA'),
(107011330, 'JUNIN', 'JAUJA', 'YAULI'),
(107111331, 'JUNIN', 'JAUJA', 'CURICACA'),
(107211332, 'JUNIN', 'JAUJA', 'MASMA CHICCHE'),
(107311333, 'JUNIN', 'JAUJA', 'SAUSA'),
(107411334, 'JUNIN', 'JAUJA', 'YAUYOS'),
(109711610, 'JUNIN', 'YAULI', 'STA ROSA DE SACCO'),
(112912110, 'LA LIBERTAD', 'TRUJILLO', 'EL PORVENIR'),
(113012111, 'LA LIBERTAD', 'TRUJILLO', 'LA ESPERANZA'),
(113112112, 'LA LIBERTAD', 'TRUJILLO', 'FLORENCIA DE MORA'),
(115312410, 'LA LIBERTAD', 'OTUZCO', 'SINSICAP'),
(115412411, 'LA LIBERTAD', 'OTUZCO', 'USQUIL'),
(115512413, 'LA LIBERTAD', 'OTUZCO', 'MACHE'),
(117012610, 'LA LIBERTAD', 'PATAZ', 'PIAS'),
(117112611, 'LA LIBERTAD', 'PATAZ', 'TAURIJA'),
(117212612, 'LA LIBERTAD', 'PATAZ', 'URPAY'),
(117312613, 'LA LIBERTAD', 'PATAZ', 'SANTIAGO DE CHALLAS'),
(119312101, 'LA LIBERTAD', 'JULCAN', 'JULCAN'),
(119412102, 'LA LIBERTAD', 'JULCAN', 'CARABAMBA'),
(119512103, 'LA LIBERTAD', 'JULCAN', 'CALAMARCA'),
(119612104, 'LA LIBERTAD', 'JULCAN', 'HUASO'),
(119712111, 'LA LIBERTAD', 'GRAN CHIMU', 'CASCAS'),
(119812112, 'LA LIBERTAD', 'GRAN CHIMU', 'LUCMA'),
(119912113, 'LA LIBERTAD', 'GRAN CHIMU', 'MARMOT'),
(120012114, 'LA LIBERTAD', 'GRAN CHIMU', 'SAYAPULLO'),
(120112121, 'LA LIBERTAD', 'VIRU', 'VIRU'),
(120212122, 'LA LIBERTAD', 'VIRU', 'CHAO'),
(120312123, 'LA LIBERTAD', 'VIRU', 'GUADALUPITO'),
(121313110, 'LAMBAYEQUE', 'CHICLAYO', 'PIMENTEL'),
(121413111, 'LAMBAYEQUE', 'CHICLAYO', 'REQUE'),
(121513112, 'LAMBAYEQUE', 'CHICLAYO', 'JOSE LEONARDO ORTIZ'),
(121613113, 'LAMBAYEQUE', 'CHICLAYO', 'SANTA ROSA'),
(121713114, 'LAMBAYEQUE', 'CHICLAYO', 'SAÑA'),
(121813115, 'LAMBAYEQUE', 'CHICLAYO', 'LA VICTORIA'),
(121913116, 'LAMBAYEQUE', 'CHICLAYO', 'CAYALTI'),
(122013117, 'LAMBAYEQUE', 'CHICLAYO', 'PATAPO'),
(122113118, 'LAMBAYEQUE', 'CHICLAYO', 'POMALCA'),
(122213119, 'LAMBAYEQUE', 'CHICLAYO', 'PUCALA'),
(122313120, 'LAMBAYEQUE', 'CHICLAYO', 'TUMAN'),
(123913310, 'LAMBAYEQUE', 'LAMBAYEQUE', 'SALAS'),
(124013311, 'LAMBAYEQUE', 'LAMBAYEQUE', 'SAN JOSE'),
(124113312, 'LAMBAYEQUE', 'LAMBAYEQUE', 'TUCUME'),
(125114110, 'LIMA', 'LIMA', 'LA MOLINA'),
(125214111, 'LIMA', 'LIMA', 'LINCE'),
(125314112, 'LIMA', 'LIMA', 'LURIGANCHO'),
(125414113, 'LIMA', 'LIMA', 'LURIN'),
(125514114, 'LIMA', 'LIMA', 'MAGDALENA DEL MAR'),
(125614115, 'LIMA', 'LIMA', 'MIRAFLORES'),
(125714116, 'LIMA', 'LIMA', 'PACHACAMAC'),
(125814117, 'LIMA', 'LIMA', 'PUEBLO LIBRE'),
(125914118, 'LIMA', 'LIMA', 'PUCUSANA'),
(126014119, 'LIMA', 'LIMA', 'PUENTE PIEDRA'),
(126114120, 'LIMA', 'LIMA', 'PUNTA HERMOSA'),
(126214121, 'LIMA', 'LIMA', 'PUNTA NEGRA'),
(126314122, 'LIMA', 'LIMA', 'RIMAC'),
(126414123, 'LIMA', 'LIMA', 'SAN BARTOLO'),
(126514124, 'LIMA', 'LIMA', 'SAN ISIDRO'),
(126614125, 'LIMA', 'LIMA', 'BARRANCO'),
(126714126, 'LIMA', 'LIMA', 'SAN MARTIN DE PORRES'),
(126814127, 'LIMA', 'LIMA', 'SAN MIGUEL'),
(126914128, 'LIMA', 'LIMA', 'STA MARIA DEL MAR'),
(127014129, 'LIMA', 'LIMA', 'SANTA ROSA'),
(127114130, 'LIMA', 'LIMA', 'SANTIAGO DE SURCO'),
(127214131, 'LIMA', 'LIMA', 'SURQUILLO'),
(127314132, 'LIMA', 'LIMA', 'VILLA MARIA DEL TRIUNFO'),
(127414133, 'LIMA', 'LIMA', 'JESUS MARIA'),
(127514134, 'LIMA', 'LIMA', 'INDEPENDENCIA'),
(127614135, 'LIMA', 'LIMA', 'EL AGUSTINO'),
(127714136, 'LIMA', 'LIMA', 'SAN JUAN DE MIRAFLORES'),
(127814137, 'LIMA', 'LIMA', 'SAN JUAN DE LURIGANCHO'),
(127914138, 'LIMA', 'LIMA', 'SAN LUIS'),
(128014139, 'LIMA', 'LIMA', 'CIENEGUILLA'),
(128114140, 'LIMA', 'LIMA', 'SAN BORJA'),
(128214141, 'LIMA', 'LIMA', 'VILLA EL SALVADOR'),
(128314142, 'LIMA', 'LIMA', 'LOS OLIVOS'),
(128414143, 'LIMA', 'LIMA', 'SANTA ANITA'),
(130614410, 'LIMA', 'CAÑETE', 'PACARAN'),
(130714411, 'LIMA', 'CAÑETE', 'QUILMANA'),
(130814412, 'LIMA', 'CAÑETE', 'SAN ANTONIO'),
(130914413, 'LIMA', 'CAÑETE', 'SAN LUIS'),
(131014414, 'LIMA', 'CAÑETE', 'SANTA CRUZ DE FLORES'),
(131114415, 'LIMA', 'CAÑETE', 'ZUÑIGA'),
(131214416, 'LIMA', 'CAÑETE', 'ASIA'),
(132114511, 'LIMA', 'HUAURA', 'SANTA LEONOR'),
(132214512, 'LIMA', 'HUAURA', 'SANTA MARIA'),
(132314513, 'LIMA', 'HUAURA', 'SAYAN'),
(132414516, 'LIMA', 'HUAURA', 'VEGUETA'),
(133414610, 'LIMA', 'HUAROCHIRI', 'LAHUAYTAMBO'),
(133514611, 'LIMA', 'HUAROCHIRI', 'LANGA'),
(133614612, 'LIMA', 'HUAROCHIRI', 'MARIATANA'),
(133714613, 'LIMA', 'HUAROCHIRI', 'RICARDO PALMA'),
(133814614, 'LIMA', 'HUAROCHIRI', 'SAN ANDRES DE TUPICOCHA'),
(133914615, 'LIMA', 'HUAROCHIRI', 'SAN ANTONIO'),
(134014616, 'LIMA', 'HUAROCHIRI', 'SAN BARTOLOME'),
(134114617, 'LIMA', 'HUAROCHIRI', 'SAN DAMIAN'),
(134214618, 'LIMA', 'HUAROCHIRI', 'SANGALLAYA'),
(134314619, 'LIMA', 'HUAROCHIRI', 'SAN JUAN DE TANTARANCHE'),
(134414620, 'LIMA', 'HUAROCHIRI', 'SAN LORENZO DE QUINTI'),
(134514621, 'LIMA', 'HUAROCHIRI', 'SAN MATEO'),
(134614622, 'LIMA', 'HUAROCHIRI', 'SAN MATEO DE OTAO'),
(134714623, 'LIMA', 'HUAROCHIRI', 'SAN PEDRO DE HUANCAYRE'),
(134814624, 'LIMA', 'HUAROCHIRI', 'SANTA CRUZ DE COCACHACRA'),
(134914625, 'LIMA', 'HUAROCHIRI', 'SANTA EULALIA'),
(135014626, 'LIMA', 'HUAROCHIRI', 'SANTIAGO DE ANCHUCAYA'),
(135114627, 'LIMA', 'HUAROCHIRI', 'SANTIAGO DE TUNA'),
(135214628, 'LIMA', 'HUAROCHIRI', 'SANTO DOMINGO DE LOS OLLEROS'),
(135314629, 'LIMA', 'HUAROCHIRI', 'SURCO'),
(135414630, 'LIMA', 'HUAROCHIRI', 'HUACHUPAMPA'),
(135514631, 'LIMA', 'HUAROCHIRI', 'LARAOS'),
(135614632, 'LIMA', 'HUAROCHIRI', 'SAN JUAN DE IRIS'),
(136614710, 'LIMA', 'YAUYOS', 'CHOCOS'),
(136714711, 'LIMA', 'YAUYOS', 'HUAMPARA'),
(136814712, 'LIMA', 'YAUYOS', 'HUANCAYA'),
(136914713, 'LIMA', 'YAUYOS', 'HUANGASCAR'),
(137014714, 'LIMA', 'YAUYOS', 'HUANTAN'),
(137114715, 'LIMA', 'YAUYOS', 'HUAÑEC'),
(137214716, 'LIMA', 'YAUYOS', 'LARAOS'),
(137314717, 'LIMA', 'YAUYOS', 'LINCHA'),
(137414718, 'LIMA', 'YAUYOS', 'MIRAFLORES'),
(137514719, 'LIMA', 'YAUYOS', 'OMAS'),
(137614720, 'LIMA', 'YAUYOS', 'QUINCHES'),
(137714721, 'LIMA', 'YAUYOS', 'QUINOCAY'),
(137814722, 'LIMA', 'YAUYOS', 'SAN JOAQUIN'),
(137914723, 'LIMA', 'YAUYOS', 'SAN PEDRO DE PILAS'),
(138014724, 'LIMA', 'YAUYOS', 'TANTA'),
(138114725, 'LIMA', 'YAUYOS', 'TAURIPAMPA'),
(138214726, 'LIMA', 'YAUYOS', 'TUPE'),
(138314727, 'LIMA', 'YAUYOS', 'TOMAS'),
(138414728, 'LIMA', 'YAUYOS', 'VIÑAC'),
(138514729, 'LIMA', 'YAUYOS', 'VITIS'),
(138614730, 'LIMA', 'YAUYOS', 'HONGOS'),
(138714731, 'LIMA', 'YAUYOS', 'MADEAN'),
(138814732, 'LIMA', 'YAUYOS', 'PUTINZA'),
(138914733, 'LIMA', 'YAUYOS', 'CATAHUASI'),
(139914810, 'LIMA', 'HUARAL', 'VEINTISIETE DE NOVIEMBRE'),
(140014811, 'LIMA', 'HUARAL', 'STA CRUZ DE ANDAMARCA'),
(140114812, 'LIMA', 'HUARAL', 'SUMBILCA'),
(140714101, 'LIMA', 'OYON', 'OYON'),
(140814102, 'LIMA', 'OYON', 'NAVAN'),
(140914103, 'LIMA', 'OYON', 'CAUJUL'),
(141014104, 'LIMA', 'OYON', 'ANDAJES'),
(141114105, 'LIMA', 'OYON', 'PACHANGARA'),
(141214106, 'LIMA', 'OYON', 'COCHAMARCA'),
(142115110, 'LORETO', 'MAYNAS', 'INDIANA'),
(142215111, 'LORETO', 'MAYNAS', 'PUNCHANA'),
(142315112, 'LORETO', 'MAYNAS', 'BELEN'),
(142415113, 'LORETO', 'MAYNAS', 'SAN JUAN BAUTISTA'),
(142515114, 'LORETO', 'MAYNAS', 'TNTE MANUEL CLAVERO'),
(143015210, 'LORETO', 'ALTO AMAZONAS', 'SANTA CRUZ'),
(143115211, 'LORETO', 'ALTO AMAZONAS', 'TNTE CESAR LOPEZ ROJAS'),
(144615410, 'LORETO', 'REQUENA', 'JENARO HERRERA'),
(144715411, 'LORETO', 'REQUENA', 'YAQUERANA'),
(149017210, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'UBINAS'),
(149117211, 'MOQUEGUA', 'GENERAL SANCHEZ CERRO', 'YUNGA'),
(150318110, 'PASCO', 'PASCO', 'SIMON BOLIVAR'),
(150418111, 'PASCO', 'PASCO', 'TICLACAYAN'),
(150518112, 'PASCO', 'PASCO', 'TINYAHUARCO'),
(150618113, 'PASCO', 'PASCO', 'VICCO'),
(150718114, 'PASCO', 'PASCO', 'YANACANCHA'),
(153019113, 'PIURA', 'PIURA', 'CURA MORI'),
(153119114, 'PIURA', 'PIURA', 'EL TALLAN'),
(154119210, 'PIURA', 'AYABACA', 'PAIMAS'),
(155919410, 'PIURA', 'MORROPON', 'SAN JUAN DE BIGOTE'),
(159620110, 'PUNO', 'PUNO', 'PICHACANI'),
(159720111, 'PUNO', 'PUNO', 'SAN ANTONIO'),
(159820112, 'PUNO', 'PUNO', 'TIQUILLACA'),
(159920113, 'PUNO', 'PUNO', 'VILQUE'),
(160020114, 'PUNO', 'PUNO', 'PLATERIA'),
(160120115, 'PUNO', 'PUNO', 'AMANTANI'),
(161020210, 'PUNO', 'AZANGARO', 'POTONI'),
(161120212, 'PUNO', 'AZANGARO', 'SAMAN'),
(161220213, 'PUNO', 'AZANGARO', 'SAN ANTON'),
(161320214, 'PUNO', 'AZANGARO', 'SAN JOSE'),
(161420215, 'PUNO', 'AZANGARO', 'SAN JUAN DE SALINAS'),
(161520216, 'PUNO', 'AZANGARO', 'STGO DE PUPUJA'),
(161620217, 'PUNO', 'AZANGARO', 'TIRAPATA'),
(162620310, 'PUNO', 'CARABAYA', 'USICAYOS'),
(163220410, 'PUNO', 'CHUCUITO', 'ZEPITA'),
(163320412, 'PUNO', 'CHUCUITO', 'KELLUYO'),
(164120511, 'PUNO', 'HUANCANE', 'HUATASANI'),
(165120610, 'PUNO', 'LAMPA', 'VILAVILA'),
(166820810, 'PUNO', 'SANDIA', 'YANAHUAYA'),
(166920811, 'PUNO', 'SANDIA', 'ALTO INAMBARI'),
(167020812, 'PUNO', 'SANDIA', 'SAN PEDRO DE PUTINA PUNCO'),
(167520101, 'PUNO', 'YUNGUYO', 'YUNGUYO'),
(167620102, 'PUNO', 'YUNGUYO', 'UNICACHI'),
(167720103, 'PUNO', 'YUNGUYO', 'ANAPIA'),
(167820104, 'PUNO', 'YUNGUYO', 'COPANI'),
(167920105, 'PUNO', 'YUNGUYO', 'CUTURAPI'),
(168020106, 'PUNO', 'YUNGUYO', 'OLLARAYA'),
(168120107, 'PUNO', 'YUNGUYO', 'TINICACHI'),
(168220111, 'PUNO', 'SAN ANTONIO DE PUTINA', 'PUTINA'),
(168320112, 'PUNO', 'SAN ANTONIO DE PUTINA', 'PEDRO VILCA APAZA'),
(168420113, 'PUNO', 'SAN ANTONIO DE PUTINA', 'QUILCA PUNCU'),
(168520114, 'PUNO', 'SAN ANTONIO DE PUTINA', 'ANANEA'),
(168620115, 'PUNO', 'SAN ANTONIO DE PUTINA', 'SINA'),
(168720121, 'PUNO', 'EL COLLAO', 'ILAVE'),
(168820122, 'PUNO', 'EL COLLAO', 'PILCUYO'),
(168920123, 'PUNO', 'EL COLLAO', 'SANTA ROSA'),
(169020124, 'PUNO', 'EL COLLAO', 'CAPASO'),
(169120125, 'PUNO', 'EL COLLAO', 'CONDURIRI'),
(169220131, 'PUNO', 'MOHO', 'MOHO'),
(169320132, 'PUNO', 'MOHO', 'CONIMA'),
(169420133, 'PUNO', 'MOHO', 'TILALI'),
(169520134, 'PUNO', 'MOHO', 'HUAYRAPATA'),
(171421311, 'SAN MARTIN', 'LAMAS', 'SHANAO'),
(171521313, 'SAN MARTIN', 'LAMAS', 'TABALOSOS'),
(171621314, 'SAN MARTIN', 'LAMAS', 'ZAPATERO'),
(171721315, 'SAN MARTIN', 'LAMAS', 'ALONSO DE ALVARADO'),
(171821316, 'SAN MARTIN', 'LAMAS', 'SAN ROQUE DE CUMBAZA'),
(174021610, 'SAN MARTIN', 'SAN MARTIN', 'JUAN GUERRA'),
(174121611, 'SAN MARTIN', 'SAN MARTIN', 'MORALES'),
(174221612, 'SAN MARTIN', 'SAN MARTIN', 'PAPAPLAYA'),
(174321616, 'SAN MARTIN', 'SAN MARTIN', 'SAN ANTONIO'),
(174421619, 'SAN MARTIN', 'SAN MARTIN', 'SAUCE'),
(174521620, 'SAN MARTIN', 'SAN MARTIN', 'SHAPAJA'),
(174621621, 'SAN MARTIN', 'SAN MARTIN', 'LA BANDA DE SHILCAYO'),
(176721910, 'SAN MARTIN', 'PICOTA', 'SHAMBOYACU'),
(176821101, 'SAN MARTIN', 'EL DORADO', 'SAN JOSE DE SISA'),
(176921102, 'SAN MARTIN', 'EL DORADO', 'AGUA BLANCA'),
(177021103, 'SAN MARTIN', 'EL DORADO', 'SHATOJA'),
(177121104, 'SAN MARTIN', 'EL DORADO', 'SAN MARTIN'),
(177221105, 'SAN MARTIN', 'EL DORADO', 'SANTA ROSA'),
(177922110, 'TACNA', 'TACNA', 'SAMA'),
(178022111, 'TACNA', 'TACNA', 'ALTO DE LA ALIANZA'),
(178122112, 'TACNA', 'TACNA', 'CIUDAD NUEVA'),
(178222113, 'TACNA', 'TACNA', 'CORONEL GREGORIO ALBARRACIN L.'),
(178722210, 'TACNA', 'TARATA', 'SITAJARA'),
(178822211, 'TACNA', 'TARATA', 'SUSAPAYA'),
(178922212, 'TACNA', 'TARATA', 'TARUCACHI'),
(179022213, 'TACNA', 'TARATA', 'TICACO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit_type_id` int(10) unsigned NOT NULL,
  `value` decimal(15,4) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `units`
--

INSERT INTO `units` (`id`, `name`, `symbol`, `unit_type_id`, `value`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CENTIMETRO', 'cm', 1, '1.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(2, 'METRO', 'mt', 1, '100.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(3, 'KILOMETRO', 'km', 1, '100000.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(4, 'PULGADA', 'pulg', 1, '2.5400', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(5, 'PIE', 'pie', 1, '30.4800', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(6, 'YARDA', 'yar', 1, '91.4400', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(7, 'MILLA', 'milla', 1, '160934.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(8, 'MILILITRO', 'ml', 2, '1.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(9, 'LITRO', 'lt', 2, '1000.0000', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(10, 'METRO CUBICO', 'm3', 2, '1000000.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(11, 'PULGADA CUBICA', 'pulg3', 2, '16.3871', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(12, 'PIE CUBICO', 'pie3', 2, '28317.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(13, 'GALON', 'gal', 2, '3785.4000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(14, 'GRAMO', 'gr', 3, '1.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(15, 'KILOGRAMO', 'kg', 3, '1000.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(16, 'TONELADA', 'ton', 3, '1000000.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(17, 'ONZA', 'oz', 3, '28.3490', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(18, 'LIBRA', 'lb', 3, '453.5900', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(19, 'UNIDAD', 'und', 4, '1.0000', '', '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unit_types`
--

CREATE TABLE IF NOT EXISTS `unit_types` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `unit_types`
--

INSERT INTO `unit_types` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'LONGITUD', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(2, 'VOLUMEN', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(3, 'MASA', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL),
(4, 'UNIDAD', '', '2015-07-17 07:32:50', '2015-07-17 07:32:50', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `is_superuser`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Noel', 'noel.logan@gmail.com', '$2y$10$zv5DnbcDgkI0JrW2exzy8.ogrwp6SdQqKgKJYL2ayF4PQuVLstphC', 'Ifv9tUx95hMDM2S5eLp6yfoAiTthAsc6EowZghBMsCG0s43SVIJvPdy3EcFY', 1, '2015-07-17 07:32:45', '2015-10-16 06:34:58', NULL),
(2, 'Juana', 'jlara@masaki.com.pe', '$2y$10$n8rQ//HmStkHgeS7USPBxee1ua/WdhrSyUz12Uf/S9aJTKOyZOQS2', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:24', NULL),
(3, 'Estuardo', 'etataje@masaki.com.pe', '$2y$10$QtkkUMRofkK6EBa3IK1GMusgrIBJiSztIa5lazf5k4coczwDN7lD.', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:31', NULL),
(4, 'Marian', 'mderteano@masaki.com.pe', '$2y$10$Oj.u/GgUoBOPWkXnSzg/aOXD9Ms8pANBA/QWI6FYRIH3zLp1eYtXi', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:38', NULL),
(5, 'Pedro', 'prequena@masaki.com.pe', '$2y$10$3E9PFpLuRalVie0OqNHpLOP/MR4SQM.L4h8/slx2jZCQouAAkfunS', 'QQXVnLhbfsio8zXhwbYtjxSQo6nS1TBa7nm9JjJsr1LWsUrqjU3W3CJjyUxn', 0, '2015-07-17 07:32:46', '2015-09-23 16:11:50', NULL),
(6, 'Pamela', 'pguerrero@masaki.com.pe', '$2y$10$YKEGNcEt6Qp0zH/sq4ys3.ROfFjeOaLnx7WcrvIa4rxC5rC6SZ13W', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:50', NULL),
(7, 'Eduardo', 'ecastellano@masaki.com.pe', '$2y$10$bkGIdwzaCDP260GV8woiZOKB7z8oWkPZkjUjWus0MZ5VljgUDrPGq', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:56', NULL),
(8, 'Marco', 'mgeller@masaki.com.pe', '$2y$10$yQn9YBl9MvPgLo12ZF7CG.7fYbwk.zyWEHNLGB2IMEK3myRiiBCq2', 'jT2TuISw9YKEZjnAzNoOVlvdd1aBEkAPmvFakcuGPh5OICLdLtx7zFeXR6Hc', 0, '2015-07-17 07:32:46', '2015-09-04 15:25:29', NULL),
(9, 'Melissa', 'mmendoza@masaki.com.pe', '$2y$10$/j.nZbFcbtuU0UN6SC0DcurBfmXOwvn5XRN/bth6m/g01dncyMhwC', NULL, 0, '2015-07-17 07:32:46', '2015-07-17 07:39:02', NULL),
(10, 'Lung', 'alung@masaki.com.pe', '$2y$10$kTDOMPk0dOAELlswkwN4vOs3c3LBBXOpQDbt0tJb7/bcBgZcGQzOK', NULL, 0, '2015-07-17 07:32:46', '2015-09-14 15:25:29', NULL),
(11, 'WILLIAM FREDY YAUYO PAREDES', 'controlador@masaki.com.pe', '$2y$10$v.PhaQzvgxI6t6oxzgK9YeQnGxU9vOYUaPaXZ2A7TwE4BHv0txNy6', 'mQlNsVkxORbuISBIc36qNSJk7nm3A8ZoZs92y4RTpDLMuuCfk9q8KtNzwoyb', 0, '2015-07-17 19:06:24', '2015-09-23 16:38:26', NULL),
(12, 'EDWIN HANS RIVAS', 'edu_rivad@outlook.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', 'BGNl87UFgA8zrsLV36frLcLupL8eLRcCOmMqXCX68OtRxQPVW6GkgH8kjXYo', 0, '2015-09-14 15:24:57', '2015-09-25 16:24:10', NULL),
(13, 'Henrry Edwar Aguilar', 'aguilar_henryy88@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:03:36', NULL),
(14, 'Luis Enrique Berrú', 'luisthekin88@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:03:16', NULL),
(15, 'Carlos Marca Huasco', 'carlosmarca13_1990@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:02:57', NULL),
(16, 'Carlos Alberto Vergara Sanchez', 'carvrhonda@gmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:02:29', NULL),
(17, 'Edgar Iván Rodriguez Marchán', 'garymarchan78@gmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:02:19', NULL),
(18, 'Osibes Edison Alarcón', 'osibes2015@gmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:02:10', NULL),
(19, 'Jhony Pablo Lagos', 'jplagosl@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', '4p8VnF0EEyZzveM7szdLVFVqfsOsTUaHeureEtp5z5Iurv4mxS9WbUhvPAVE', 0, '2015-09-14 20:24:57', '2015-10-16 08:47:34', NULL),
(20, 'Christian Franco Capcha', 'franck_22592@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:01:42', NULL),
(21, 'Jedy Anthony Chacca', 'jedy_2012@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:00:14', NULL),
(22, 'Teofilo Huamani', 'teohuamanihonda75@gmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:59:51', NULL),
(23, 'Victor Hugo Chamorro', 'v.h.chamorro@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:59:32', NULL),
(24, 'Eusebio Paitan', 'epaitanq@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:59:17', NULL),
(25, 'Wilmer Christian Huamani', 'wilmer_rst@hotmail.com', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:59:08', NULL),
(26, 'Henry Manuel Elias', 'helias@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', 'CwES9GmYDahWANHeufTp9UOrJgF1CjD8iVOg1qOgqIPQeOZika5IRneAzG6l', 0, '2015-09-14 20:24:57', '2015-10-16 09:03:03', NULL),
(27, 'Rubén Chumpitaz', 'rchumpitaz@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:57:55', NULL),
(28, 'Isidro Ernesto Perla', 'iperla@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:57:47', NULL),
(29, 'Miguel Chacón', 'mchacon@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:57:32', NULL),
(30, 'Javier Rodolfo Polo', 'jpolo@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', 'EVDQNXVy9f9rKEbSNHgK5BFbjWbYBT87ryGBcAzV3c18Fs3y9pAD3FfzPGuU', 0, '2015-09-14 20:24:57', '2015-10-10 00:47:19', NULL),
(31, 'David Gersin Monzon', 'dmonzon@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:49:45', NULL),
(32, 'Jose Luis Espinoza', 'jespinoza@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:49:32', NULL),
(33, 'Ignacio Estrada', 'iestrada@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:48:56', NULL),
(34, 'Anabel Huillca', 'citas@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:48:27', NULL),
(35, 'Inés Pérez', 'iperez@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 16:00:29', NULL),
(36, 'Patricia De La Piniella', 'pdelapiniella@masaki.com.pe', '$2y$10$oe/kHOdyy.PdRY0WlJOwv.OpfAenme6bGL3Tzo3ey/5oGdcNiO4WG', NULL, 0, '2015-09-14 20:24:57', '2015-09-14 15:48:05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 9, 7, '2015-07-17 07:39:02', '2015-07-17 07:39:02'),
(2, 8, 7, '2015-07-17 07:39:08', '2015-07-17 07:39:08'),
(3, 2, 7, '2015-07-17 07:39:18', '2015-07-17 07:39:18'),
(4, 3, 7, '2015-07-17 07:39:31', '2015-07-17 07:39:31'),
(5, 4, 7, '2015-07-17 07:39:38', '2015-07-17 07:39:38'),
(6, 5, 8, '2015-07-17 07:39:44', '2015-07-17 07:39:44'),
(7, 6, 7, '2015-07-17 07:39:50', '2015-07-17 07:39:50'),
(8, 7, 7, '2015-07-17 07:39:56', '2015-07-17 07:39:56'),
(9, 8, 6, '2015-07-17 07:40:29', '2015-07-17 07:40:29'),
(10, 11, 13, '2015-07-17 19:06:24', '2015-07-17 19:06:24'),
(11, 12, 14, '2015-09-14 15:24:57', '2015-09-14 15:24:57'),
(12, 36, 11, '2015-09-14 15:48:05', '2015-09-14 15:48:05'),
(13, 35, 11, '2015-09-14 15:48:18', '2015-09-14 15:48:18'),
(14, 34, 11, '2015-09-14 15:48:27', '2015-09-14 15:48:27'),
(15, 33, 13, '2015-09-14 15:48:56', '2015-09-14 15:48:56'),
(16, 32, 10, '2015-09-14 15:49:33', '2015-09-14 15:49:33'),
(17, 31, 10, '2015-09-14 15:49:45', '2015-09-14 15:49:45'),
(18, 30, 10, '2015-09-14 15:57:01', '2015-09-14 15:57:01'),
(19, 29, 10, '2015-09-14 15:57:32', '2015-09-14 15:57:32'),
(20, 28, 10, '2015-09-14 15:57:47', '2015-09-14 15:57:47'),
(21, 27, 10, '2015-09-14 15:57:55', '2015-09-14 15:57:55'),
(22, 26, 22, '2015-09-14 15:58:44', '2015-09-14 15:58:44'),
(23, 25, 14, '2015-09-14 15:59:08', '2015-09-14 15:59:08'),
(24, 24, 14, '2015-09-14 15:59:18', '2015-09-14 15:59:18'),
(25, 23, 21, '2015-09-14 15:59:32', '2015-09-14 15:59:32'),
(26, 22, 14, '2015-09-14 15:59:51', '2015-09-14 15:59:51'),
(27, 21, 14, '2015-09-14 16:00:14', '2015-09-14 16:00:14'),
(29, 20, 14, '2015-09-14 16:01:42', '2015-09-14 16:01:42'),
(30, 19, 14, '2015-09-14 16:01:59', '2015-09-14 16:01:59'),
(31, 18, 14, '2015-09-14 16:02:10', '2015-09-14 16:02:10'),
(32, 17, 14, '2015-09-14 16:02:19', '2015-09-14 16:02:19'),
(33, 16, 14, '2015-09-14 16:02:29', '2015-09-14 16:02:29'),
(34, 15, 14, '2015-09-14 16:02:57', '2015-09-14 16:02:57'),
(35, 14, 14, '2015-09-14 16:03:16', '2015-09-14 16:03:16'),
(36, 13, 14, '2015-09-14 16:03:36', '2015-09-14 16:03:36'),
(37, 1, 1, '2015-09-14 18:04:20', '2015-09-14 18:04:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `versions`
--

CREATE TABLE IF NOT EXISTS `versions` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `modelo_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `versions`
--

INSERT INTO `versions` (`id`, `name`, `description`, `modelo_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'LX MT', '', 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(2, 'LX AT', '', 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(3, 'EX AT', '', 1, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(4, 'LX AT', '', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(5, 'DE LUXE', '', 2, '2015-07-17 07:32:53', '2015-07-17 07:32:53', NULL),
(6, 'SI 2.4 MT', '', 2, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(7, 'EX AT', '', 3, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(8, 'SI 2.4 MT', '', 3, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(9, 'EX AT', '', 4, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(10, 'EXL AT', '', 4, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(11, 'V6 AT', '', 4, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(12, 'V6 AT', '', 5, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(13, 'LX CVT 2WD Up-Grade', '', 6, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(14, 'LX CVT 2WD Up-Grade', '', 6, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(15, 'DE LUXE', '', 6, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(16, 'TOURING', '', 6, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(17, 'LX Up-Grade', '', 7, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(18, 'EXL Up-Grade', '', 7, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(19, 'TOURING', '', 7, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(20, 'EXL', '', 8, '2015-07-17 07:32:54', '2015-07-17 07:32:54', NULL),
(21, 'LX', '', 7, '2015-09-04 15:26:32', '2015-09-04 15:26:32', NULL),
(22, 'EXL', '', 7, '2015-09-04 15:26:44', '2015-09-04 15:26:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `warehouses`
--

CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ubigeo_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `address`, `ubigeo_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ALMACEN JAVIER PRADO', 'AV.JAVIER PRADO ESTE 5446', 125114110, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(2, 'ALMACEN ATE', 'AV. SEPARADORA INDUSTRIAL 781 ATE', 12441413, '2015-07-17 07:32:51', '2015-07-17 07:32:51', NULL),
(3, 'ALMACEN CUAS', 'AV.JAVIER PRADO ESTE 5446', 125114110, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(4, 'LABORATORIO 1 LA MOLINA', 'AV.JAVIER PRADO ESTE 5446', 125114110, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(5, 'LABORATORIO 2 ATE', 'AV. SEPARADORA INDUSTRIAL 781 ATE', 12441413, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(6, 'JAD TRANSPORTES Y CONSTRUCCIONES SAC', 'CALLE LAMBDA 205 CALLAO, ALTURA CUADRA 50 Y 51 DE LA COLONIAL', 18132411, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL),
(7, 'ALMACEN ALTERNATIVO', 'AV. SEPARADORA INDUSTRIAL 781 ATE', 125114110, '2015-07-17 07:32:52', '2015-07-17 07:32:52', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `car_quotes`
--
ALTER TABLE `car_quotes`
  ADD PRIMARY KEY (`id`), ADD KEY `car_quotes_catalog_car_id_foreign` (`catalog_car_id`), ADD KEY `car_quotes_company_id_foreign` (`company_id`), ADD KEY `car_quotes_currency_id_foreign` (`currency_id`), ADD KEY `car_quotes_employee_id_foreign` (`employee_id`);

--
-- Indices de la tabla `catalog_cars`
--
ALTER TABLE `catalog_cars`
  ADD PRIMARY KEY (`id`), ADD KEY `catalog_cars_version_id_foreign` (`version_id`), ADD KEY `catalog_cars_currency_id_foreign` (`currency_id`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`), ADD KEY `companies_id_type_id_foreign` (`id_type_id`), ADD KEY `companies_ubigeo_id_foreign` (`ubigeo_id`);

--
-- Indices de la tabla `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`), ADD KEY `employees_job_id_foreign` (`job_id`), ADD KEY `employees_id_type_id_foreign` (`id_type_id`), ADD KEY `employees_ubigeo_id_foreign` (`ubigeo_id`), ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `exchanges`
--
ALTER TABLE `exchanges`
  ADD PRIMARY KEY (`id`), ADD KEY `exchanges_currency_id_foreign` (`currency_id`);

--
-- Indices de la tabla `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`), ADD KEY `features_feature_group_id_foreign` (`feature_group_id`), ADD KEY `features_catalog_car_id_foreign` (`catalog_car_id`);

--
-- Indices de la tabla `feature_groups`
--
ALTER TABLE `feature_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `id_types`
--
ALTER TABLE `id_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`id`), ADD KEY `modelos_brand_id_foreign` (`brand_id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`), ADD KEY `password_resets_token_index` (`token`);

--
-- Indices de la tabla `payment_conditions`
--
ALTER TABLE `payment_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`), ADD KEY `permissions_permission_group_id_foreign` (`permission_group_id`);

--
-- Indices de la tabla `permission_groups`
--
ALTER TABLE `permission_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`), ADD KEY `products_currency_id_foreign` (`currency_id`), ADD KEY `products_brand_id_foreign` (`brand_id`), ADD KEY `products_sub_category_id_foreign` (`sub_category_id`), ADD KEY `products_unit_id_foreign` (`unit_id`);

--
-- Indices de la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`), ADD KEY `purchases_document_type_id_foreign` (`document_type_id`), ADD KEY `purchases_company_id_foreign` (`company_id`), ADD KEY `purchases_payment_condition_id_foreign` (`payment_condition_id`), ADD KEY `purchases_currency_id_foreign` (`currency_id`);

--
-- Indices de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`), ADD KEY `purchase_details_purchase_id_foreign` (`purchase_id`), ADD KEY `purchase_details_stock_id_foreign` (`stock_id`), ADD KEY `purchase_details_unit_id_foreign` (`unit_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `service_checklists`
--
ALTER TABLE `service_checklists`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `service_checklist_checkitem`
--
ALTER TABLE `service_checklist_checkitem`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`), ADD KEY `stocks_warehouse_id_foreign` (`warehouse_id`), ADD KEY `stocks_product_id_foreign` (`product_id`), ADD KEY `stocks_currency_id_foreign` (`currency_id`);

--
-- Indices de la tabla `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`), ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indices de la tabla `ubigeos`
--
ALTER TABLE `ubigeos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`), ADD KEY `units_unit_type_id_foreign` (`unit_type_id`);

--
-- Indices de la tabla `unit_types`
--
ALTER TABLE `unit_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `versions`
--
ALTER TABLE `versions`
  ADD PRIMARY KEY (`id`), ADD KEY `versions_modelo_id_foreign` (`modelo_id`);

--
-- Indices de la tabla `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`), ADD KEY `warehouses_ubigeo_id_foreign` (`ubigeo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT de la tabla `car_quotes`
--
ALTER TABLE `car_quotes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT de la tabla `catalog_cars`
--
ALTER TABLE `catalog_cars`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `checkitems`
--
ALTER TABLE `checkitems`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT de la tabla `checkitem_groups`
--
ALTER TABLE `checkitem_groups`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `colors`
--
ALTER TABLE `colors`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=127;
--
-- AUTO_INCREMENT de la tabla `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT de la tabla `exchanges`
--
ALTER TABLE `exchanges`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `features`
--
ALTER TABLE `features`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=739;
--
-- AUTO_INCREMENT de la tabla `feature_groups`
--
ALTER TABLE `feature_groups`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `id_types`
--
ALTER TABLE `id_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `payment_conditions`
--
ALTER TABLE `payment_conditions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=71;
--
-- AUTO_INCREMENT de la tabla `permission_groups`
--
ALTER TABLE `permission_groups`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=139;
--
-- AUTO_INCREMENT de la tabla `service_checklists`
--
ALTER TABLE `service_checklists`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `service_checklist_checkitem`
--
ALTER TABLE `service_checklist_checkitem`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=220;
--
-- AUTO_INCREMENT de la tabla `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT de la tabla `ubigeos`
--
ALTER TABLE `ubigeos`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=179022214;
--
-- AUTO_INCREMENT de la tabla `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `unit_types`
--
ALTER TABLE `unit_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT de la tabla `versions`
--
ALTER TABLE `versions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de la tabla `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `car_quotes`
--
ALTER TABLE `car_quotes`
ADD CONSTRAINT `car_quotes_catalog_car_id_foreign` FOREIGN KEY (`catalog_car_id`) REFERENCES `catalog_cars` (`id`),
ADD CONSTRAINT `car_quotes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
ADD CONSTRAINT `car_quotes_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
ADD CONSTRAINT `car_quotes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Filtros para la tabla `catalog_cars`
--
ALTER TABLE `catalog_cars`
ADD CONSTRAINT `catalog_cars_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
ADD CONSTRAINT `catalog_cars_version_id_foreign` FOREIGN KEY (`version_id`) REFERENCES `versions` (`id`);

--
-- Filtros para la tabla `checkitems`
--
ALTER TABLE `checkitems`
ADD CONSTRAINT `checkitems_checkitem_group_id_foreign` FOREIGN KEY (`checkitem_group_id`) REFERENCES `checkitem_groups` (`id`);

--
-- Filtros para la tabla `companies`
--
ALTER TABLE `companies`
ADD CONSTRAINT `companies_id_type_id_foreign` FOREIGN KEY (`id_type_id`) REFERENCES `id_types` (`id`),
ADD CONSTRAINT `companies_ubigeo_id_foreign` FOREIGN KEY (`ubigeo_id`) REFERENCES `ubigeos` (`id`);

--
-- Filtros para la tabla `employees`
--
ALTER TABLE `employees`
ADD CONSTRAINT `employees_id_type_id_foreign` FOREIGN KEY (`id_type_id`) REFERENCES `id_types` (`id`),
ADD CONSTRAINT `employees_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`),
ADD CONSTRAINT `employees_ubigeo_id_foreign` FOREIGN KEY (`ubigeo_id`) REFERENCES `ubigeos` (`id`),
ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `exchanges`
--
ALTER TABLE `exchanges`
ADD CONSTRAINT `exchanges_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Filtros para la tabla `features`
--
ALTER TABLE `features`
ADD CONSTRAINT `features_catalog_car_id_foreign` FOREIGN KEY (`catalog_car_id`) REFERENCES `catalog_cars` (`id`),
ADD CONSTRAINT `features_feature_group_id_foreign` FOREIGN KEY (`feature_group_id`) REFERENCES `feature_groups` (`id`);

--
-- Filtros para la tabla `modelos`
--
ALTER TABLE `modelos`
ADD CONSTRAINT `modelos_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
ADD CONSTRAINT `permissions_permission_group_id_foreign` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_groups` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
ADD CONSTRAINT `products_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
ADD CONSTRAINT `products_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`),
ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Filtros para la tabla `purchases`
--
ALTER TABLE `purchases`
ADD CONSTRAINT `purchases_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
ADD CONSTRAINT `purchases_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
ADD CONSTRAINT `purchases_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`),
ADD CONSTRAINT `purchases_payment_condition_id_foreign` FOREIGN KEY (`payment_condition_id`) REFERENCES `payment_conditions` (`id`);

--
-- Filtros para la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
ADD CONSTRAINT `purchase_details_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`),
ADD CONSTRAINT `purchase_details_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`),
ADD CONSTRAINT `purchase_details_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);

--
-- Filtros para la tabla `stocks`
--
ALTER TABLE `stocks`
ADD CONSTRAINT `stocks_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
ADD CONSTRAINT `stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
ADD CONSTRAINT `stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Filtros para la tabla `sub_categories`
--
ALTER TABLE `sub_categories`
ADD CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `units`
--
ALTER TABLE `units`
ADD CONSTRAINT `units_unit_type_id_foreign` FOREIGN KEY (`unit_type_id`) REFERENCES `unit_types` (`id`);

--
-- Filtros para la tabla `versions`
--
ALTER TABLE `versions`
ADD CONSTRAINT `versions_modelo_id_foreign` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`);

--
-- Filtros para la tabla `warehouses`
--
ALTER TABLE `warehouses`
ADD CONSTRAINT `warehouses_ubigeo_id_foreign` FOREIGN KEY (`ubigeo_id`) REFERENCES `ubigeos` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
