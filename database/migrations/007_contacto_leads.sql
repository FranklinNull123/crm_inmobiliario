USE `crm_inmobiliario`;

ALTER TABLE `leads`
  ADD COLUMN `telefono` VARCHAR(30) NULL AFTER `nombre`,
  ADD COLUMN `email` VARCHAR(120) NULL AFTER `telefono`,
  ADD COLUMN `id_proyecto` INT UNSIGNED NULL AFTER `proyecto_interes`,
  ADD KEY `idx_leads_proyecto` (`id_proyecto`);
