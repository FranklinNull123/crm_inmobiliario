<?php
/** @var array $reportesResumen */
/** @var array $reportesVentasMes */
/** @var array $reportesInventario */
/** @var array $reportesTopProyectos */

$ventasTotales = (float) ($reportesResumen['ventas_totales'] ?? 0);
$leadsActivos = (int) ($reportesResumen['leads_activos'] ?? 0);
$unidadesDisponibles = (int) ($reportesResumen['unidades_disponibles'] ?? 0);
$cuotasPendientes = (float) ($reportesResumen['cuotas_pendientes'] ?? 0);
$ventasCount = (int) ($reportesResumen['ventas_count'] ?? 0);
?>
<div id="reportesView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Reportes</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Resumen comercial y operativo</h1>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button type="button" data-export="ventas" class="report-export-btn inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-brand-500 hover:text-brand-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <i data-lucide="download" class="h-4 w-4"></i>
          Exportar ventas
        </button>
        <button type="button" data-export="inventario" class="report-export-btn inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-brand-500 hover:text-brand-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <i data-lucide="download" class="h-4 w-4"></i>
          Exportar inventario
        </button>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Ventas cerradas</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">S/ <?= number_format($ventasTotales, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400"><?= $ventasCount ?> operaciones registradas</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cuotas pendientes</p>
      <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400">S/ <?= number_format($cuotasPendientes, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Cobranza activa</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Leads activos</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400"><?= $leadsActivos ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Seguimiento del pipeline</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Unidades disponibles</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= $unidadesDisponibles ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Inventario disponible</p>
    </article>
  </div>

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 xl:col-span-2">
      <div class="mb-5 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Ventas por mes</h2>
        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">Monto acumulado</span>
      </div>

      <div class="space-y-3">
        <?php if ($reportesVentasMes === []): ?>
          <div class="rounded-lg border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">No hay ventas registradas para mostrar.</div>
        <?php else: ?>
          <?php
          $maxVenta = 0.0;
          foreach ($reportesVentasMes as $mes) {
              $maxVenta = max($maxVenta, (float) ($mes['total_ventas'] ?? 0));
          }
          ?>
          <?php foreach ($reportesVentasMes as $mes): ?>
            <?php $valor = (float) ($mes['total_ventas'] ?? 0); $pct = $maxVenta > 0 ? ($valor / $maxVenta) * 100 : 0; ?>
            <div>
              <div class="mb-1.5 flex items-center justify-between text-sm">
                <span class="font-medium text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($mes['mes'] ?? 'Mes'), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="font-semibold text-slate-900 dark:text-white">S/ <?= number_format($valor, 2) ?></span>
              </div>
              <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full rounded-full bg-brand-600" style="width: <?= min(100, $pct) ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Inventario por estado</h2>
      <div class="mt-5 space-y-4">
        <?php if ($reportesInventario === []): ?>
          <p class="text-sm text-slate-500 dark:text-slate-400">Sin datos de inventario.</p>
        <?php else: ?>
          <?php $inventarioTotal = array_sum(array_map(static fn (array $item): int => (int) ($item['total'] ?? 0), $reportesInventario)); ?>
          <?php foreach ($reportesInventario as $estado): ?>
            <?php $total = (int) ($estado['total'] ?? 0); $pct = $inventarioTotal > 0 ? ($total / $inventarioTotal) * 100 : 0; ?>
            <div>
              <div class="mb-1.5 flex items-center justify-between text-sm">
                <span class="text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($estado['estado'] ?? 'Sin estado'), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="font-medium text-slate-900 dark:text-white"><?= $total ?></span>
              </div>
              <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full rounded-full bg-emerald-500" style="width: <?= min(100, $pct) ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Proyectos con mayor volumen</h2>
      <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Top 5</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Proyecto</th>
            <th class="pb-3 pr-4 font-semibold">Ventas</th>
            <th class="pb-3 font-semibold">Monto</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($reportesTopProyectos === []): ?>
            <tr>
              <td colspan="3" class="py-6 text-center text-slate-500 dark:text-slate-400">No hay proyectos con ventas registradas.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($reportesTopProyectos as $proyecto): ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($proyecto['proyecto'] ?? 'Proyecto'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($proyecto['ventas'] ?? 0) ?></td>
                <td class="py-3 text-slate-600 dark:text-slate-300">S/ <?= number_format((float) ($proyecto['total_ventas'] ?? 0), 2) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
