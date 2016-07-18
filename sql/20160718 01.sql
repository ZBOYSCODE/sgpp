/*CREATE TABLE `sgpp`.`estado_bloques` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `nombre` VARCHAR(150) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
COMMENT = 'estado_tareas';*/

ALTER TABLE `sgpp`.`bloques` 
ADD COLUMN `estado_id` INT NULL COMMENT '' AFTER `orden`,
ADD COLUMN `proyecto_id` INT NULL COMMENT '' AFTER `estado_id`,
ADD COLUMN `nombre` VARCHAR(250) NULL COMMENT '' AFTER `proyecto_id`,
ADD COLUMN `hh_estimadas` INT NULL COMMENT '' AFTER `nombre`,
ADD COLUMN `fecha_termino` DATETIME NULL COMMENT '' AFTER `hh_estimadas`;


ALTER TABLE `sgpp`.`actividades` 
DROP FOREIGN KEY `fk_estado`;
ALTER TABLE `sgpp`.`actividades` 
CHANGE COLUMN `estado_id` `estado` INT(11) NOT NULL DEFAULT 1 COMMENT '' ,
DROP INDEX `fk_estado_idx` ;

ALTER TABLE `sgpp`.`bloques` 
ADD INDEX `fk_estado_id_idx` (`estado_id` ASC)  COMMENT '';
ALTER TABLE `sgpp`.`bloques` 
ADD CONSTRAINT `fk_estado_id`
  FOREIGN KEY (`estado_id`)
  REFERENCES `sgpp`.`estados` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `sgpp`.`bloques` 
ADD COLUMN `creado_por` INT NULL COMMENT '' AFTER `fecha_termino`,
ADD INDEX `fk_creado_por_idx` (`creado_por` ASC)  COMMENT '';
ALTER TABLE `sgpp`.`bloques` 
ADD CONSTRAINT `fk_creado_por`
  FOREIGN KEY (`creado_por`)
  REFERENCES `sgpp`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `sgpp`.`bloques` 
DROP FOREIGN KEY `usuario_id`;
ALTER TABLE `sgpp`.`bloques` 
CHANGE COLUMN `usuario_id` `usuario_id` INT(11) NULL COMMENT '' ;
ALTER TABLE `sgpp`.`bloques` 
ADD CONSTRAINT `usuario_id`
  FOREIGN KEY (`usuario_id`)
  REFERENCES `sgpp`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

CREATE TABLE `sgpp`.`roles` (
`id` INT NOT NULL COMMENT '',
`nombre` VARCHAR(100) NOT NULL COMMENT '',
PRIMARY KEY (`id`)  COMMENT '');

ALTER TABLE `sgpp`.`roles` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ;

INSERT INTO `sgpp`.`roles` (`nombre`) VALUES ('Administrador');
INSERT INTO `sgpp`.`roles` (`nombre`) VALUES ('Gerente Proyecto');
INSERT INTO `sgpp`.`roles` (`nombre`) VALUES ('Jefe Proyecto');
INSERT INTO `sgpp`.`roles` (`nombre`) VALUES ('Desarrollador');


