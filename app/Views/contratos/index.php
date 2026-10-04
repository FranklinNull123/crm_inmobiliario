<?php
/** @var array $contratos */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="contratosView" class="space-y-6">
  <div id="contratoMessage" class="hidden rounded-md border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

  <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-5 flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Contratos y legal</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gestiona contratos, firmas y documentación legal del proceso de venta.</p>
      </div>
      <button id="newContractBtn" type="button" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">
        <i data-lucide="plus" class="h-4 w-4"></i>Nuevo contrato
      </button>
    </div>

    <form id="contractForm" class="grid gap-4 lg:grid-cols-4 hidden rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
      <label class="text-sm font-medium text-slate-700 dark:text-slate-200 lg:col-span-2">
        Cliente
        <select name="cliente_id" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
          <option value="">Selecciona</option>
          <?php foreach ($contratosClientes ?? [] as $cliente): ?>
            <option value="<?= (int) ($cliente['id'] ?? 0) ?>"><?= $escape(($cliente['nombre'] ?? '')) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label class="text-sm font-medium text-slate-700 dark:text-slate-200 lg:col-span-1">
        Nombre del contrato
        <input name="nombre" type="text" placeholder="Contrato - Cliente" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>

      <div class="flex items-end">
        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-md bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Guardar</button>
      </div>
    </form>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Documentos legales</h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800 dark:text-slate-400">
          <tr>
            <th class="px-4 py-3">Cliente</th>
            <th class="px-4 py-3">Proyecto</th>
            <th class="px-4 py-3">Unidad</th>
            <th class="px-4 py-3">Contrato</th>
            <th class="px-4 py-3">Tipo</th>
            <th class="px-4 py-3">Fecha</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($contratos as $contrato): ?>
            <tr>
              <td class="px-4 py-3 font-medium text-slate-900 dark:text-white"><?= $escape($contrato['cliente'] ?? '') ?></td>
              <td class="px-4 py-3"><?= $escape($contrato['proyecto'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= $escape($contrato['unidad'] ?? '—') ?></td>
              <td class="px-4 py-3"><?= $escape($contrato['contrato'] ?? 'Contrato') ?></td>
              <td class="px-4 py-3"><?= $escape(strtoupper((string) ($contrato['tipo'] ?? 'contract'))) ?></td>
              <td class="px-4 py-3"><?= $escape($contrato['fecha_venta'] ?? date('Y-m-d')) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($contratos)): ?>
            <tr>
              <td colspan="6" class="px-4 py-10 text-center text-slate-500">No hay contratos registrados.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
