<?php
/** @var array $soporteResumen */
/** @var array $soporteEventos */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="soporteView" class="space-y-6">
  <section class="grid gap-4 md:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Eventos totales</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($soporteResumen['total_eventos'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Hoy</p>
      <p class="mt-2 text-2xl font-bold text-sky-600 dark:text-sky-400"><?= (int) ($soporteResumen['eventos_hoy'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Usuarios activos</p>
      <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400"><?= (int) ($soporteResumen['usuarios_activos'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Eventos críticos</p>
      <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400"><?= (int) ($soporteResumen['eventos_criticos'] ?? 0) ?></p>
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-4 md:flex-row md:items-center md:justify-between dark:border-slate-800">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Soporte y auditoría</h1>
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
        <span>Buscar</span>
        <input id="soporteSearch" type="search" placeholder="Evento, usuario o entidad" class="w-52 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 focus:border-brand-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200" />
      </label>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400">
          <tr>
            <th class="px-4 py-3">Fecha</th>
            <th class="px-4 py-3">Evento</th>
            <th class="px-4 py-3">Entidad</th>
            <th class="px-4 py-3">Usuario</th>
            <th class="px-4 py-3">IP</th>
            <th class="px-4 py-3">Dispositivo</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($soporteEventos as $evento): ?>
            <tr class="soporte-row">
              <td class="px-4 py-3 whitespace-nowrap text-slate-500 dark:text-slate-400"><?= $escape($evento['creado_en'] ?? '') ?></td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2 py-1 text-xs font-medium <?= stripos((string) ($evento['evento'] ?? ''), 'fallido') !== false || stripos((string) ($evento['evento'] ?? ''), 'error') !== false || stripos((string) ($evento['evento'] ?? ''), 'rechazado') !== false ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' ?>">
                  <?= $escape($evento['evento'] ?? '') ?>
                </span>
              </td>
              <td class="px-4 py-3"><?= $escape(($evento['entidad'] ?? '') . (!empty($evento['entidad_id']) ? ' #' . $evento['entidad_id'] : '')) ?></td>
              <td class="px-4 py-3"><?= $escape($evento['usuario'] ?? 'Sistema') ?></td>
              <td class="px-4 py-3 font-mono text-xs text-slate-500 dark:text-slate-400"><?= $escape($evento['ip'] ?? '') ?></td>
              <td class="px-4 py-3 max-w-[220px] truncate text-slate-500 dark:text-slate-400" title="<?= $escape($evento['dispositivo'] ?? '') ?>"><?= $escape($evento['dispositivo'] ?? 'N/D') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($soporteEventos)): ?>
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">No hay eventos registrados todavía.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
