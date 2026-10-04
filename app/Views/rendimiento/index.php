<?php
/** @var array $rendimientoResumen */
/** @var array $rendimiento */

$metaMensual = (float) ($rendimientoResumen['meta_mensual'] ?? 0);
$ventasActuales = (float) ($rendimientoResumen['ventas_actuales'] ?? 0);
$ventasCount = (int) ($rendimientoResumen['ventas_count'] ?? 0);
$cumplimiento = (float) ($rendimientoResumen['cumplimiento'] ?? 0);
?>
<div id="rendimientoView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 15</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rendimiento por asesor</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-500/10 dark:text-emerald-300">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        Meta comercial
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Meta mensual</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">S/ <?= number_format($metaMensual, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Objetivo del período</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Ventas actuales</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400">S/ <?= number_format($ventasActuales, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400"><?= $ventasCount ?> operaciones</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cumplimiento</p>
      <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400"><?= number_format($cumplimiento, 1) ?>%</p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Vs. objetivo</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Asesores activos</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= count($rendimiento) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Con cartera asignada</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Desempeño por asesor</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($rendimiento) ?> registros</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Asesor</th>
            <th class="pb-3 pr-4 font-semibold">Clientes</th>
            <th class="pb-3 pr-4 font-semibold">Ventas</th>
            <th class="pb-3 pr-4 font-semibold">Volumen</th>
            <th class="pb-3 font-semibold">Cumplimiento</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($rendimiento === []): ?>
            <tr>
              <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay información de rendimiento disponible.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($rendimiento as $item): ?>
              <?php $compliance = (float) ($item['cumplimiento'] ?? 0); ?>
              <?php $barClass = $compliance >= 100 ? 'bg-emerald-500' : ($compliance >= 70 ? 'bg-amber-500' : 'bg-rose-500'); ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($item['asesor'] ?? 'Sin asignar'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($item['clientes'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($item['ventas'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">S/ <?= number_format((float) ($item['volumen'] ?? 0), 2) ?></td>
                <td class="py-3">
                  <div class="flex items-center gap-3">
                    <div class="h-2.5 w-28 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                      <div class="h-full rounded-full <?= $barClass ?>" style="width: <?= min(max($compliance, 0), 100) ?>%"></div>
                    </div>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200"><?= number_format($compliance, 1) ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
