<?php
/** @var array $comisionesResumen */
/** @var array $comisiones */

$totalVentas = (int) ($comisionesResumen['ventas_count'] ?? 0);
$totalVolumen = (float) ($comisionesResumen['volumen_total'] ?? 0);
$totalComisiones = (float) ($comisionesResumen['comision_total'] ?? 0);
?>
<div id="comisionesView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 11</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Comisiones por asesor</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-500/10 dark:text-emerald-300">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        Liquidación comercial
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Ventas cerradas</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white"><?= $totalVentas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Operaciones registradas</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Volumen vendido</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400">S/ <?= number_format($totalVolumen, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Monto total en ventas</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Comisión proyectada</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400">S/ <?= number_format($totalComisiones, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Base 2% por operación</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Ranking de asesores</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($comisiones) ?> asesores</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Asesor</th>
            <th class="pb-3 pr-4 font-semibold">Ventas</th>
            <th class="pb-3 pr-4 font-semibold">Volumen</th>
            <th class="pb-3 pr-4 font-semibold">Comisión</th>
            <th class="pb-3 font-semibold">Última venta</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($comisiones === []): ?>
            <tr>
              <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay ventas asociadas a asesores para calcular comisiones.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($comisiones as $item): ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($item['asesor'] ?? 'Sin asignar'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($item['ventas'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">S/ <?= number_format((float) ($item['volumen'] ?? 0), 2) ?></td>
                <td class="py-3 pr-4 font-semibold text-emerald-600 dark:text-emerald-400">S/ <?= number_format((float) ($item['comision'] ?? 0), 2) ?></td>
                <td class="py-3 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($item['ultima_venta'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
