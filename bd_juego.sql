CREATE TABLE `usuario` (
  `id_usuario` int PRIMARY KEY,
  `user_name` varchar(255),
  `contra` varchar(255),
  `email` varchar(255),
  `puntos` int,
  `ultima_sesion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `id_avatar` int,
  `id_rol` int,
  `id_estado` int
);

CREATE TABLE `avatar` (
  `id_avatar` int PRIMARY KEY,
  `nom_avatar` varchar(255),
  `img_avatar` varchar(255)
);

CREATE TABLE `rol` (
  `id_rol` int PRIMARY KEY,
  `nom_rol` varchar(255)
);

CREATE TABLE `estado` (
  `id_estado` int PRIMARY KEY,
  `estado` varchar(255)
);

CREATE TABLE `recu_contra` (
  `id_recu` int PRIMARY KEY,
  `id_usuario` int,
  `token` int,
  `creacion_t` timestamp DEFAULT CURRENT_TIMESTAMP,
  `expiracion_t` timestamp DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `mundos` (
  `id_mundo` int PRIMARY KEY,
  `nom_mundo` varchar(255),
  `img_mundo` varchar(255)
);

CREATE TABLE `deta_mundos` (
  `id_deta_mundo` int PRIMARY KEY,
  `id_usuario` int,
  `id_mundo` int
);

CREATE TABLE `nivel` (
  `id_nivel` int PRIMARY KEY,
  `nom_nivel` varchar(255),
  `puntos_minimos` int,
  `img_nivel` varchar(255)
);

CREATE TABLE `deta_niv` (
  `id_deta_niv` int PRIMARY KEY,
  `id_usuario` int,
  `id_nivel` int
);

CREATE TABLE `tipo_arma` (
  `id_tipo` int PRIMARY KEY,
  `nom_tip_arma` varchar(255),
  `dano` varchar(255),
  `balas` int
);

CREATE TABLE `arma` (
  `id_arma` int PRIMARY KEY,
  `nom_arma` varchar(255),
  `img_arma` varchar(255),
  `id_tipo` int
);

CREATE TABLE `deta_arma` (
  `id_deta_arma` int PRIMARY KEY,
  `id_usuario` int,
  `id_arma` int
);

CREATE TABLE `sala` (
  `id_sala` int PRIMARY KEY,
  `nom_sala` varchar(255),
  `id_mundo` int,
  `id_nivel` int,
  `id_estado` int
);

CREATE TABLE `sala_jugadores` (
  `id_sala_jugador` INT PRIMARY KEY,
  `id_sala` INT,
  `id_jugador` INT,
  `fecha_ingreso` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `partidas` (
  `id_partida` INT PRIMARY KEY,
  `id_sala` INT,
  `fecha_inicio` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` timestamp DEFAULT CURRENT_TIMESTAMP
  `id_estado` int
);

CREATE TABLE `partida_jugadores` (
  `id_partida_jugador` INT PRIMARY KEY,
  `id_partida` INT,
  `id_jugador` INT
);

CREATE TABLE `golpes` (
  `id_golpe` INT PRIMARY KEY,
  `dano` INT,
  `id_arma` INT,
  `id_partida` INT,
  `id_atacante` INT,
  `id_victima` INT,
  `fecha_golpe` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);

CREATE TABLE `estadisticas` (
  `id_estadistica` INT PRIMARY KEY,
  `id_jugador` INT,
  `partidas_jugadas` INT,
  `partidas_ganadas` INT,
  `partidas_perdidas` INT,
  `total_golpes_dados` INT,
  `total_golpes_recibidos` INT,
  `total_dano_hecho` INT,
  `total_dano_recibido` INT,
  `asesinatos` INT,
  `muertes` INT,
  `ult_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_avatar`) REFERENCES `avatar` (`id_avatar`);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);

ALTER TABLE `usuario` ADD FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

ALTER TABLE `recu_contra` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `deta_mundos` ADD FOREIGN KEY (`id_mundo`) REFERENCES `mundos` (`id_mundo`);

ALTER TABLE `deta_mundos` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `deta_niv` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `deta_niv` ADD FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`);

ALTER TABLE `sala` ADD FOREIGN KEY (`id_nivel`) REFERENCES `nivel` (`id_nivel`);

ALTER TABLE `sala` ADD FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

ALTER TABLE `sala_jugadores` ADD FOREIGN KEY (`id_sala_jugador`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `partidas` ADD FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`);

ALTER TABLE `partidas` ADD FOREIGN KEY (`id_estado`) REFERENCES `estado` (`id_estado`);

ALTER TABLE `deta_arma` ADD FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `deta_arma` ADD FOREIGN KEY (`id_arma`) REFERENCES `arma` (`id_arma`);

ALTER TABLE `arma` ADD FOREIGN KEY (`id_tipo`) REFERENCES `tipo_arma` (`id_tipo`);

ALTER TABLE `partida_jugadores` ADD FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`);

ALTER TABLE `partida_jugadores` ADD FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `golpes` ADD FOREIGN KEY (`id_arma`) REFERENCES `arma` (`id_arma`);

ALTER TABLE `golpes` ADD FOREIGN KEY (`id_partida`) REFERENCES `partidas` (`id_partida`);

ALTER TABLE `golpes` ADD FOREIGN KEY (`id_atacante`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `golpes` ADD FOREIGN KEY (`id_victima`) REFERENCES `usuario` (`id_usuario`);

ALTER TABLE `estadisticas` ADD FOREIGN KEY (`id_jugador`) REFERENCES `usuario` (`id_usuario`);
