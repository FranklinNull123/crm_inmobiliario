USE `crm_inmobiliario`;

ALTER TABLE `leads`
  ADD COLUMN `dni` VARCHAR(20) NULL AFTER `nombre`,
  ADD COLUMN `telefono` VARCHAR(30) NULL AFTER `dni`,
  ADD COLUMN `email` VARCHAR(120) NULL AFTER `telefono`,
  ADD COLUMN `id_proyecto` INT UNSIGNED NULL AFTER `proyecto_interes`,
  ADD KEY `idx_leads_proyecto` (`id_proyecto`);
