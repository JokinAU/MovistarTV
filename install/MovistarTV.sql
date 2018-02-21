-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-02-2018 a las 18:34:17
-- Versión del servidor: 5.7.21-0ubuntu0.17.10.1
-- Versión de PHP: 7.1.11-0ubuntu0.17.10.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `MovistarTV`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Geo`
--

CREATE TABLE `Geo` (
  `ID` smallint(5) UNSIGNED NOT NULL,
  `City` text COLLATE utf8_spanish_ci NOT NULL,
  `State` text COLLATE utf8_spanish_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Nodes`
--

CREATE TABLE `Nodes` (
  `ID` smallint(5) UNSIGNED NOT NULL,
  `IP` text NOT NULL,
  `Hostname` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `Port` smallint(5) UNSIGNED NOT NULL DEFAULT '5000',
  `City` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `State` smallint(6) UNSIGNED DEFAULT NULL,
  `Added` datetime NOT NULL,
  `LastGood` datetime DEFAULT NULL,
  `LastCode` smallint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Geo`
--
ALTER TABLE `Geo`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Nodes`
--
ALTER TABLE `Nodes`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Geo`
--
ALTER TABLE `Geo`
  MODIFY `ID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `Nodes`
--
ALTER TABLE `Nodes`
  MODIFY `ID` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
