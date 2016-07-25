ALTER TABLE `sgpp`.`proyecto` 
ADD COLUMN `descripcion` TEXT NULL COMMENT '' AFTER `coordinador_id`,
ADD COLUMN `created_at` DATETIME NULL DEFAULT now() COMMENT '' AFTER `descripcion`,
ADD COLUMN `updated_at` DATETIME NULL COMMENT '' AFTER `created_at`;

CREATE TABLE `sgpp`.`equipo_users` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `user_id` INT NOT NULL COMMENT '',
  `equipo_id` INT NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `fk_equipouser_user_idx` (`user_id` ASC)  COMMENT '',
  INDEX `fk_equipouser_equipo_idx` (`equipo_id` ASC)  COMMENT '',
  CONSTRAINT `fk_equipouser_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `sgpp`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_equipouser_equipo`
    FOREIGN KEY (`equipo_id`)
    REFERENCES `sgpp`.`equipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


