CREATE TABLE `bloques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proyecto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `horas` int(11) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proyecto_id_idx` (`proyecto_id`),
  KEY `usuario_id_idx` (`usuario_id`),
  CONSTRAINT `proyecto_id` FOREIGN KEY (`proyecto_id`) REFERENCES `proyecto` (`proy_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `usuario_id` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `bloques` 
ADD COLUMN `orden` INT NOT NULL COMMENT '' AFTER `fecha`;



CREATE TABLE `actividades` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `bloque_id` INT NOT NULL COMMENT '',
  `hh_estimadas` FLOAT NOT NULL COMMENT '',
  `hh_reales` FLOAT NULL DEFAULT 0 COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `bloque_id_idx` (`bloque_id` ASC)  COMMENT '',
  CONSTRAINT `bloque_id`
    FOREIGN KEY (`bloque_id`)
    REFERENCES `sgpp`.`bloques` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

ALTER TABLE `actividades` 
ADD COLUMN `descripcion` VARCHAR(255) NOT NULL COMMENT '' AFTER `hh_reales`;


