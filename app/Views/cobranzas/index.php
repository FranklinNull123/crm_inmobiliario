<?php
/** @var array $cobranzasResumen */
/** @var array $cobranzas */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="cobranzasView" class="space-y-6">
  <section class="grid gap-4 md:grid-cols-4">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Por cobrar</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">S/ <?= number_format((float) ($cobranzasResumen['pendiente_total'] ?? 0), 2) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cuotas pendientes</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($cobranzasResumen['cuotas_pendientes'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Vencidas</p>
      <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400"><?= (int) ($cobranzasResumen['vencidas'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Clientes con cartera</p>
      <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400"><?= (int) ($cobranzasResumen['clientes_con_cartera'] ?? 0) ?></p>
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-4 md:flex-row md:items-center md:justify-between dark:border-slate-800">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Cobranza</h1>
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
        <span>Filtrar</span>
        <select id="cobranzaFilter" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-sm text-slate-700 focus:border-brand-500 focus:outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
          <option value="all">Todas</option>
          <option value="Vencida">Vencidas</option>
          <option value="Pendiente">Pendientes</option>
        </select>
      </label>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400">
          <tr>
            <th class="px-4 py-3">Cliente</th>
            <th class="px-4 py-3">Proyecto</th>
            <th class="px-4 py-3">Unidad</th>
            <th class="px-4 py-3">Cuota</th>
            <th class="px-4 py-3">Monto</th>
            <th class="px-4 py-3">Vencimiento</th>
            <th class="px-4 py-3">Días</th>
            <th class="px-4 py-3">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($cobranzas as $item): ?>
            <tr class="cobranza-row" data-status="<?= $escape($item['estado'] ?? 'Pendiente') ?>">
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white"><?= $escape($item['cliente'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($item['proyecto'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= $escape($item['unidad'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= (int) ($item['numero_cuota'] ?? 0) ?></td>
              <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">S/ <?= number_format((float) ($item['monto'] ?? 0), 2) ?></td>
              <td class="px-4 py-3"><?= $escape($item['fecha_vencimiento'] ?? '') ?></td>
              <td class="px-4 py-3"><?= (int) ($item['dias_vencidos'] ?? 0) ?> días</td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2 py-1 text-xs font-medium <?= ($item['estado'] ?? 'Pendiente') === 'Vencida' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' ?>">
                  <?= $escape($item['estado'] ?? 'Pendiente') ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($cobranzas)): ?>
            <tr>
              <td colspan="8" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">No hay cuotas pendientes de cobro.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
