-- Vincula usuarios con fichas de asesor y registra propietarios de leads.
-- Ejecutar una sola vez después de 001_fundamentos.sql.

USE `crm_inmobiliario`;

ALTER TABLE `asesores`
  ADD COLUMN `id_usuario` INT UNSIGNED NULL,
  ADD UNIQUE KEY `uq_asesores_usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_asesores_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

UPDATE `asesores` a
JOIN `usuarios` u ON LOWER(TRIM(u.email)) = LOWER(TRIM(a.email))
SET a.id_usuario = u.id_usuario
WHERE a.id_usuario IS NULL;

ALTER TABLE `leads`
  ADD COLUMN `id_campana` INT UNSIGNED NULL AFTER `id`,
  ADD COLUMN `id_usuario_asignado` INT UNSIGNED NULL AFTER `id_campana`,
  ADD KEY `idx_leads_usuario_etapa` (`id_usuario_asignado`, `etapa`),
  ADD CONSTRAINT `fk_leads_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas` (`id_campana`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_usuario` FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;

UPDATE `leads` l
JOIN `clientes_leads` cl ON LOWER(TRIM(cl.nombres)) = LOWER(TRIM(l.nombre))
LEFT JOIN `asesores` a ON a.id = cl.id_asesor
SET l.id_campana = cl.id_campana, l.id_usuario_asignado = a.id_usuario
WHERE l.id_campana IS NULL AND l.id_usuario_asignado IS NULL;