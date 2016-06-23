CREATE TABLE `sgpp`.`estados` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `nombre` VARCHAR(100) NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');

INSERT INTO `sgpp`.`estados` (`nombre`) VALUES ('Pendientes');
INSERT INTO `sgpp`.`estados` (`nombre`) VALUES ('En proceso');
INSERT INTO `sgpp`.`estados` (`nombre`) VALUES ('Pausa');
INSERT INTO `sgpp`.`estados` (`nombre`) VALUES ('Terminado');
