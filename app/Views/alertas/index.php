<?php
/** @var array $alertasResumen */
/** @var array $alertas */

$leadsActivos = (int) ($alertasResumen['leads_activos'] ?? 0);
$cuotasVencidas = (int) ($alertasResumen['cuotas_vencidas'] ?? 0);
$separacionesProximas = (int) ($alertasResumen['separaciones_proximas'] ?? 0);
$unidadesBloqueadas = (int) ($alertasResumen['unidades_bloqueadas'] ?? 0);
?>
<div id="alertasView" class="space-y-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Fase 13</p>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Alertas y seguimiento</h1>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:border-amber-900/60 dark:bg-amber-500/10 dark:text-amber-300">
        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
        Operación en tiempo real
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Leads activos</p>
      <p class="mt-3 text-3xl font-bold text-sky-600 dark:text-sky-400"><?= $leadsActivos ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Pipeline abierto</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Cuotas vencidas</p>
      <p class="mt-3 text-3xl font-bold text-rose-600 dark:text-rose-400"><?= $cuotasVencidas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Recaudo pendiente</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Separaciones próximas</p>
      <p class="mt-3 text-3xl font-bold text-amber-600 dark:text-amber-400"><?= $separacionesProximas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Próximos 7 días</p>
    </article>

    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <p class="text-sm text-slate-500 dark:text-slate-400">Unidades bloqueadas</p>
      <p class="mt-3 text-3xl font-bold text-violet-600 dark:text-violet-400"><?= $unidadesBloqueadas ?></p>
      <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Reservadas o separadas</p>
    </article>
  </div>

  <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="mb-4 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Prioridades del negocio</h2>
      <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300"><?= count($alertas) ?> eventos</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-slate-200 text-slate-500 dark:border-slate-700 dark:text-slate-400">
          <tr>
            <th class="pb-3 pr-4 font-semibold">Tipo</th>
            <th class="pb-3 pr-4 font-semibold">Descripción</th>
            <th class="pb-3 pr-4 font-semibold">Valor</th>
            <th class="pb-3 font-semibold">Prioridad</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
          <?php if ($alertas === []): ?>
            <tr>
              <td colspan="4" class="py-8 text-center text-slate-500 dark:text-slate-400">No hay alertas activas en este momento.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($alertas as $alerta): ?>
              <?php $prioridad = strtolower((string) ($alerta['prioridad'] ?? 'media')); ?>
              <?php $badgeClass = match ($prioridad) {
                  'alta' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300',
                  'media' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300',
                  default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
              }; ?>
              <tr>
                <td class="py-3 pr-4 font-medium text-slate-900 dark:text-white"><?= htmlspecialchars((string) ($alerta['titulo'] ?? 'Alerta'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars((string) ($alerta['detalle'] ?? 'Sin detalle'), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="py-3 pr-4 text-slate-600 dark:text-slate-300">
                  <?php if (is_numeric($alerta['valor'] ?? null)): ?>
                    <?php
                    $valor = (float) ($alerta['valor'] ?? 0);
                    echo $valor >= 1000 ? 'S/ ' . number_format($valor, 2) : number_format($valor, 0);
                    ?>
                  <?php else: ?>
                    <?= htmlspecialchars((string) ($alerta['valor'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                  <?php endif; ?>
                </td>
                <td class="py-3">
                  <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $badgeClass ?>"><?= ucfirst($prioridad) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </article>
</div>
