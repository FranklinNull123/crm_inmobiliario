<?php
declare(strict_types=1);
/** @var list<array<string, mixed>> $auditLogs */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<section id="auditView" class="space-y-4">
  <div class="flex flex-wrap items-end justify-between gap-3">
    <div><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Trazabilidad del sistema</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Eventos recientes de acceso y cambios registrados.</p></div>
    <span class="text-xs text-slate-500 dark:text-slate-400">Máximo 200 eventos</span>
  </div>
  <div class="overflow-x-auto rounded-md border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <table class="w-full min-w-[850px] text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800"><tr><th class="px-3 py-3">Fecha</th><th class="px-3 py-3">Usuario</th><th class="px-3 py-3">Evento</th><th class="px-3 py-3">Entidad</th><th class="px-3 py-3">IP</th><th class="px-3 py-3">Dispositivo</th></tr></thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <?php foreach ($auditLogs as $log): ?>
          <tr><td class="whitespace-nowrap px-3 py-3 text-slate-500"><?= $escape($log['creado_en']) ?></td><td class="px-3 py-3"><?= $escape($log['usuario'] ?? 'Sistema') ?></td><td class="px-3 py-3 font-medium"><?= $escape($log['evento']) ?></td><td class="px-3 py-3"><?= $escape($log['entidad'] . ($log['entidad_id'] ? ' #' . $log['entidad_id'] : '')) ?></td><td class="px-3 py-3 font-mono text-xs"><?= $escape($log['ip'] ?? '') ?></td><td class="max-w-[260px] truncate px-3 py-3 text-xs text-slate-500" title="<?= $escape($log['dispositivo'] ?? '') ?>"><?= $escape($log['dispositivo'] ?? '') ?></td></tr>
        <?php endforeach; ?>
        <?php if ($auditLogs === []): ?><tr><td colspan="6" class="px-3 py-10 text-center text-slate-500">Aún no hay eventos registrados.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>