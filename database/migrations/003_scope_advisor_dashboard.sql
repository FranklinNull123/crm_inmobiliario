-- Evita que el rol asesor consulte ventas globales desde el dashboard.
-- Ejecutar una sola vez después de las migraciones 001 y 002.
USE `crm_inmobiliario`;

DELETE rp
FROM `rol_permisos` rp
JOIN `roles` r ON r.id = rp.id_rol
JOIN `permisos` p ON p.id = rp.id_permiso
WHERE r.nombre = 'asesor'
  AND p.modulo = 'dashboard';