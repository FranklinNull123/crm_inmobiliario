<!-- Dashboard Gerencial -->
        <div id="dashboardView" class="space-y-6">
          <!-- Filtros -->
          <form id="dashFilters" class="rounded-2xl border border-gray-200/70 bg-white p-4 shadow-sm sm:p-5 dark:border-slate-800 dark:bg-slate-900" aria-label="Filtros del dashboard">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
              <div>
                <label for="fRango" class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="calendar" class="h-3.5 w-3.5"></i>Rango de fechas</label>
                <select id="fRango" class="dash-select">
                  <option>Últimos 7 días</option><option>Últimos 30 días</option><option selected>Últimos 6 meses</option><option>Este año</option><option>Personalizado…</option>
                </select>
              </div>
              <div>
                <label for="fProyecto" class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="building-2" class="h-3.5 w-3.5"></i>Proyecto</label>
                <select id="fProyecto" class="dash-select">
                  <option>Todos los proyectos</option>
                </select>
              </div>
              <div>
                <label for="fSede" class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="map-pin" class="h-3.5 w-3.5"></i>Sede</label>
                <select id="fSede" class="dash-select">
                  <option>Todas las sedes</option>
                </select>
              </div>
              <div>
                <label for="fAsesor" class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="user-round" class="h-3.5 w-3.5"></i>Asesor</label>
                <select id="fAsesor" class="dash-select">
                  <option>Todos los asesores</option>
                </select>
              </div>
              <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30 sm:col-span-2 lg:col-span-1">
                <i data-lucide="filter" class="h-4 w-4"></i>Aplicar filtros
              </button>
            </div>
          </form>

          <!-- KPIs -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Leads nuevos</p>
                  <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">—</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-600/15 dark:text-brand-400"><i data-lucide="user-plus" class="h-5 w-5"></i></span>
              </div>
              <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sin datos conectados</p>
            </article>
            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Ventas cerradas</p>
                  <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">—</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400"><i data-lucide="handshake" class="h-5 w-5"></i></span>
              </div>
              <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sin datos conectados</p>
            </article>
            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Ingresos cobrados</p>
                  <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">—</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400"><i data-lucide="banknote" class="h-5 w-5"></i></span>
              </div>
              <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sin datos conectados</p>
            </article>
            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <div class="flex items-start justify-between">
                <div>
                  <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Unidades disponibles</p>
                  <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">—</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400"><i data-lucide="home" class="h-5 w-5"></i></span>
              </div>
              <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Sin datos conectados</p>
            </article>
          </div>

          <!-- Gráficos -->
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
              <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                <div>
                  <h2 class="text-base font-semibold text-slate-900 dark:text-white">Comparación de Ventas vs Metas Mensuales</h2>
                  <p class="text-sm text-slate-500 dark:text-slate-400">Montos en millones de soles (S/)</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-medium text-slate-600 dark:text-slate-300">
                  <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-brand-600"></span>Ventas</span>
                  <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-slate-300 dark:bg-slate-600"></span>Meta</span>
                </div>
              </div>
              <div class="flex gap-3">
                <div class="flex h-60 flex-col justify-between pb-6 text-right text-[11px] text-slate-400" aria-hidden="true"><span>3.0</span><span>2.0</span><span>1.0</span><span>0</span></div>
                <div id="salesChart" class="relative h-60 flex-1" role="img" aria-label="Gráfico de barras de ventas contra metas de abril a septiembre"></div>
              </div>
            </article>

            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <h2 class="text-base font-semibold text-slate-900 dark:text-white">Estado del Inventario</h2>
              <p class="text-sm text-slate-500 dark:text-slate-400">Sin datos conectados</p>
              <div class="flex min-h-64 items-center justify-center text-sm text-slate-500 dark:text-slate-400">Sin datos de inventario conectados</div>
            </article>
          </div>

          <!-- Tabla + Alertas -->
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <article class="overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-sm lg:col-span-2 dark:border-slate-800 dark:bg-slate-900">
              <div class="flex items-center justify-between px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Últimos Ventas y Separaciones</h2>
                <a href="#" class="text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">Ver todo</a>
              </div>
              <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                  <thead class="border-y border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr><th scope="col" class="px-5 py-3 font-semibold">Cliente</th><th scope="col" class="px-5 py-3 font-semibold">Proyecto</th><th scope="col" class="px-5 py-3 font-semibold">Unidad</th><th scope="col" class="px-5 py-3 font-semibold">Estado</th><th scope="col" class="px-5 py-3 font-semibold">Fecha</th></tr>
                  </thead>
                  <tbody id="salesTable" class="divide-y divide-gray-100 dark:divide-slate-800"></tbody>
                </table>
              </div>
            </article>

            <article class="rounded-2xl border border-gray-200/70 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
              <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-900 dark:text-white">Alertas</h2>
                <span id="dashboardAlertsBadge" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400">Sin datos</span>
              </div>
              <div id="dashboardAlertsList" class="space-y-3"></div>
            </article>
          </div>
        </div>