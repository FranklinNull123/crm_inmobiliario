<?php
/** @var array $carteraResumen */
/** @var array $cartera */

$clientesActivos = (int) ($carteraResumen['clientes_activos'] ?? 0);
$carteraTotal = (int) ($carteraResumen['cartera_total'] ?? 0);
$deudaPendiente = (float) ($carteraResumen['deuda_pendiente'] ?? 0);
$oportunidadesAbiertas = (int) ($carteraResumen['oportunidades_abiertas'] ?? 0);
?>
<div id="carteraView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 14</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Cartera activa</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 dark:border-brand-900/60 dark:bg-brand-500/10 dark:text-brand-300">
        <span class="h-2 w-2 rounded-full bg-brand-500"></span>
        Seguimiento comercial
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Clientes activos</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white"><?= $clientesActivos ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Base de clientes</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cartera total</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400"><?= $carteraTotal ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Clientes en gestión</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Deuda pendiente</p>
      <p class="mt-3 text-3xl font-bold text-rose-600 dark:text-rose-400">S/ <?= number_format($deudaPendiente, 2) ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Cobranza abierta</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Oportunidades</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= $oportunidadesAbiertas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pipeline vigente</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Clientes y deuda</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($cartera) ?> registros</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Cliente</th>
            <th class="pb-3 pr-4 font-semibold">Asesor</th>
            <th class="pb-3 pr-4 font-semibold">Unidad</th>
            <th class="pb-3 pr-4 font-semibold">Valor</th>
            <th class="pb-3 pr-4 font-semibold">Deuda</th>
            <th class="pb-3 pr-4 font-semibold">Próximo vencimiento</th>
            <th class="pb-3 font-semibold">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($cartera === []): ?>
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay cartera activa registrada.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($cartera as $item): ?>
              <?php $estado = strtolower((string) ($item['estado'] ?? 'activa')); ?>
              <?php $stateClass = match ($estado) {
                  'en mora' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
                  'activa' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                  default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
              }; ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($item['cliente'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($item['asesor'] ?? 'Sin asignar'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($item['unidad'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">S/ <?= number_format((float) ($item['valor_unidad'] ?? 0), 2) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">S/ <?= number_format((float) ($item['deuda_total'] ?? 0), 2) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= $item['proximo_vencimiento'] ? htmlspecialchars((string) $item['proximo_vencimiento'], ENT_QUOTES, 'UTF-8') : '—' ?></td>
                <td class="py-3">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $stateClass ?>"><?= ucfirst((string) ($item['estado'] ?? 'Activa')) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
