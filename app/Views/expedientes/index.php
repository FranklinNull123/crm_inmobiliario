<?php
/** @var array $expedientesResumen */
/** @var array $expedientes */

$expedientesTotales = (int) ($expedientesResumen['expedientes_totales'] ?? 0);
$conDocumentos = (int) ($expedientesResumen['con_documentos'] ?? 0);
$pendientesFirma = (int) ($expedientesResumen['pendientes_firma'] ?? 0);
$contratosActivos = (int) ($expedientesResumen['contratos_activos'] ?? 0);
?>
<div id="expedientesView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 16</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Expedientes legales</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 dark:border-indigo-900/60 dark:bg-indigo-500/10 dark:text-indigo-300">
        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
        Firma y documentación
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Expedientes</p>
      <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white"><?= $expedientesTotales ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Clientes con documentos</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Con documentos</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400"><?= $conDocumentos ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Listos para seguimiento</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Pendientes de firma</p>
      <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400"><?= $pendientesFirma ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Revisión requerida</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Contratos activos</p>
      <p class="mt-3 text-3xl font-bold text-emerald-600 dark:text-emerald-400"><?= $contratosActivos ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Documentos legales</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Estado de expedientes</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($expedientes) ?> registros</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Cliente</th>
            <th class="pb-3 pr-4 font-semibold">Asesor</th>
            <th class="pb-3 pr-4 font-semibold">Proyecto</th>
            <th class="pb-3 pr-4 font-semibold">Unidad</th>
            <th class="pb-3 pr-4 font-semibold">Documentos</th>
            <th class="pb-3 pr-4 font-semibold">Contrato</th>
            <th class="pb-3 font-semibold">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($expedientes === []): ?>
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay expedientes legales registrados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($expedientes as $expediente): ?>
              <?php $estado = strtolower((string) ($expediente['estado'] ?? 'pendiente')); ?>
              <?php $stateClass = match ($estado) {
                  'listo para firma' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                  'en revisión' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
                  default => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
              }; ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($expediente['cliente'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($expediente['asesor'] ?? 'Sin asignar'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($expediente['proyecto'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($expediente['unidad'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= (int) ($expediente['documentos'] ?? 0) ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($expediente['contrato_actual'] ?? 'Sin contrato'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $stateClass ?>"><?= htmlspecialchars((string) ($expediente['estado'] ?? 'Pendiente'), ENT_QUOTES, 'UTF-8') ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
