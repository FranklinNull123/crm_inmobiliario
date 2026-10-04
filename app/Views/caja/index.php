<?php
/** @var array $cajaCuotas */
/** @var array $cajaSummary */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="cajaView" class="space-y-6">
  <div id="cajaMessage" class="hidden rounded-md border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

  <section class="grid gap-4 md:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Por cobrar</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">S/ <?= number_format((float) ($cajaSummary['pendiente_total'] ?? 0), 2) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cuotas pendientes</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($cajaSummary['cuotas_pendientes'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Pagadas</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($cajaSummary['cuotas_pagadas'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Vencidas</p>
      <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">S/ <?= number_format((float) ($cajaSummary['vencido_total'] ?? 0), 2) ?></p>
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Caja y cobranza</h1>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[860px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400">
          <tr>
            <th class="px-4 py-3">Cliente</th>
            <th class="px-4 py-3">Proyecto</th>
            <th class="px-4 py-3">Unidad</th>
            <th class="px-4 py-3">Cuota</th>
            <th class="px-4 py-3">Monto</th>
            <th class="px-4 py-3">Vencimiento</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($cajaCuotas as $cuota): ?>
            <tr>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white"><?= $escape($cuota['cliente'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($cuota['proyecto'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= $escape($cuota['unidad'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= (int) ($cuota['numero_cuota'] ?? 0) ?></td>
              <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">S/ <?= number_format((float) ($cuota['monto'] ?? 0), 2) ?></td>
              <td class="px-4 py-3"><?= $escape($cuota['fecha_vencimiento'] ?? '') ?></td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2 py-1 text-xs font-medium <?= ($cuota['pagada'] ?? false) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : (($cuota['estado'] ?? '') === 'Vencida' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300') ?>">
                  <?= $escape($cuota['estado'] ?? 'Pendiente') ?>
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <?php if (!($cuota['pagada'] ?? false)): ?>
                  <button type="button" class="mark-paid-btn inline-flex items-center justify-center rounded-md bg-brand-600 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-700" data-cuota-id="<?= (int) ($cuota['id'] ?? 0) ?>">
                    Marcar cobrada
                  </button>
                <?php else: ?>
                  <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Cobrado</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ($cajaCuotas === []): ?>
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-slate-500">No hay cuotas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
