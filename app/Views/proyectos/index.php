<?php
declare(strict_types=1);
/** @var array{projects: array, stages: array, statuses: array} $projectData */
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<div id="projectsView" class="space-y-8">
  <div id="projectMessage" class="hidden rounded-md border px-4 py-3 text-sm" role="status" aria-live="polite"></div>

  <section class="border-b border-slate-200 pb-8 dark:border-slate-800">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Proyectos inmobiliarios</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Datos generales, estado operativo y unidades vinculadas.</p></div>
      <div class="flex gap-2">
        <label class="sr-only" for="projectSearch">Buscar proyecto</label>
        <input id="projectSearch" type="search" placeholder="Buscar nombre o ubicación" class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-900 sm:w-64">
        <button id="newProjectBtn" type="button" class="inline-flex h-10 shrink-0 items-center gap-2 rounded-md bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700"><i data-lucide="plus" class="h-4 w-4"></i>Nuevo proyecto</button>
      </div>
    </div>
    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
      <table class="w-full min-w-[850px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800"><tr><th class="px-4 py-3">Proyecto</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3">Fechas</th><th class="px-4 py-3 text-right">Unidades</th><th class="px-4 py-3 text-right">Etapas</th><th class="px-4 py-3 text-right">Acciones</th></tr></thead>
        <tbody id="projectTable" class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($projectData['projects'] as $project): ?>
            <?php $projectJson = htmlspecialchars(json_encode($project, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>
            <tr data-project-row data-search="<?= $escape(mb_strtolower($project['name'] . ' ' . ($project['location'] ?? ''))) ?>">
              <td class="px-4 py-3"><p class="font-semibold text-slate-900 dark:text-white"><?= $escape($project['name']) ?></p><p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400"><?= $escape($project['location'] ?? '') ?></p></td>
              <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 dark:bg-slate-800 dark:text-slate-200"><?= $escape($project['status']) ?></span></td>
              <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400"><?= $escape($project['startDate'] ?: 'Sin inicio') ?> · <?= $escape($project['deliveryDate'] ?: 'Sin entrega') ?></td>
              <td class="px-4 py-3 text-right tabular-nums"><?= (int) $project['unitsCount'] ?></td>
              <td class="px-4 py-3 text-right tabular-nums"><?= (int) $project['stagesCount'] ?></td>
              <td class="px-4 py-3"><div class="flex justify-end"><button type="button" data-edit-project="<?= $projectJson ?>" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-brand-700 dark:hover:bg-slate-800" aria-label="Editar <?= $escape($project['name']) ?>" title="Editar proyecto"><i data-lucide="pencil" class="h-4 w-4"></i></button></div></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($projectData['projects'] === []): ?><tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Aún no hay proyectos registrados.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <p id="projectSearchEmpty" class="hidden py-8 text-center text-sm text-slate-500">No hay proyectos que coincidan con la búsqueda.</p>
  </section>

  <section>
    <div class="mb-5"><h2 class="text-lg font-semibold text-slate-900 dark:text-white">Etapas de proyecto</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Ordena el ciclo del proyecto; las etapas con unidades activas no se pueden desactivar.</p></div>
    <form id="stageForm" class="mb-4 grid gap-3 rounded-md bg-slate-50 p-4 dark:bg-slate-900/60 sm:grid-cols-[1.3fr_1.3fr_.6fr_auto]">
      <input type="hidden" name="id" value="">
      <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Proyecto
        <select name="project_id" required class="mt-1 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950"><?php foreach ($projectData['projects'] as $project): ?><option value="<?= (int) $project['id'] ?>"><?= $escape($project['name']) ?></option><?php endforeach; ?></select>
      </label>
      <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Nombre de etapa
        <input name="name" required minlength="2" maxlength="100" class="mt-1 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>
      <label class="text-xs font-semibold text-slate-600 dark:text-slate-400">Orden
        <input name="position" type="number" min="1" max="999" value="1" required class="mt-1 h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-950">
      </label>
      <div class="flex items-end gap-2"><button id="stageSubmit" class="h-10 rounded-md bg-[#183b35] px-4 text-sm font-semibold text-white hover:bg-[#24584d]" type="submit">Añadir etapa</button><button id="stageCancel" class="hidden h-10 rounded-md px-3 text-sm text-slate-600 hover:bg-slate-200 dark:text-slate-300 dark:hover:bg-slate-800" type="button">Cancelar</button></div>
    </form>
    <div class="overflow-x-auto rounded-md border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
      <table class="w-full min-w-[650px] text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800"><tr><th class="px-4 py-3">Proyecto</th><th class="px-4 py-3">Etapa</th><th class="px-4 py-3 text-right">Orden</th><th class="px-4 py-3">Estado</th><th class="px-4 py-3 text-right">Acciones</th></tr></thead>
        <tbody id="stageTable" class="divide-y divide-slate-100 dark:divide-slate-800">
          <?php foreach ($projectData['stages'] as $stage): ?>
            <tr data-stage-row data-stage-id="<?= (int) $stage['id'] ?>" data-project-id="<?= (int) $stage['projectId'] ?>" data-name="<?= $escape($stage['name']) ?>" data-position="<?= (int) $stage['position'] ?>">
              <td class="px-4 py-3"><?= $escape($stage['project']) ?></td><td class="px-4 py-3 font-medium"><?= $escape($stage['name']) ?></td><td class="px-4 py-3 text-right tabular-nums"><?= (int) $stage['position'] ?></td><td class="px-4 py-3"><?= (int) $stage['active'] === 1 ? 'Activa' : 'Inactiva' ?></td>
              <td class="px-4 py-3"><div class="flex justify-end gap-1"><button type="button" data-edit-stage class="rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-brand-700 dark:hover:bg-slate-800" aria-label="Editar etapa"><i data-lucide="pencil" class="h-4 w-4"></i></button><button type="button" data-toggle-stage="<?= (int) $stage['active'] === 1 ? '0' : '1' ?>" class="rounded-md px-2 py-1 text-xs font-semibold text-brand-700 hover:bg-slate-100 dark:text-brand-400 dark:hover:bg-slate-800"><?= (int) $stage['active'] === 1 ? 'Desactivar' : 'Activar' ?></button></div></td>
            </tr>
          <?php endforeach; ?>
          <?php if ($projectData['stages'] === []): ?><tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">Añade una etapa para iniciar la planificación.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div id="projectModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="projectModalTitle">
    <div data-project-close class="absolute inset-0 bg-slate-950/60"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
      <form id="projectForm" class="relative w-full max-w-2xl space-y-4 rounded-lg bg-white p-6 shadow-2xl dark:bg-slate-900">
        <div class="flex items-start justify-between"><div><h2 id="projectModalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Nuevo proyecto</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Los proyectos no se eliminan físicamente.</p></div><button type="button" data-project-close class="rounded-md p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Cerrar"><i data-lucide="x" class="h-5 w-5"></i></button></div>
        <input type="hidden" name="id">
        <div class="grid gap-4 sm:grid-cols-2">
          <label class="text-sm font-medium">Nombre<input name="name" required minlength="2" maxlength="120" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
          <label class="text-sm font-medium">Ubicación<input name="location" maxlength="200" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
          <label class="text-sm font-medium">Fecha de inicio<input name="start_date" type="date" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
          <label class="text-sm font-medium">Fecha de entrega<input name="delivery_date" type="date" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
          <label class="text-sm font-medium sm:col-span-2">Estado<select name="status" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"><?php foreach ($projectData['statuses'] as $status): ?><option value="<?= $escape($status['label']) ?>"><?= $escape($status['label']) ?></option><?php endforeach; ?></select></label>
          <label class="text-sm font-medium sm:col-span-2">Descripción<textarea name="description" rows="3" maxlength="4000" class="mt-1.5 w-full rounded-md border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-950"></textarea></label>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-800"><button type="button" data-project-close class="h-10 rounded-md border border-slate-300 px-4 text-sm font-medium dark:border-slate-700">Cancelar</button><button type="submit" class="h-10 rounded-md bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">Guardar proyecto</button></div>
      </form>
    </div>
  </div>
</div>
