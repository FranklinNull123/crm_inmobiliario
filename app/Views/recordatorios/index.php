<?php
/** @var array $recordatorios */
?>
<div id="recordatoriosView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 10</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Recordatorios y seguimiento</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-500/10 dark:text-emerald-300">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
        Agenda comercial
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Nuevo recordatorio</h2>

      <form id="recordatorioForm" class="mt-5 grid gap-4 md:grid-cols-2">
        <label class="md:col-span-2">
          <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Título</span>
          <input name="titulo" required maxlength="120" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="Confirmar visita con cliente" />
        </label>

        <label>
          <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Tipo</span>
          <select name="tipo" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
            <option>Lead</option>
            <option>Cliente</option>
            <option>Venta</option>
            <option>General</option>
          </select>
        </label>

        <label>
          <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Prioridad</span>
          <select name="prioridad" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
            <option value="alta">Alta</option>
            <option value="media" selected>Media</option>
            <option value="baja">Baja</option>
          </select>
        </label>

        <label>
          <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Fecha</span>
          <input name="fecha_programada" type="date" required class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950" />
        </label>

        <label class="md:col-span-2">
          <span class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Descripción</span>
          <textarea name="descripcion" rows="4" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm dark:border-slate-700 dark:bg-slate-950" placeholder="Detalle del seguimiento, llamada o documento a revisar."></textarea>
        </label>

        <div class="md:col-span-2 flex justify-end">
          <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Guardar recordatorio
          </button>
        </div>
      </form>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Resumen</h2>
      <div class="mt-5 space-y-3">
        <div class="rounded-xl bg-sky-50 px-4 py-3 dark:bg-sky-500/10">
          <p class="text-xs uppercase tracking-[0.2em] text-sky-700 dark:text-sky-300">Próximos</p>
          <p class="mt-2 text-2xl font-bold text-sky-700 dark:text-sky-200"><?= count($recordatorios) ?></p>
        </div>
        <div class="rounded-xl bg-amber-50 px-4 py-3 dark:bg-amber-500/10">
          <p class="text-xs uppercase tracking-[0.2em] text-amber-700 dark:text-amber-300">Prioridad alta</p>
          <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-200"><?= count(array_filter($recordatorios, static fn (array $item): bool => (string) ($item['prioridad'] ?? '') === 'alta')) ?></p>
        </div>
      </div>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Agenda de seguimiento</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($recordatorios) ?> registros</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Título</th>
            <th class="pb-3 pr-4 font-semibold">Tipo</th>
            <th class="pb-3 pr-4 font-semibold">Prioridad</th>
            <th class="pb-3 pr-4 font-semibold">Fecha</th>
            <th class="pb-3 font-semibold">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($recordatorios === []): ?>
            <tr>
              <td colspan="5" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay recordatorios creados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($recordatorios as $recordatorio): ?>
              <tr>
                <td class="py-3 pr-4">
                  <p class="font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($recordatorio['titulo'] ?? 'Recordatorio'), ENT_QUOTES, 'UTF-8') ?></p>
                  <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?= htmlspecialchars((string) ($recordatorio['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($recordatorio['tipo'] ?? 'General'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                    <?= (string) ($recordatorio['prioridad'] ?? 'media') === 'alta' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : ((string) ($recordatorio['prioridad'] ?? 'media') === 'baja' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300') ?>">
                    <?= htmlspecialchars((string) ($recordatorio['prioridad'] ?? 'media'), ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($recordatorio['fecha_programada'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3">
                  <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                    <?= htmlspecialchars((string) ($recordatorio['estado'] ?? 'pendiente'), ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
