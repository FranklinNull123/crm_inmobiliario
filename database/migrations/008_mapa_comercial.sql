USE `crm_inmobiliario`;

ALTER TABLE `proyectos`
  ADD COLUMN `ubigeo_distrito` CHAR(6) NULL AFTER `ubicacion`,
  ADD COLUMN `latitud` DECIMAL(10,7) NULL AFTER `ubigeo_distrito`,
  ADD COLUMN `longitud` DECIMAL(10,7) NULL AFTER `latitud`,
  ADD KEY `idx_proyectos_ubigeo` (`ubigeo_distrito`);

ALTER TABLE `leads`
  ADD COLUMN `ubigeo_distrito` CHAR(6) NULL AFTER `id_cliente_convertido`,
  ADD COLUMN `latitud` DECIMAL(10,7) NULL AFTER `ubigeo_distrito`,
  ADD COLUMN `longitud` DECIMAL(10,7) NULL AFTER `latitud`,
  ADD KEY `idx_leads_ubigeo` (`ubigeo_distrito`);
