CREATE TABLE `permisos` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `permiso` VARCHAR(200) NOT NULL COMMENT '',
  `rol_id` INT NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_permisos_roles_idx` (`rol_id` ASC)  COMMENT '',
  CONSTRAINT `fk_permisos_roles`
    FOREIGN KEY (`rol_id`)
    REFERENCES `sgpp`.`roles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


INSERT INTO permisos (permiso, rol_id) VALUES
("acceso/denegado", 3),