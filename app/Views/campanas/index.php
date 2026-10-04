<?php
/** @var array $campanasResumen */
/** @var array $campanas */

$totalCampanas = (int) ($campanasResumen['total_campanas'] ?? 0);
$totalProspeccion = (int) ($campanasResumen['prospeccion_total'] ?? 0);
$ventasCerradas = (int) ($campanasResumen['ventas_cerradas'] ?? 0);
$campanasActivas = (int) ($campanasResumen['campanas_activas'] ?? 0);
?>
<div id="campanasView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 12</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Campañas y canales</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-violet-200 bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700 dark:border-violet-900/60 dark:bg-violet-500/10 dark:text-violet-300">
        <span class="h-2 w-2 rounded-full bg-violet-500"></span>
        Performance comercial
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Campañas activas</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white"><?= $campanasActivas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Estrategias en curso</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Total de leads</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400"><?= $totalCampanas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Registros capturados</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Prospección</p>
      <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400"><?= $totalProspeccion ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Leads en proceso</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Ventas cerradas</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= $ventasCerradas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Conversión efectiva</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Métricas por campaña</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($campanas) ?> registros</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Campaña</th>
            <th class="pb-3 pr-4 font-semibold">Canal</th>
            <th class="pb-3 pr-4 font-semibold">Leads</th>
            <th class="pb-3 pr-4 font-semibold">Nuevos</th>
            <th class="pb-3 pr-4 font-semibold">Contactados</th>
            <th class="pb-3 pr-4 font-semibold">Visitas</th>
            <th class="pb-3 pr-4 font-semibold">Negociación</th>
            <th class="pb-3 font-semibold">Cerradas</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($campanas === []): ?>
            <tr>
              <td colspan="8" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay campañas registradas todavía.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($campanas as $campana): ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($campana['nombre'] ?? 'Campaña'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($campana['canal'] ?? 'Sin canal'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($campana['leads'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($campana['nuevos'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($campana['contactados'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($campana['visitas'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($campana['negociacion'] ?? 0) ?></td>
                <td class="py-3 font-semibold text-emerald-600 dark:text-emerald-400"><?= (int) ($campana['ventas_cerradas'] ?? 0) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
