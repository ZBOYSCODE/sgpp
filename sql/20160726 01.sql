CREATE TABLE `prioridades` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `nombre` VARCHAR(100) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');

INSERT INTO `prioridades` (`id`, `nombre`) VALUES (1, 'Normal');
INSERT INTO `prioridades` (`id`, `nombre`) VALUES (2, 'Urgente');

ALTER TABLE `bloques` 
ADD COLUMN `prioridad_id` INT NOT NULL DEFAULT 1 COMMENT '' AFTER `proyecto_id`,
ADD INDEX `fk_bloque_prioridad_idx` (`prioridad_id` ASC)  COMMENT '';
ALTER TABLE `bloques` 
ADD CONSTRAINT `fk_bloque_prioridad`
  FOREIGN KEY (`prioridad_id`)
  REFERENCES `prioridades` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;