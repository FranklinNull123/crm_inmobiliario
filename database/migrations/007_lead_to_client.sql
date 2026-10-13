-- Ejecutar una sola vez después de restaurar cms_inmobiliario.sql.
USE `crm_inmobiliario`;

ALTER TABLE `leads`
  ADD COLUMN `id_cliente_convertido` INT UNSIGNED NULL AFTER `etapa`,
  ADD KEY `idx_leads_cliente_convertido` (`id_cliente_convertido`),
  ADD CONSTRAINT `fk_leads_cliente_convertido`
    FOREIGN KEY (`id_cliente_convertido`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT;
