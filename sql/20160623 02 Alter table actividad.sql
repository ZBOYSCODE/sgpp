ALTER TABLE `actividades` 
ADD COLUMN `estado_id` INT(11) NOT NULL COMMENT '' AFTER `proyecto_id`,
ADD INDEX `fk_estado_idx` (`estado_id` ASC)  COMMENT '';
ALTER TABLE `actividades` 
ADD CONSTRAINT `fk_estado`
  FOREIGN KEY (`estado_id`)
  REFERENCES `estados` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `actividades` 
DROP FOREIGN KEY `fk_estado`;
ALTER TABLE `actividades` 
CHANGE COLUMN `estado_id` `estado_id` INT(11) NOT NULL DEFAULT 1 COMMENT '' ;
ALTER TABLE `actividades` 
ADD CONSTRAINT `fk_estado`
  FOREIGN KEY (`estado_id`)
  REFERENCES `estados` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
