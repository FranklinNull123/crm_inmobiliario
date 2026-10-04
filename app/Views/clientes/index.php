<!-- Clientes -->
<div id="clientsView">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Clientes</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Consulta la ficha, propiedades y estado de cuenta de cada cliente.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative sm:w-72">
                <label for="clientSearch" class="sr-only">Buscar clientes</label>
                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                <input id="clientSearch" type="search" placeholder="Buscar por nombre o DNI…" autocomplete="off" class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
            </div>
            <button type="button" data-open-separation class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                <i data-lucide="file-signature" class="h-4 w-4"></i>Nueva Separación
            </button>
            <button type="button" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
                <i data-lucide="user-plus" class="h-4 w-4"></i>Nuevo Cliente
            </button>
        </div>
    </div>
    <article class="overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <caption class="sr-only">Listado de clientes</caption>
                <thead class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-400">
                    <tr>
                        <th scope="col" class="px-5 py-3">Cliente</th>
                        <th scope="col" class="px-5 py-3">DNI</th>
                        <th scope="col" class="px-5 py-3">Teléfono</th>
                        <th scope="col" class="px-5 py-3">Asesor</th>
                        <th scope="col" class="px-5 py-3 text-right">Saldo Pendiente</th>
                        <th scope="col" class="px-5 py-3">Estado</th>
                        <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="clientsTable" class="divide-y divide-gray-100 dark:divide-slate-800"></tbody>
            </table>
        </div>
    </article>
</div>