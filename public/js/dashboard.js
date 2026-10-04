    // DASHBOARD: dibuja los indicadores, el gráfico y la tabla de ventas.
    // salesData y recentSales llegan desde PHP; no se consultan en este archivo.
    // ---------- Dashboard ----------
    if (document.getElementById('salesChart')) {
      // DATOS (inyectados desde PHP): salesData — barras del gráfico de ventas [{ m, v, g }].
      const chartMax = 3;
      document.getElementById('salesChart').innerHTML = `
        <div class="absolute inset-x-0 top-0 bottom-6 flex flex-col justify-between" aria-hidden="true">
          ${'<div class="border-t border-dashed border-gray-200 dark:border-slate-800"></div>'.repeat(4)}
        </div>
        <div class="relative flex h-full items-end justify-around gap-2">
          ${salesData.map((d) => `
            <div class="group flex h-full flex-1 flex-col items-center">
              <div class="relative flex w-full flex-1 items-end justify-center gap-1 sm:gap-1.5">
                <div class="pointer-events-none absolute -top-2 z-10 hidden -translate-y-full whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-[11px] text-white shadow group-hover:block dark:bg-slate-700">
                  Ventas S/ ${d.v}M · Meta S/ ${d.g}M
                </div>
                <div class="bar w-3 rounded-t-md bg-brand-600 transition-all duration-700 sm:w-5 ${d.v >= d.g ? '' : 'opacity-80'}" style="height:0" data-h="${(d.v / chartMax) * 100}%"></div>
                <div class="bar w-3 rounded-t-md bg-slate-300 transition-all duration-700 sm:w-5 dark:bg-slate-600" style="height:0" data-h="${(d.g / chartMax) * 100}%"></div>
              </div>
              <span class="mt-2 h-4 text-xs text-slate-500 dark:text-slate-400">${d.m}</span>
            </div>`).join('')}
        </div>`;
      if (salesData.length === 0) {
        document.getElementById('salesChart').innerHTML = '<div class="flex h-full items-center justify-center text-sm text-slate-500 dark:text-slate-400">Sin datos de ventas conectados</div>';
      }
      requestAnimationFrame(() => setTimeout(() => {
        document.querySelectorAll('#salesChart .bar').forEach((b) => { b.style.height = b.dataset.h; });
      }, 50));

      const statusStyles = {
        Vendido: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        Separado: 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'En proceso': 'bg-sky-50 text-sky-700 dark:bg-sky-500/15 dark:text-sky-400',
      };
      // DATOS (inyectados desde PHP): recentSales — filas de la tabla de últimas ventas.
      document.getElementById('salesTable').innerHTML = recentSales.map(([c, p, u, s, f]) => `
        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700 dark:bg-brand-600/15 dark:text-brand-400">${c.split(' ').map((n) => n[0]).join('')}</span>
              <span class="font-medium text-slate-900 dark:text-white">${c}</span>
            </div>
          </td>
          <td class="px-5 py-3 text-slate-600 dark:text-slate-300">${p}</td>
          <td class="px-5 py-3 text-slate-600 dark:text-slate-300">${u}</td>
          <td class="px-5 py-3"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${statusStyles[s]}">${s}</span></td>
          <td class="px-5 py-3 text-slate-500 dark:text-slate-400">${f}</td>
        </tr>`).join('');
      if (recentSales.length === 0) {
        document.getElementById('salesTable').innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">Sin ventas conectadas.</td></tr>';
      }

      document.getElementById('dashFilters').addEventListener('submit', (e) => e.preventDefault());
    }

    const renderAlerts = () => {
      const alerts = Array.isArray(dashboardAlerts) ? dashboardAlerts : [];
      const list = document.getElementById('dashboardAlertsList');
      const notifList = document.getElementById('notifItems');
      const alertBadge = document.getElementById('dashboardAlertsBadge');

      if (!list || !notifList) return;

      if (alerts.length === 0) {
        list.innerHTML = '<p class="rounded-lg bg-gray-50 px-3 py-6 text-center text-sm text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">No hay alertas activas en este momento.</p>';
        notifList.innerHTML = '<p class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">No hay notificaciones conectadas.</p>';
        if (alertBadge) {
          alertBadge.textContent = 'Sin datos';
          alertBadge.className = 'rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-400';
        }
        return;
      }

      const typeStyles = {
        warning: 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-500/10 dark:text-amber-200',
        info: 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-900/50 dark:bg-sky-500/10 dark:text-sky-200',
        success: 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-500/10 dark:text-emerald-200',
      };

      list.innerHTML = alerts.map((alert) => `
        <div class="rounded-xl border px-3 py-3 ${typeStyles[alert.type] || typeStyles.info}">
          <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/60 dark:bg-slate-900/40">
              <i data-lucide="${alert.icon || 'bell'}" class="h-4 w-4"></i>
            </span>
            <div class="flex-1">
              <p class="text-sm font-semibold">${alert.title}</p>
              <p class="mt-1 text-xs opacity-90">${alert.message}</p>
            </div>
          </div>
        </div>
      `).join('');

      notifList.innerHTML = alerts.map((alert) => `
        <div class="border-b border-gray-100 px-4 py-3 last:border-b-0 dark:border-slate-700">
          <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300">
              <i data-lucide="${alert.icon || 'bell'}" class="h-4 w-4"></i>
            </span>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">${alert.title}</p>
              <p class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400">${alert.message}</p>
            </div>
          </div>
        </div>
      `).join('');

      if (alertBadge) {
        alertBadge.textContent = `${alerts.length} alerta${alerts.length > 1 ? 's' : ''}`;
        alertBadge.className = 'rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300';
      }

      lucide.createIcons();
    };

    renderAlerts();

