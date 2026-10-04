<?php
/** @var list<array<string, mixed>> $inventoryProjects */
/** @var list<array<string, mixed>> $inventoryStages */
/** @var list<string> $unitStatuses */
?>
<!-- Inventario -->
<div id="inventoryView">
    <div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Inventario de Unidades</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Controla la disponibilidad, precios y estado de cada unidad de tus proyectos.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <div class="relative sm:w-72">
                <label for="unitSearch" class="sr-only">Buscar unidades</label>
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input id="unitSearch" type="search" placeholder="Buscar por código o proyecto…" autocomplete="off" class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <div class="flex gap-3">
                <button id="toggleFilters" type="button" aria-expanded="false" aria-controls="advancedFilters" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-brand-500/20 sm:flex-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>Filtros Avanzados
                    <span id="filterCount" class="hidden rounded-full bg-brand-600 px-1.5 text-[10px] font-semibold leading-4 text-white"></span>
                </button>
                <button id="exportBtn" type="button" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-brand-500/20 sm:flex-none dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                    <i data-lucide="download" class="h-4 w-4"></i>Exportar
                </button>
            </div>
            <button id="newUnitBtn" type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
                <i data-lucide="plus" class="h-4 w-4"></i>Nueva Unidad
            </button>
        </div>
    </div>

    <form id="advancedFilters" class="mb-4 hidden grid-cols-1 gap-3 rounded-2xl border border-gray-200/70 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4 dark:border-slate-800 dark:bg-slate-900">
        <label class="flex flex-col gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">Proyecto
            <select name="project" class="inv-filter h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"></select>
        </label>
        <label class="flex flex-col gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">Etapa
            <select name="stage" class="inv-filter h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"></select>
        </label>
        <label class="flex flex-col gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">Tipo
            <select name="type" class="inv-filter h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">Todos</option>
                <option>Lote</option>
                <option>Dpto.</option>
            </select>
        </label>
        <label class="flex flex-col gap-1.5 text-xs font-medium text-slate-600 dark:text-slate-400">Estado
            <select name="status" class="inv-filter h-10 rounded-lg border border-gray-200 bg-white px-3 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">Todos</option>
                <?php foreach ($unitStatuses as $status): ?><option><?= htmlspecialchars((string) $status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option><?php endforeach; ?>
            </select>
        </label>
        <div class="flex items-end">
            <button type="reset" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg px-4 text-sm font-medium text-slate-600 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-800">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>Limpiar filtros
            </button>
        </div>
    </form>

    <article class="overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <caption class="sr-only">Inventario de unidades inmobiliarias</caption>
                <thead class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-3">Código</th>
                        <th scope="col" class="px-5 py-3">Proyecto</th>
                        <th scope="col" class="px-5 py-3">Tipo</th>
                        <th scope="col" class="px-5 py-3 text-right">Área</th>
                        <th scope="col" class="px-5 py-3 text-right">Precio</th>
                        <th scope="col" class="px-5 py-3">Estado</th>
                        <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="inventoryTable" class="divide-y divide-gray-100 dark:divide-slate-800"></tbody>
            </table>
        </div>

        <nav aria-label="Paginación" class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
            <p id="pageInfo" class="text-sm text-slate-500 dark:text-slate-400"></p>
            <div id="pagination" class="flex items-center gap-1"></div>
        </nav>
    </article>
</div>

<div id="unitModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="unitModalTitle">
    <div data-unit-close class="absolute inset-0 bg-slate-950/60"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <form id="unitForm" class="relative w-full max-w-2xl space-y-5 rounded-lg bg-white p-6 shadow-2xl dark:bg-slate-900">
            <div class="flex items-start justify-between"><div><h2 id="unitModalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Nueva unidad</h2><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Código único dentro del inventario.</p></div><button type="button" data-unit-close aria-label="Cerrar" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800"><i data-lucide="x" class="h-5 w-5"></i></button></div>
            <input type="hidden" name="id">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">Proyecto<select name="project_id" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"><option value="">Selecciona proyecto</option><?php foreach ($inventoryProjects as $project): ?><option value="<?= (int) $project['id'] ?>"><?= htmlspecialchars($project['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option><?php endforeach; ?></select></label>
                <label class="text-sm font-medium">Etapa<select name="stage_id" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"><option value="">Sin etapa</option></select></label>
                <label class="text-sm font-medium">Código<input name="code" required maxlength="30" pattern="[A-Za-z0-9][A-Za-z0-9._-]{1,29}" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 font-mono dark:border-slate-700 dark:bg-slate-950"></label>
                <label class="text-sm font-medium">Tipo<input name="type" required maxlength="30" placeholder="Dpto., Lote, Casa..." class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
                <label class="text-sm font-medium">Área (m²)<input name="area" type="number" min="0.01" max="100000" step="0.01" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
                <label class="text-sm font-medium">Precio (S/)<input name="price" type="number" min="0.01" max="9999999999" step="0.01" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"></label>
                <label class="text-sm font-medium sm:col-span-2">Estado<select name="status" required class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950"><?php foreach ($unitStatuses as $status): ?><option><?= htmlspecialchars((string) $status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option><?php endforeach; ?></select></label>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-800"><button type="button" data-unit-close class="h-10 rounded-md border border-slate-300 px-4 text-sm font-medium dark:border-slate-700">Cancelar</button><button type="submit" class="h-10 rounded-md bg-brand-600 px-4 text-sm font-semibold text-white hover:bg-brand-700">Guardar unidad</button></div>
        </form>
    </div>
</div>