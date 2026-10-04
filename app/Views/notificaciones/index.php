<?php
/** @var array $notificacionesResumen */
/** @var array $notificaciones */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="notificacionesView" class="space-y-6">
  <section class="grid gap-4 md:grid-cols-5">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Total</p>
      <p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white"><?= (int) ($notificacionesResumen['total'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Alertas</p>
      <p class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400"><?= (int) ($notificacionesResumen['alertas'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Recordatorios</p>
      <p class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400"><?= (int) ($notificacionesResumen['recordatorios'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Campañas</p>
      <p class="mt-2 text-2xl font-bold text-sky-600 dark:text-sky-400"><?= (int) ($notificacionesResumen['campanas'] ?? 0) ?></p>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cobros pendientes</p>
      <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400"><?= (int) ($notificacionesResumen['pendientes'] ?? 0) ?></p>
    </div>
  </section>

  <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Notificaciones y seguimiento</h1>
    </div>

    <div class="divide-y divide-slate-100 dark:divide-slate-800">
      <?php foreach ($notificaciones as $item): ?>
        <article class="flex flex-col gap-3 px-6 py-4 md:flex-row md:items-center md:justify-between">
          <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full <?= ($item['prioridad'] ?? 'Normal') === 'Alta' ? 'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300' : (($item['prioridad'] ?? 'Normal') === 'Media' ? 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300') ?>">
              <i data-lucide="bell" class="h-4 w-4"></i>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= $escape($item['tipo'] ?? 'General') ?></span>
                <span class="text-xs text-slate-500 dark:text-slate-400"><?= $escape($item['fecha'] ?? '') ?></span>
              </div>
              <h2 class="mt-1 text-base font-semibold text-slate-900 dark:text-white"><?= $escape($item['titulo'] ?? 'Notificación') ?></h2>
              <p class="mt-1 text-sm text-slate-600 dark:text-slate-300"><?= $escape($item['detalle'] ?? '') ?></p>
            </div>
          </div>
          <span class="rounded-full px-2 py-1 text-xs font-medium <?= ($item['prioridad'] ?? 'Normal') === 'Alta' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : (($item['prioridad'] ?? 'Normal') === 'Media' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300') ?>">
            <?= $escape($item['prioridad'] ?? 'Normal') ?>
          </span>
        </article>
      <?php endforeach; ?>
      <?php if (empty($notificaciones)): ?>
        <div class="px-6 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No hay notificaciones activas en este momento.</div>
      <?php endif; ?>
    </div>
  </section>
</div>
