<?php
/** @var array $pipelineResumen */
/** @var array $pipeline */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="pipelineView" class="space-y-6">
  <section class="grid gap-4 md:grid-cols-5">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Leads totales</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($pipelineResumen['total_leads'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Oportunidades</p>
      <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400"><?= (int) ($pipelineResumen['oportunidades'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cerradas</p>
      <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400"><?= (int) ($pipelineResumen['cerradas'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Ventas del mes</p>
      <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400">S/ <?= number_format((float) ($pipelineResumen['ventas_mes'] ?? 0), 2) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Conversión</p>
      <p class="mt-2 text-2xl font-bold text-sky-600 dark:text-sky-400"><?= number_format((float) ($pipelineResumen['conversion'] ?? 0), 1) ?>%</p>
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-4 md:flex-row md:items-center md:justify-between dark:border-slate-800">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 24</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Pipeline de ventas</h1>
      </div>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($pipeline) ?> registros</span>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-800">
      <?php foreach ($pipeline as $item): ?>
        <article class="flex flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-full <?= ($item['prioridad'] ?? 'bajo') === 'alto' || ($item['prioridad'] ?? 'bajo') === 'urgente' ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300' : (($item['prioridad'] ?? 'bajo') === 'medio' ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300') ?>">
              <i data-lucide="route" class="h-4 w-4"></i>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= $escape($item['etapa'] ?? 'Lead') ?></span>
                <span class="text-xs text-slate-500 dark:text-slate-400"><?= $escape($item['campana'] ?? 'Sin campaña') ?></span>
              </div>
              <h2 class="mt-1 text-base font-semibold text-slate-900 dark:text-white"><?= $escape($item['titulo'] ?? 'Lead') ?></h2>
              <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><?= $escape($item['asesor'] ?? 'Sin asignar') ?> · <?= $escape($item['canal'] ?? 'Sin canal') ?></p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="rounded-full px-2 py-1 text-xs font-medium <?= ($item['prioridad'] ?? 'bajo') === 'alto' || ($item['prioridad'] ?? 'bajo') === 'urgente' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : (($item['prioridad'] ?? 'bajo') === 'medio' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300') ?>">
              <?= $escape(strtoupper((string) ($item['prioridad'] ?? 'BAJO'))) ?>
            </span>
            <span class="text-sm font-semibold text-slate-900 dark:text-white"><?= $escape($item['nivel'] ?? 'Bajo') ?></span>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (empty($pipeline)): ?>
        <div class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No hay registros en el pipeline actual.</div>
      <?php endif; ?>
    </div>
  </section>
</div>
