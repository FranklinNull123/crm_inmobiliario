<?php
declare(strict_types=1);
/** @var array<string, mixed> $configData */
/** @var bool $canViewAudit */
/** @var string $csrfToken */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$configJson = json_encode($configData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
?>
<div id="configuracionView" class="space-y-8">
  <div id="configMessage" class="hidden rounded-md border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

  <section class="border-b border-slate-200 pb-7 dark:border-slate-800">
    <div class="mb-4">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Configuración general</h2>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Parámetros base de la operación.</p>
    </div>
    <form id="settingsForm" class="grid gap-4 sm:grid-cols-3">
      <label class="text-sm font-medium">Nombre de empresa
        <input name="empresa_nombre" maxlength="120" required value="<?= $escape($configData['settings']['empresa_nombre'] ?? 'Inmobi CRM') ?>" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-900">
      </label>
      <label class="text-sm font-medium">Moneda principal
        <select name="moneda_principal" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-900">
          <?php foreach (($configData['catalogs'] ?? []) as $catalog): ?>
            <?php if ($catalog['categoria'] === 'moneda' && (int) $catalog['activo'] === 1): ?>
              <option value="<?= $escape($catalog['codigo']) ?>" <?= ($configData['settings']['moneda_principal'] ?? 'PEN') === $catalog['codigo'] ? 'selected' : '' ?>><?= $escape($catalog['etiqueta']) ?></option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="text-sm font-medium">Zona horaria
        <input name="zona_horaria" maxlength="120" required value="<?= $escape($configData['settings']['zona_horaria'] ?? 'America/Lima') ?>" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-900">
      </label>
      <div class="sm:col-span-3"><button class="rounded-md bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700" type="submit">Guardar configuración</button></div>
    </form>
  </section>

  <section class="border-b border-slate-200 pb-7 dark:border-slate-800">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
      <div><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Usuarios y roles</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Las contraseñas se almacenan con password_hash.</p></div>
    </div>
    <form id="userForm" class="grid gap-3 rounded-md bg-slate-50 p-4 dark:bg-slate-900/60 sm:grid-cols-2 lg:grid-cols-5">
      <input name="name" required minlength="2" maxlength="100" placeholder="Nombre completo" aria-label="Nombre completo" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <input name="email" type="email" required maxlength="120" placeholder="Correo" aria-label="Correo" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <input name="password" type="password" required minlength="12" maxlength="4096" placeholder="Contraseña (12+ caracteres)" aria-label="Contraseña" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <select name="role_id" required aria-label="Rol" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
        <?php foreach ($configData['roles'] as $role): ?><?php if ($role['nombre'] !== 'superadmin'): ?><option value="<?= (int) $role['id'] ?>"><?= $escape($role['etiqueta']) ?></option><?php endif; ?><?php endforeach; ?>
      </select>
      <button class="h-10 rounded-md bg-[#183b35] px-4 text-sm font-semibold text-white hover:bg-[#24584d]" type="submit">Crear usuario</button>
    </form>
    <div class="mt-4 overflow-x-auto">
      <table class="w-full min-w-[560px] text-left text-sm">
        <thead class="border-b border-slate-200 text-xs uppercase text-slate-500 dark:border-slate-800"><tr><th class="py-2 pr-4">Usuario</th><th class="py-2 pr-4">Correo</th><th class="py-2 pr-4">Rol</th><th class="py-2">Estado</th></tr></thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($configData['users'] as $user): ?>
            <tr>
              <td class="py-3 pr-4">
                <div class="flex items-center gap-3">
                  <?php $userAvatar = trim((string) ($user['avatar_url'] ?? '')); ?>
                  <?php if ($userAvatar !== ''): ?>
                    <img src="<?= $escape($userAvatar) ?>" alt="Avatar de <?= $escape($user['nombre']) ?>" class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700" />
                  <?php else: ?>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200"><?= $escape(mb_substr($user['nombre'], 0, 1)) ?></span>
                  <?php endif; ?>
                  <span class="font-medium"><?= $escape($user['nombre']) ?></span>
                </div>
              </td>
              <td class="py-3 pr-4 text-slate-500"><?= $escape($user['email']) ?></td>
              <td class="py-3 pr-4"><?= $escape($user['roles'] ?? 'Sin rol') ?></td>
              <td class="py-3">
                <div class="flex items-center gap-2">
                  <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium <?= (int) $user['estado'] === 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' ?>"><?= (int) $user['estado'] === 1 ? 'Activo' : 'Inactivo' ?></span>
                  <button type="button" data-user-id="<?= (int) $user['id'] ?>" data-active="<?= (int) $user['estado'] === 1 ? '0' : '1' ?>" class="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-400"><?= (int) $user['estado'] === 1 ? 'Desactivar' : 'Activar' ?></button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="border-b border-slate-200 pb-7 dark:border-slate-800">
    <div class="mb-4"><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Matriz de permisos</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Cada cambio se aplica en el servidor. El superadministrador conserva acceso total.</p></div>
    <div class="overflow-x-auto rounded-md border border-slate-200 dark:border-slate-800">
      <table class="w-full min-w-[1050px] text-left text-xs">
        <thead class="bg-slate-50 text-slate-500 dark:bg-slate-900"><tr><th class="sticky left-0 bg-slate-50 px-3 py-3 dark:bg-slate-900">Permiso</th><?php foreach ($configData['roles'] as $role): ?><th class="px-2 py-3 text-center"><?= $escape($role['etiqueta']) ?></th><?php endforeach; ?></tr></thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($configData['permissions'] as $permission): ?>
            <tr><th class="sticky left-0 bg-white px-3 py-2 font-medium dark:bg-slate-950"><?= $escape($permission['etiqueta']) ?></th>
              <?php foreach ($configData['roles'] as $role):
                $isAllowed = $role['nombre'] === 'superadmin';
                foreach ($configData['assignments'] as $assignment) {
                  if ((int) $assignment['id_rol'] === (int) $role['id'] && (int) $assignment['id_permiso'] === (int) $permission['id']) { $isAllowed = (bool) $assignment['permitido']; break; }
                }
              ?>
                <td class="px-2 py-2 text-center"><input type="checkbox" aria-label="<?= $escape($role['etiqueta'] . ': ' . $permission['etiqueta']) ?>" data-role-id="<?= (int) $role['id'] ?>" data-permission-id="<?= (int) $permission['id'] ?>" <?= $isAllowed ? 'checked' : '' ?> <?= $role['nombre'] === 'superadmin' ? 'disabled' : '' ?> class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section>
    <div class="mb-4"><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Catálogos</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Desactiva valores para nuevas operaciones sin borrar registros históricos.</p></div>
    <form id="catalogForm" class="mb-4 grid gap-3 rounded-md bg-slate-50 p-4 dark:bg-slate-900/60 sm:grid-cols-4">
      <input name="category" required maxlength="60" placeholder="Categoría (ej. medio_pago)" aria-label="Categoría" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <input name="code" required maxlength="60" placeholder="Código" aria-label="Código" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <input name="label" required maxlength="120" placeholder="Etiqueta visible" aria-label="Etiqueta visible" class="h-10 rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      <button class="h-10 rounded-md border border-slate-300 bg-white px-4 text-sm font-semibold hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-950" type="submit">Añadir valor</button>
    </form>
    <div class="overflow-x-auto rounded-md border border-slate-200 dark:border-slate-800">
      <table class="w-full min-w-[600px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900"><tr><th class="px-3 py-3">Categoría</th><th class="px-3 py-3">Código</th><th class="px-3 py-3">Etiqueta</th><th class="px-3 py-3">Estado</th><th class="px-3 py-3">Acción</th></tr></thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($configData['catalogs'] as $catalog): ?><tr><td class="px-3 py-3"><?= $escape($catalog['categoria']) ?></td><td class="px-3 py-3 font-mono text-xs"><?= $escape($catalog['codigo']) ?></td><td class="px-3 py-3"><?= $escape($catalog['etiqueta']) ?></td><td class="px-3 py-3"><?= (int) $catalog['activo'] === 1 ? 'Activo' : 'Inactivo' ?></td><td class="px-3 py-3"><button type="button" data-catalog-id="<?= (int) $catalog['id'] ?>" data-active="<?= (int) $catalog['activo'] === 1 ? '0' : '1' ?>" class="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-400"><?= (int) $catalog['activo'] === 1 ? 'Desactivar' : 'Activar' ?></button></td></tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <?php if ($canViewAudit): ?>
    <section class="border-t border-slate-200 pt-7 dark:border-slate-800">
      <div class="mb-4"><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Auditoría reciente</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Últimos 100 eventos registrados con usuario, dirección IP y dispositivo.</p></div>
      <div class="overflow-x-auto rounded-md border border-slate-200 dark:border-slate-800">
        <table class="w-full min-w-[800px] text-left text-sm">
          <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-900"><tr><th class="px-3 py-3">Fecha</th><th class="px-3 py-3">Usuario</th><th class="px-3 py-3">Evento</th><th class="px-3 py-3">Registro</th><th class="px-3 py-3">IP</th><th class="px-3 py-3">Dispositivo</th></tr></thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <?php foreach ($configData['auditLogs'] as $log): ?><tr><td class="whitespace-nowrap px-3 py-3 text-slate-500"><?= $escape($log['creado_en']) ?></td><td class="px-3 py-3"><?= $escape($log['usuario'] ?? 'Sistema') ?></td><td class="px-3 py-3 font-medium"><?= $escape($log['evento']) ?></td><td class="px-3 py-3"><?= $escape($log['entidad'] . ($log['entidad_id'] ? ' #' . $log['entidad_id'] : '')) ?></td><td class="px-3 py-3 font-mono text-xs"><?= $escape($log['ip'] ?? '') ?></td><td class="max-w-[220px] truncate px-3 py-3 text-xs text-slate-500" title="<?= $escape($log['dispositivo'] ?? '') ?>"><?= $escape($log['dispositivo'] ?? '') ?></td></tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>
</div>

<script>
  const configurationData = <?= $configJson ?>;
  const configurationCsrf = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
  (() => {
    const root = document.getElementById('configuracionView');
    const message = document.getElementById('configMessage');
    const showMessage = (text, error = false) => {
      message.textContent = text;
      message.className = `rounded-md border px-4 py-3 text-sm ${error ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`;
    };
    const post = async (action, data = {}) => {
      const response = await fetch('?api=configuracion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': configurationCsrf },
        body: JSON.stringify({ action, ...data }),
      });
      const result = await response.json();
      if (!response.ok) throw new Error(result.message || 'No se pudo guardar el cambio.');
      return result;
    };
    root.querySelector('#settingsForm').addEventListener('submit', async (event) => {
      event.preventDefault();
      try { await post('settings', Object.fromEntries(new FormData(event.currentTarget))); showMessage('Configuración guardada.'); }
      catch (error) { showMessage(error.message, true); }
    });
    root.querySelector('#userForm').addEventListener('submit', async (event) => {
      event.preventDefault();
      try { await post('user_create', Object.fromEntries(new FormData(event.currentTarget))); location.reload(); }
      catch (error) { showMessage(error.message, true); }
    });
    root.querySelector('#catalogForm').addEventListener('submit', async (event) => {
      event.preventDefault();
      const values = Object.fromEntries(new FormData(event.currentTarget));
      try { await post('catalog_create', values); location.reload(); }
      catch (error) { showMessage(error.message, true); }
    });
    root.querySelectorAll('[data-role-id]').forEach((checkbox) => checkbox.addEventListener('change', async () => {
      try { await post('permission_set', { role_id: checkbox.dataset.roleId, permission_id: checkbox.dataset.permissionId, allowed: checkbox.checked }); showMessage('Permiso actualizado.'); }
      catch (error) { checkbox.checked = !checkbox.checked; showMessage(error.message, true); }
    }));
    root.querySelectorAll('[data-catalog-id]').forEach((button) => button.addEventListener('click', async () => {
      try { await post('catalog_status', { id: button.dataset.catalogId, active: button.dataset.active }); location.reload(); }
      catch (error) { showMessage(error.message, true); }
    }));
    root.querySelectorAll('[data-user-id]').forEach((button) => button.addEventListener('click', async () => {
      try { await post('user_status', { user_id: button.dataset.userId, active: button.dataset.active }); location.reload(); }
      catch (error) { showMessage(error.message, true); }
    }));
  })();
</script>