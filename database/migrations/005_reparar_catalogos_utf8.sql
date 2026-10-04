-- Repara los caracteres acentuados convertidos a ?? por importación desde consola.
-- Ejecutar una sola vez después de 001_fundamentos.sql y 004_proyectos_inventario.sql.
USE `cms_inmobiliario`;

UPDATE `catalogo_valores`
SET `codigo` = 'en_planificacion', `etiqueta` = CONVERT(UNHEX('456e20706c616e69666963616369c3b36e') USING utf8mb4)
WHERE `categoria` = 'estado_proyecto' AND HEX(`codigo`) = '456E20706C616E696669636163693F3F6E';

UPDATE `catalogo_valores`
SET `codigo` = 'en_construccion', `etiqueta` = CONVERT(UNHEX('456e20636f6e73747275636369c3b36e') USING utf8mb4)
WHERE `categoria` = 'estado_proyecto' AND HEX(`codigo`) = '456E20636F6E737472756363693F3F6E';

UPDATE `catalogo_valores`
SET `etiqueta` = CONVERT(UNHEX('4e65676f6369616369c3b36e') USING utf8mb4)
WHERE `categoria` = 'estado_lead' AND `codigo` = 'negociacion' AND HEX(`etiqueta`) = '4E65676F63696163693F3F6E';

UPDATE `catalogo_valores`
SET `etiqueta` = CONVERT(UNHEX('446570c3b37369746f20656e206375656e7461') USING utf8mb4)
WHERE `categoria` = 'medio_pago' AND `codigo` = 'deposito' AND HEX(`etiqueta`) = '4465703F3F7369746F20656E206375656E7461';

UPDATE `catalogo_valores`
SET `etiqueta` = CONVERT(UNHEX('5461726a657461206465206372c3a96469746f2f64c3a96269746f') USING utf8mb4)
WHERE `categoria` = 'medio_pago' AND `codigo` = 'tarjeta' AND HEX(`etiqueta`) = '5461726A6574612064652063723F3F6469746F2F643F3F6269746F';

UPDATE `etapas_proyecto`
SET `nombre` = CONVERT(UNHEX('436f6d65726369616c697a616369c3b36e') USING utf8mb4)
WHERE HEX(`nombre`) = '436F6D65726369616C697A6163693F3F6E';

UPDATE `etapas_proyecto`
SET `nombre` = CONVERT(UNHEX('436f6e73747275636369c3b36e') USING utf8mb4)
WHERE HEX(`nombre`) = '436F6E737472756363693F3F6E';