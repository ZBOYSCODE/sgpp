ALTER TABLE `sgpp`.`actividades` 
ADD COLUMN `estado_id` INT(11) NULL COMMENT '' AFTER `proyecto_id`,
ADD INDEX `fk_estado_idx` (`estado_id` ASC)  COMMENT '';
ALTER TABLE `sgpp`.`actividades` 
ADD CONSTRAINT `fk_estado`
  FOREIGN KEY (`estado_id`)
  REFERENCES `sgpp`.`estados` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `sgpp`.`actividades` 
DROP FOREIGN KEY `fk_estado`;
ALTER TABLE `sgpp`.`actividades` 
CHANGE COLUMN `estado_id` `estado_id` INT(11) NULL DEFAULT 1 COMMENT '' ;
ALTER TABLE `sgpp`.`actividades` 
ADD CONSTRAINT `fk_estado`
  FOREIGN KEY (`estado_id`)
  REFERENCES `sgpp`.`estados` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
