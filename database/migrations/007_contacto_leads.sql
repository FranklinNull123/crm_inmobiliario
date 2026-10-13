USE `crm_inmobiliario`;

ALTER TABLE `leads`
  ADD COLUMN `telefono` VARCHAR(30) NULL AFTER `nombre`,
  ADD COLUMN `email` VARCHAR(120) NULL AFTER `telefono`;
