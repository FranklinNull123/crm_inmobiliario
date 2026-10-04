<?php
/** @var array $ventas */
/** @var array $saleClients */
/** @var array $saleUnits */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="salesView" class="space-y-8">
  <div id="saleMessage" class="hidden rounded-md border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

  <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-5">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Ventas</h1>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Registra ventas y genera el cronograma de cuotas del cliente.</p>
    </div>

    <form id="saleForm" class="grid gap-4 lg:grid-cols-5">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
        Cliente
        <select name="cliente_id" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
          <option value="">Selecciona</option>
          <?php foreach ($saleClients as $client): ?>
            <option value="<?= (int) ($client['id'] ?? 0) ?>"><?= $escape(($client['nombre'] ?? '') . ' · ' . ($client['dni'] ?? '')) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
        Unidad
        <select name="unidad_id" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
          <option value="">Selecciona</option>
          <?php foreach ($saleUnits as $unit): ?>
            <option value="<?= (int) ($unit['id'] ?? 0) ?>" data-price="<?= (float) ($unit['precio'] ?? 0) ?>"><?= $escape(($unit['proyecto'] ?? '') . ' · ' . ($unit['codigo'] ?? '')) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
        Monto total
        <input name="monto_total" type="number" min="1" step="0.01" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>

      <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
        Cuotas
        <input name="cuotas" type="number" min="1" max="120" value="12" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>

      <label class="text-sm font-medium text-slate-700 dark:text-slate-200">
        Fecha
        <input name="fecha" type="date" value="<?= date('Y-m-d') ?>" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>

      <div class="lg:col-span-5 flex justify-end">
        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">Registrar venta</button>
      </div>
    </form>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Ventas registradas</h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400">
          <tr>
            <th class="px-4 py-3">Cliente</th>
            <th class="px-4 py-3">Proyecto</th>
            <th class="px-4 py-3">Unidad</th>
            <th class="px-4 py-3">Fecha</th>
            <th class="px-4 py-3">Cuotas</th>
            <th class="px-4 py-3">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($ventas as $venta): ?>
            <tr>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white"><?= $escape($venta['cliente'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($venta['proyecto'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($venta['unidad'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($venta['fecha'] ?? '') ?></td>
              <td class="px-4 py-3"><?= (int) ($venta['cuotas'] ?? 0) ?></td>
              <td class="px-4 py-3"><span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300"><?= $escape($venta['estado'] ?? 'Vendido') ?></span></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($ventas === []): ?>
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-slate-500">Aún no hay ventas registradas.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
