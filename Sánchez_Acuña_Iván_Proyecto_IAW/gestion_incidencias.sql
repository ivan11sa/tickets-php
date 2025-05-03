-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-03-2025 a las 10:43:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `GESTION_INCIDENCIAS`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `COMENTARIOS`
--

CREATE TABLE `COMENTARIOS` (
  `ID_COMENTARIO` int(5) NOT NULL,
  `TEXTO` text NOT NULL,
  `FECHA_CREACION` datetime NOT NULL DEFAULT current_timestamp(),
  `ID_INCIDENCIA` int(5) NOT NULL,
  `ID` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `COMENTARIOS`
--

INSERT INTO `COMENTARIOS` (`ID_COMENTARIO`, `TEXTO`, `FECHA_CREACION`, `ID_INCIDENCIA`, `ID`) VALUES
(53, 'Tengo corregido hasta el apellido ''M''', '2025-02-22 14:44:58', 61, 1),
(54, 'Tengo corregido hasta el apellido ''S''', '2025-02-22 14:46:18', 61, 1),
(55, 'Hora de entregarlo', '2025-02-22 16:36:50', 54, 4),
(56, 'Vigilar que lleven los proyectos al día', '2025-03-03 10:39:56', 69, 1),
(57, 'Dificultades preguntadas', '2025-03-03 10:40:07', 69, 1),
(58, 'Se ha realizado el 40% de los convenios', '2025-03-03 10:40:24', 70, 1),
(59, 'Scripts elaborado a falta de revisión', '2025-03-03 10:42:14', 71, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INCIDENCIAS`
--

CREATE TABLE `INCIDENCIAS` (
  `ID_INCIDENCIA` int(5) NOT NULL,
  `TITULO` varchar(200) NOT NULL,
  `DESCRIPCION` text NOT NULL,
  `FECHA_CREACION` datetime NOT NULL DEFAULT current_timestamp(),
  `NIVEL_PRIORIDAD` enum('Baja','Media','Alta','Urgente') NOT NULL,
  `ESTADO` enum('Abierta','Cerrada') NOT NULL DEFAULT 'Abierta',
  `ID_PROVINCIA` int(5) NOT NULL,
  `ID` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INCIDENCIAS`
--

INSERT INTO `INCIDENCIAS` (`ID_INCIDENCIA`, `TITULO`, `DESCRIPCION`, `FECHA_CREACION`, `NIVEL_PRIORIDAD`, `ESTADO`, `ID_PROVINCIA`, `ID`) VALUES
(54, 'Proyecto de PHP.', 'Proyecto de gestión de incidencias en PHP', '2025-02-22 11:58:24', 'Urgente', 'Cerrada', 8, 4),
(60, 'Examen ASO', 'Examen para el 6 de marzo', '2025-02-22 12:27:00', 'Media', 'Cerrada', 4, 4),
(61, 'Corregir examenes', 'Corregir los exámenes de SRI.', '2025-02-22 13:00:38', 'Media', 'Cerrada', 1, 1),
(62, 'Trabajo de ASO', 'Realizar trabajo de scripts de ASO', '2025-02-22 13:02:47', 'Media', 'Cerrada', 2, 6),
(63, 'Simulación en VirtualBox', 'Realizar red local en VirtualBox', '2025-02-22 13:09:12', 'Baja', 'Cerrada', 5, 7),
(67, 'Entrega proyecto final', 'Entrega proyecto final de ASIR', '2025-03-03 10:37:37', 'Baja', 'Abierta', 3, 4),
(68, 'Creación de consultas SQL', 'Tabla creada de base de datos', '2025-03-03 10:38:08', 'Media', 'Abierta', 6, 6),
(69, 'Corrección proyectos de alumnos', 'Corrección de los proyectos de los alumnos', '2025-03-03 10:38:51', 'Alta', 'Abierta', 8, 1),
(70, 'Convenio con las empresas', 'Firmar convenio con las empresas', '2025-03-03 10:39:17', 'Urgente', 'Abierta', 5, 1),
(71, 'Proyecto scripts de linux', 'Entrega del proyecto de scripts de linux', '2025-03-03 10:41:50', 'Urgente', 'Abierta', 1, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PROVINCIAS`
--

CREATE TABLE `PROVINCIAS` (
  `ID_PROVINCIA` int(5) NOT NULL,
  `NOMBRE_PROVINCIA` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `PROVINCIAS`
--

INSERT INTO `PROVINCIAS` (`ID_PROVINCIA`, `NOMBRE_PROVINCIA`) VALUES
(1, 'Almería'),
(2, 'Cádiz'),
(3, 'Córdoba'),
(4, 'Granada'),
(5, 'Huelva'),
(6, 'Jaén'),
(7, 'Málaga'),
(8, 'Sevilla');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `USUARIOS`
--

CREATE TABLE `USUARIOS` (
  `ID` int(5) NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `CORREO` varchar(100) NOT NULL,
  `CONTRASENA` varchar(100) NOT NULL,
  `ADMIN` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `USUARIOS`
--

INSERT INTO `USUARIOS` (`ID`, `NOMBRE`, `CORREO`, `CONTRASENA`, `ADMIN`) VALUES
(1, 'Administrador', 'admin@example.com', '$2y$10$i1H5Qvl1sP.o8dHlGMq1XeIqb4o5cA0xRoCmaOlfnjB84qCToL2Lu', b'1'),
(4, 'Guille', 'guille@example.com', '$2y$10$U4W3WA5NT9rIXRmCg/8./ePrkpaT9t.Kol2spAAFgVd/tyQxH.xV.', b'0'),
(5, 'Pepe', 'pepe@example.com', '$2y$10$Rlqv53ERrvkH0ddAiVldgezVw02AF2TuO0nQiHxtANuni312FVhIS', b'0'),
(6, 'Juan', 'juan@example.com', '$2y$10$EhAIld2IyT.XRuld87pgR.4TsYwokAVE51cA0ck0pa5wZs1s06Gcy', b'0'),
(7, 'María', 'maria@example.com', '$2y$10$sqAy/JqVuenuVJTJ.7wyWOtTvt639T9.GCMn94GvqwLgMVsVK.AtS', b'0'),
(8, 'Paula', 'paula@example.com', '$2y$10$46WFZZxz3uQgYxgtjczpoOzarkYO.rgPfCtlSU1NxRj.S1zGEjLMu', b'0'),
(12, 'ivan', 'ivan@example.com', '$2y$10$o7sr9LovBSfI/22LpF4UouO4cfXvZwxYOCjIlDTjV3Cdd2CpxuZge', b'0'),
(14, 'África', 'africa@example.com', '$2y$10$0rtCVfrYXkpXdHZomXtWTOp9GMqzfH3nrI1DXPRK1HRzFqVMENY4m', b'0');

-- --------------------------------------------------------

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `COMENTARIOS`
--
ALTER TABLE `COMENTARIOS`
  ADD PRIMARY KEY (`ID_COMENTARIO`),
  ADD KEY `ID_INCIDENCIA` (`ID_INCIDENCIA`),
  ADD KEY `ID` (`ID`);

--
-- Indices de la tabla `INCIDENCIAS`
--
ALTER TABLE `INCIDENCIAS`
  ADD PRIMARY KEY (`ID_INCIDENCIA`),
  ADD KEY `ID_PROVINCIA` (`ID_PROVINCIA`),
  ADD KEY `ID` (`ID`);

--
-- Indices de la tabla `PROVINCIAS`
--
ALTER TABLE `PROVINCIAS`
  ADD PRIMARY KEY (`ID_PROVINCIA`),
  ADD UNIQUE KEY `NOMBRE_PROVINCIA` (`NOMBRE_PROVINCIA`);

--
-- Indices de la tabla `USUARIOS`
--
ALTER TABLE `USUARIOS`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `CORREO` (`CORREO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `COMENTARIOS`
--
ALTER TABLE `COMENTARIOS`
  MODIFY `ID_COMENTARIO` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `INCIDENCIAS`
--
ALTER TABLE `INCIDENCIAS`
  MODIFY `ID_INCIDENCIA` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `PROVINCIAS`
--
ALTER TABLE `PROVINCIAS`
  MODIFY `ID_PROVINCIA` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `USUARIOS`
--
ALTER TABLE `USUARIOS`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `COMENTARIOS`
--
ALTER TABLE `COMENTARIOS`
  ADD CONSTRAINT `COMENTARIOS_ibfk_1` FOREIGN KEY (`ID_INCIDENCIA`) REFERENCES `INCIDENCIAS` (`ID_INCIDENCIA`),
  ADD CONSTRAINT `COMENTARIOS_ibfk_2` FOREIGN KEY (`ID`) REFERENCES `USUARIOS` (`ID`);

--
-- Filtros para la tabla `INCIDENCIAS`
--
ALTER TABLE `INCIDENCIAS`
  ADD CONSTRAINT `INCIDENCIAS_ibfk_1` FOREIGN KEY (`ID_PROVINCIA`) REFERENCES `PROVINCIAS` (`ID_PROVINCIA`),
  ADD CONSTRAINT `INCIDENCIAS_ibfk_2` FOREIGN KEY (`ID`) REFERENCES `USUARIOS` (`ID`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@@OLD_COLLATION_CONNECTION */;
