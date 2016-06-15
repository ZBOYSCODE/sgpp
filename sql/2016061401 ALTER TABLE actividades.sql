/* ELIMINAR PROYECTO_ID DE LA TABLA BLOQUE */

ALTER TABLE `actividades` 
ADD COLUMN `proyecto_id` INT NOT NULL COMMENT '' AFTER `bloque_id`;

ALTER TABLE `actividades` 
CHANGE COLUMN `hh_estimadas` `hh_estimadas` INT(11) NOT NULL COMMENT '' ,
CHANGE COLUMN `hh_reales` `hh_reales` INT(11) NULL DEFAULT 0 COMMENT '' ;

