-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 08-02-2026 a las 18:37:04
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
-- Base de datos: `curriculum_php`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cvs`
--

CREATE TABLE `cvs` (
  `id` int NOT NULL,
  `version` int NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubicacion` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sobre_mi` text COLLATE utf8mb4_unicode_ci,
  `experiencia` text COLLATE utf8mb4_unicode_ci,
  `formacion` text COLLATE utf8mb4_unicode_ci,
  `habilidades` text COLLATE utf8mb4_unicode_ci,
  `idiomas` text COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cvs`
--

INSERT INTO `cvs` (`id`, `version`, `nombre`, `email`, `telefono`, `ubicacion`, `sobre_mi`, `experiencia`, `formacion`, `habilidades`, `idiomas`) VALUES
(6, 1, 'Antonio Manuel', 'antonio.serrano.barrrera03@gmail.com', '674801076', 'Sevilla', 'gyuu', 'yuyuytutyut', 'fththfththfth', 'HTML', 'fghfhfghfgh'),
(7, 3, 'Antonio Manuel', 'antonio.serrano.barrera03@gmail.com', '674801076', 'Sevilla', 'dsfsdfsdfsdf', 'dsfsdf', 'sdfsdfdsgggggggggggggggg', 'HTML', 'asdasd'),
(5, 2, 'Antonio Manuel', 'antonio.serrano.barrera03@gmail.com', '674801076', 'Sevilla', 'dsfsdfsdfsdf', 'dsfsdf', 'sdfsdfds', 'HTML', 'asdasd');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_version` (`email`,`version`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cvs`
--
ALTER TABLE `cvs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
