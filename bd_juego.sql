-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-03-2025 a las 06:10:39
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
-- Base de datos: `bd_juego`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arma`
--

CREATE TABLE `arma` (
  `id_arma` int(11) NOT NULL,
  `nom_arma` varchar(255) DEFAULT NULL,
  `img_arma` varchar(255) DEFAULT NULL,
  `id_tipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arma`
--

INSERT INTO `arma` (`id_arma`, `nom_arma`, `img_arma`, `id_tipo`) VALUES
(1, 'puño', 'img/armas/puno.png', 1),
(2, 'desert eagle', 'img/armas/deserteagle.png', 2),
(3, 'glock 18', 'img/armas/glock18.png', 2),
(4, 'M 249', 'img/armas/ametralladoram249.png', 3),
(5, '50', 'img/armas/ametralladora_50.jpeg', 3),
(6, 'automatico', 'img/armas/francotirador_automatico.jpg', 4),
(7, 'cerrojo', 'img/armas/francotirador_cerrojo.jpg', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avatar`
--

CREATE TABLE `avatar` (
  `id_avatar` int(11) NOT NULL,
  `nom_avatar` varchar(255) DEFAULT NULL,
  `img_avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `avatar`
--

INSERT INTO `avatar` (`id_avatar`, `nom_avatar`, `img_avatar`) VALUES
(1, 'subzero.png', 'img/avatares/subzero.png'),
(2, 'kitana.webp', 'img/avatares/kitana.webp'),
(3, 'mileena.png', 'img/avatares/mileena.png'),
(4, 'scorpion.webp', 'img/avatares/scorpion.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_arma`
--

CREATE TABLE `deta_arma` (
  `id_deta_arma` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_arma` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_mundos`
--

CREATE TABLE `deta_mundos` (
  `id_deta_mundo` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_mundo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_niv`
--

CREATE TABLE `deta_niv` (
  `id_deta_niv` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_nivel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas`
--

CREATE TABLE `estadisticas` (
  `id_estadistica` int(11) NOT NULL,
  `id_jugador` int(11) DEFAULT NULL,
  `partidas_jugadas` int(11) DEFAULT NULL,
  `partidas_ganadas` int(11) DEFAULT NULL,
  `partidas_perdidas` int(11) DEFAULT NULL,
  `total_golpes_dados` int(11) DEFAULT NULL,
  `total_golpes_recibidos` int(11) DEFAULT NULL,
  `total_dano_hecho` int(11) DEFAULT NULL,
  `total_dano_recibido` int(11) DEFAULT NULL,
  `asesinatos` int(11) DEFAULT NULL,
  `muertes` int(11) DEFAULT NULL,
  `ult_actualizacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `id_estado` int(11) NOT NULL,
  `estado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`id_estado`, `estado`) VALUES
(1, 'activo'),
(2, 'inactivo'),
(3, 'en espera'),
(4, 'llena'),
(5, 'en curso'),
(6, 'finalizada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `golpes`
--

CREATE TABLE `golpes` (
  `id_golpe` int(11) NOT NULL,
  `dano` int(11) DEFAULT NULL,
  `id_arma` int(11) DEFAULT NULL,
  `id_partida` int(11) DEFAULT NULL,
  `id_atacante` int(11) DEFAULT NULL,
  `id_victima` int(11) DEFAULT NULL,
  `fecha_golpe` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mundos`
--

CREATE TABLE `mundos` (
  `id_mundo` int(11) NOT NULL,
  `nom_mundo` varchar(255) DEFAULT NULL,
  `img_mundo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mundos`
--

INSERT INTO `mundos` (`id_mundo`, `nom_mundo`, `img_mundo`) VALUES
(1, 'mundo 1', 'img/mundos/mundo1.webp'),
(2, 'mundo 2', 'img/mundos/mundo2.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivel`
--

CREATE TABLE `nivel` (
  `id_nivel` int(11) NOT NULL,
  `nom_nivel` varchar(255) DEFAULT NULL,
  `puntos_minimos` int(11) DEFAULT NULL,
  `img_nivel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nivel`
--

INSERT INTO `nivel` (`id_nivel`, `nom_nivel`, `puntos_minimos`, `img_nivel`) VALUES
(1, 'recluta', 0, 'img/niveles/nivel1.png'),
(2, 'soldado', 500, 'img/niveles/nivel2.png'),
(3, 'guerrero', 1000, 'img/niveles/nivel3.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidas`
--

CREATE TABLE `partidas` (
  `id_partida` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partidas`
--

INSERT INTO `partidas` (`id_partida`, `id_sala`, `id_estado`) VALUES
(1, 5, 6),
(2, 4, 6),
(3, 4, 5),
(4, 5, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida_jugadores`
--

CREATE TABLE `partida_jugadores` (
  `id_partida` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `vida` int(11) NOT NULL DEFAULT 100,
  `estado` enum('vivo','muerto') NOT NULL DEFAULT 'vivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `partida_jugadores`
--

INSERT INTO `partida_jugadores` (`id_partida`, `id_jugador`, `vida`, `estado`) VALUES
(1, 100000, 0, 'muerto'),
(1, 333333, 89, 'vivo'),
(2, 100000, 100, 'vivo'),
(2, 333333, 0, 'muerto'),
(3, 100000, 60, 'vivo'),
(3, 333333, 100, 'vivo'),
(4, 100000, 0, 'muerto'),
(4, 333333, 96, 'vivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recu_contra`
--

CREATE TABLE `recu_contra` (
  `id_recu` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `token` int(11) DEFAULT NULL,
  `creacion_t` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiracion_t` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nom_rol` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nom_rol`) VALUES
(1, 'admin'),
(2, 'jugador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sala`
--

CREATE TABLE `sala` (
  `id_sala` int(11) NOT NULL,
  `nom_sala` varchar(255) DEFAULT NULL,
  `id_mundo` int(11) DEFAULT NULL,
  `id_nivel` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sala`
--

INSERT INTO `sala` (`id_sala`, `nom_sala`, `id_mundo`, `id_nivel`, `id_estado`) VALUES
(1, NULL, NULL, NULL, NULL),
(2, 'Sala 61', 1, NULL, 3),
(3, NULL, 2, NULL, 3),
(4, NULL, 2, NULL, 1),
(5, NULL, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sala_armas`
--

CREATE TABLE `sala_armas` (
  `id_sala_arma` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_arma` int(11) NOT NULL,
  `fecha_seleccion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sala_armas`
--

INSERT INTO `sala_armas` (`id_sala_arma`, `id_sala`, `id_jugador`, `id_arma`, `fecha_seleccion`) VALUES
(211, 4, 100000, 6, '2025-03-08 04:58:25'),
(212, 4, 333333, 7, '2025-03-08 04:58:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sala_jugadores`
--

CREATE TABLE `sala_jugadores` (
  `id_sala_jugador` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `fecha_ingreso` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sala_jugadores`
--

INSERT INTO `sala_jugadores` (`id_sala_jugador`, `id_sala`, `id_jugador`, `fecha_ingreso`) VALUES
(206, 4, 100000, '2025-03-08 04:58:25'),
(207, 4, 333333, '2025-03-08 04:58:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_arma`
--

CREATE TABLE `tipo_arma` (
  `id_tipo` int(11) NOT NULL,
  `nom_tip_arma` varchar(255) DEFAULT NULL,
  `dano` varchar(255) DEFAULT NULL,
  `balas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_arma`
--

INSERT INTO `tipo_arma` (`id_tipo`, `nom_tip_arma`, `dano`, `balas`) VALUES
(1, 'puño', '1 ', NULL),
(2, 'pistola', '2', 8),
(3, 'ametralladora', '10', 5),
(4, 'francotirador', '20', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `contra` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `puntos` int(11) DEFAULT NULL,
  `ultima_sesion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_avatar` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `id_estado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `user_name`, `contra`, `email`, `puntos`, `ultima_sesion`, `id_avatar`, `id_rol`, `id_estado`) VALUES
(100000, 'dani_m', '$2y$10$Iq02M5n2hqQOkLqxmJoT9enje2kseMfpOv9RyJ0dXmwEHPtbcdEvm', 'daniela@gmail.com', NULL, '2025-03-02 18:09:21', 2, 2, 1),
(111111, 'daniela_manrique', '$2y$10$SRvECgCzq3/KNFOTN8wKnOvVw0X7j3DR.VEqWLeZdcn3pJ23SA/v2', 'daniela@gmail.com', NULL, '2025-03-01 23:21:20', 2, 2, 1),
(333333, 'dmm_', '$2y$10$dk5bFC14HuyBOKBQYqn.CekG7tzfTUbp0bL4tyV0OIlsk0SWfxbTe', 'daniela.manrique.mo@gmail.com', NULL, '2025-03-05 04:46:30', 3, 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arma`
--
ALTER TABLE `arma`
  ADD PRIMARY KEY (`id_arma`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Indices de la tabla `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`id_avatar`);

--
-- Indices de la tabla `deta_arma`
--
ALTER TABLE `deta_arma`
  ADD PRIMARY KEY (`id_deta_arma`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_arma` (`id_arma`);

--
-- Indices de la tabla `deta_mundos`
--
ALTER TABLE `deta_mundos`
  ADD PRIMARY KEY (`id_deta_mundo`),
  ADD KEY `id_mundo` (`id_mundo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `deta_niv`
--
ALTER TABLE `deta_niv`
  ADD PRIMARY KEY (`id_deta_niv`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_nivel` (`id_nivel`);

--
-- Indices de la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  ADD PRIMARY KEY (`id_estadistica`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `golpes`
--
ALTER TABLE `golpes`
  ADD PRIMARY KEY (`id_golpe`),
  ADD KEY `id_arma` (`id_arma`),
  ADD KEY `id_partida` (`id_partida`),
  ADD KEY `id_atacante` (`id_atacante`),
  ADD KEY `id_victima` (`id_victima`);

--
-- Indices de la tabla `mundos`
--
ALTER TABLE `mundos`
  ADD PRIMARY KEY (`id_mundo`);

--
-- Indices de la tabla `nivel`
--
ALTER TABLE `nivel`
  ADD PRIMARY KEY (`id_nivel`);

--
-- Indices de la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD PRIMARY KEY (`id_partida`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `partida_jugadores`
--
ALTER TABLE `partida_jugadores`
  ADD PRIMARY KEY (`id_partida`,`id_jugador`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `recu_contra`
--
ALTER TABLE `recu_contra`
  ADD PRIMARY KEY (`id_recu`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`),
  ADD KEY `id_nivel` (`id_nivel`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `sala_armas`
--
ALTER TABLE `sala_armas`
  ADD PRIMARY KEY (`id_sala_arma`),
  ADD UNIQUE KEY `unique_sala_jugador` (`id_sala`,`id_jugador`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `sala_armas_ibfk_2` (`id_jugador`),
  ADD KEY `sala_armas_ibfk_3` (`id_arma`);

--
-- Indices de la tabla `sala_jugadores`
--
ALTER TABLE `sala_jugadores`
  ADD PRIMARY KEY (`id_sala_jugador`),
  ADD KEY `id_sala` (`id_sala`),
  ADD KEY `id_jugador` (`id_jugador`);

--
-- Indices de la tabla `tipo_arma`
--
ALTER TABLE `tipo_arma`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_avatar` (`id_avatar`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `id_estado` (`id_estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arma`
--
ALTER TABLE `arma`
  MODIFY `id_arma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `avatar`
--
ALTER TABLE `avatar`
  MODIFY `id_avatar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `estado`
--
ALTER TABLE `estado`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `mundos`
--
ALTER TABLE `mundos`
  MODIFY `id_mundo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `nivel`
--
ALTER TABLE `nivel`
  MODIFY `id_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `partidas`
--
ALTER TABLE `partidas`
  MODIFY `id_partida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sala_armas`
--
ALTER TABLE `sala_armas`
  MODIFY `id_sala_arma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT de la tabla `sala_jugadores`
--
ALTER TABLE `sala_jugadores`
  MODIFY `id_sala_jugador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT de la tabla `tipo_arma`
--
ALTER TABLE `tipo_arma`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arma`
--
ALTER TABLE `arma`
  ADD CONSTRAINT `arma_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_arma` (`id_tipo`);

--
-- Filtros para la tabla `deta_arma`
--
ALTER TABLE `deta_arma`
  ADD CONSTRAINT `deta_arma_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `deta_arma_ibfk_2` FOREIGN KEY (`id_arma`) REFERENCES `arma` (`id_arma`);

--
-- Filtros para la tabla `deta_mundos`
--
ALTER TABLE `deta_mundos`
  ADD CONSTRAINT `deta_mundos_ibfk_1` FOREIGN KEY (`id_mundo`) REFERENCES `mundos` (`id_mundo`),
  ADD CONSTRAINT `deta_mundos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `deta_niv`
--
ALTER TABLE `deta_niv`
  ADD CONSTRAINT `deta_niv_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `deta_niv_ibfk_2` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`);

--
-- Filtros para la tabla `estadisticas`
--
ALTER TABLE `estadisticas`
  ADD CONSTRAINT `estadisticas_ibfk_1` FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `golpes`
--
ALTER TABLE `golpes`
  ADD CONSTRAINT `golpes_ibfk_1` FOREIGN KEY (`id_arma`) REFERENCES `arma` (`id_arma`),
  ADD CONSTRAINT `golpes_ibfk_2` FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`),
  ADD CONSTRAINT `golpes_ibfk_3` FOREIGN KEY (`id_atacante`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `golpes_ibfk_4` FOREIGN KEY (`id_victima`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `partidas`
--
ALTER TABLE `partidas`
  ADD CONSTRAINT `partidas_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `partidas_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `partida_jugadores`
--
ALTER TABLE `partida_jugadores`
  ADD CONSTRAINT `partida_jugadores_ibfk_1` FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`) ON DELETE CASCADE,
  ADD CONSTRAINT `partida_jugadores_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `recu_contra`
--
ALTER TABLE `recu_contra`
  ADD CONSTRAINT `recu_contra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `sala`
--
ALTER TABLE `sala`
  ADD CONSTRAINT `sala_ibfk_1` FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`),
  ADD CONSTRAINT `sala_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

--
-- Filtros para la tabla `sala_armas`
--
ALTER TABLE `sala_armas`
  ADD CONSTRAINT `sala_armas_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `sala_armas_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `sala_armas_ibfk_3` FOREIGN KEY (`id_arma`) REFERENCES `arma` (`id_arma`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sala_jugadores`
--
ALTER TABLE `sala_jugadores`
  ADD CONSTRAINT `sala_jugadores_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `sala_jugadores_ibfk_2` FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_avatar`) REFERENCES `avatar` (`id_avatar`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`),
  ADD CONSTRAINT `usuario_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
