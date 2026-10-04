    // INVENTARIO: filtra, pagina y exporta las unidades recibidas desde PHP.
    // ---------- Inventario ----------
    if (document.getElementById('inventoryView')) {
      // DATOS (inyectados desde PHP): units — unidades de inventario.
      // DATOS (inyectados desde PHP): totalResults — total de resultados de la consulta (paginación).
      const perPage = 10;
      let currentPage = 1;
      let currentRowsCount = units.length;
      const unitSearch = document.getElementById('unitSearch');
      const filtersForm = document.getElementById('advancedFilters');
      const toggleFiltersBtn = document.getElementById('toggleFilters');
      const escapeHTML = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);

      filtersForm.project.innerHTML = '<option value="">Todos</option>' +
        [...new Set(units.map((u) => u.project))].map((p) => `<option>${escapeHTML(p)}</option>`).join('');
      filtersForm.stage.innerHTML = '<option value="">Todas</option>' +
        [...new Set(units.map((u) => u.stage).filter(Boolean))].map((stage) => `<option>${escapeHTML(stage)}</option>`).join('');
      const unitModal = document.getElementById('unitModal');
      const unitForm = document.getElementById('unitForm');
      const unitMessage = document.createElement('p');
      unitMessage.className = 'hidden mb-4 rounded-md border px-4 py-3 text-sm';
      unitMessage.setAttribute('role', 'status');
      document.getElementById('inventoryView').prepend(unitMessage);
      function showInventoryMessage(text, error = false) {
        unitMessage.textContent = text;
        unitMessage.className = `mb-4 rounded-md border px-4 py-3 text-sm ${error ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`;
      }

      async function saveInventory(action, data) {
        const response = await fetch('?api=inventario', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': inventoryCsrfToken },
          body: JSON.stringify({ action, ...data }),
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'No se pudo guardar la unidad.');
        return result;
      }

      function fillStages(projectId, selectedStage = '') {
        const stageSelect = unitForm.elements.stage_id;
        const stages = inventoryStageOptions.filter((stage) => Number(stage.projectId) === Number(projectId));
        stageSelect.innerHTML = '<option value="">Sin etapa</option>' + stages.map((stage) => `<option value="${Number(stage.id)}">${escapeHTML(stage.name)}</option>`).join('');
        stageSelect.value = selectedStage || '';
      }

      function openUnitForm(unit = null) {
        unitForm.reset();
        unitForm.elements.id.value = unit?.id ?? '';
        unitForm.elements.project_id.value = unit?.projectId ?? inventoryProjectOptions[0]?.id ?? '';
        fillStages(unitForm.elements.project_id.value, unit?.stageId ?? '');
        unitForm.elements.code.value = unit?.code ?? '';
        unitForm.elements.type.value = unit?.type ?? 'Dpto.';
        unitForm.elements.area.value = unit?.area ?? '';
        unitForm.elements.price.value = unit?.price ?? '';
        unitForm.elements.status.value = unit?.status ?? inventoryStatusOptions[0] ?? '';
        document.getElementById('unitModalTitle').textContent = unit ? `Editar unidad ${unit.code}` : 'Nueva unidad';
        unitModal.classList.remove('hidden');
        unitForm.elements.code.focus();
      }

      document.getElementById('newUnitBtn').addEventListener('click', () => openUnitForm());
      unitForm.elements.project_id.addEventListener('change', () => fillStages(unitForm.elements.project_id.value));
      unitModal.querySelectorAll('[data-unit-close]').forEach((button) => button.addEventListener('click', () => unitModal.classList.add('hidden')));
      unitModal.addEventListener('keydown', (event) => { if (event.key === 'Escape') unitModal.classList.add('hidden'); });
      unitForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
          await saveInventory('unit_save', Object.fromEntries(new FormData(unitForm)));
          location.reload();
        } catch (error) { showInventoryMessage(error.message, true); }
      });
      function renderInventory() {
        const q = unitSearch.value.trim().toLowerCase();
        const f = Object.fromEntries(new FormData(filtersForm));
        const active = Object.values(f).filter(Boolean).length;
        const countEl = document.getElementById('filterCount');
        countEl.textContent = active;
        countEl.classList.toggle('hidden', !active);

        const filteredRows = units.filter((u) =>
          (!q || `${u.code} ${u.project}`.toLowerCase().includes(q)) &&
          (!f.project || u.project === f.project) &&
          (!f.stage || u.stage === f.stage) &&
          (!f.type || u.type === f.type) &&
          (!f.status || u.status === f.status));
        currentRowsCount = filteredRows.length;
        const pages = Math.ceil(currentRowsCount / perPage);
        currentPage = Math.min(currentPage, Math.max(pages, 1));
        const rows = filteredRows.slice((currentPage - 1) * perPage, currentPage * perPage);

        document.getElementById('inventoryTable').innerHTML = rows.length ? rows.map((u) => {
          const s = unitStatus[u.status];
          return `
          <tr class="transition hover:bg-gray-50 dark:hover:bg-slate-800/40">
            <td class="whitespace-nowrap px-5 py-3.5 font-mono text-xs font-semibold text-slate-900 dark:text-white">${escapeHTML(u.code)}</td>
            <td class="whitespace-nowrap px-5 py-3.5">
              <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600 dark:bg-brand-600/15 dark:text-brand-400"><i data-lucide="building-2" class="h-4 w-4"></i></span>
                <span class="font-medium text-slate-900 dark:text-white">${escapeHTML(u.project)}</span>
              </div>
            </td>
            <td class="whitespace-nowrap px-5 py-3.5">
              <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300"><i data-lucide="${u.type === 'Lote' ? 'map' : 'building'}" class="h-3.5 w-3.5 text-slate-400"></i>${escapeHTML(u.type)}</span>
            </td>
            <td class="whitespace-nowrap px-5 py-3.5 text-right tabular-nums text-slate-600 dark:text-slate-300">${u.area.toLocaleString('es-PE')} m²</td>
            <td class="whitespace-nowrap px-5 py-3.5 text-right font-semibold tabular-nums text-slate-900 dark:text-white">${money.format(u.price)}</td>
            <td class="whitespace-nowrap px-5 py-3.5">
              <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset ${s?.cls ?? unitStatus.Bloqueado.cls}"><span class="h-1.5 w-1.5 rounded-full ${s?.dot ?? unitStatus.Bloqueado.dot}" aria-hidden="true"></span>${escapeHTML(u.status)}</span>
            </td>
            <td class="whitespace-nowrap px-5 py-3.5">
              <div class="flex items-center justify-end gap-1">
                ${actionBtn('eye', `Ver detalle de ${escapeHTML(u.code)}`, 'hover:text-brand-600 dark:hover:text-brand-400')}
                <button type="button" data-edit-unit="${Number(u.id)}" title="Editar ${escapeHTML(u.code)}" aria-label="Editar ${escapeHTML(u.code)}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-gray-100 hover:text-amber-600 dark:hover:bg-slate-800"><i data-lucide="pencil" class="h-4 w-4"></i></button>
                <button type="button" data-archive-unit="${Number(u.id)}" title="Archivar ${escapeHTML(u.code)}" aria-label="Archivar ${escapeHTML(u.code)}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-slate-800"><i data-lucide="archive" class="h-4 w-4"></i></button>
              </div>
            </td>
          </tr>`;
        }).join('') : `
          <tr><td colspan="7" class="px-5 py-12 text-center">
            <div class="flex flex-col items-center gap-2 text-slate-500 dark:text-slate-400">
              <i data-lucide="search-x" class="h-6 w-6"></i><p class="text-sm">No se encontraron unidades con esos criterios.</p>
            </div>
          </td></tr>`;
        lucide.createIcons();
        renderPagination();
      }

      document.getElementById('inventoryTable').addEventListener('click', async (event) => {
        const editButton = event.target.closest('[data-edit-unit]');
        const archiveButton = event.target.closest('[data-archive-unit]');
        if (editButton) {
          const unit = units.find((item) => Number(item.id) === Number(editButton.dataset.editUnit));
          if (unit) openUnitForm(unit);
        }
        if (archiveButton) {
          const unit = units.find((item) => Number(item.id) === Number(archiveButton.dataset.archiveUnit));
          if (!unit || !window.confirm(`¿Archivar la unidad ${unit.code}? No se eliminará su historial.`)) return;
          try { await saveInventory('unit_archive', { id: unit.id }); location.reload(); }
          catch (error) { showInventoryMessage(error.message, true); }
        }
      });

      function renderPagination() {
        const pages = Math.ceil(currentRowsCount / perPage);
        const from = currentRowsCount === 0 ? 0 : (currentPage - 1) * perPage + 1;
        const to = Math.min(currentPage * perPage, currentRowsCount);
        document.getElementById('pageInfo').innerHTML = `Mostrando <span class="font-semibold text-slate-900 dark:text-white">${from}</span> a <span class="font-semibold text-slate-900 dark:text-white">${to}</span> de <span class="font-semibold text-slate-900 dark:text-white">${currentRowsCount}</span> resultados`;
        const base = 'inline-flex h-9 items-center justify-center rounded-lg text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-brand-500/40 disabled:pointer-events-none disabled:opacity-40';
        const idle = 'border border-gray-200 bg-white text-slate-700 hover:bg-gray-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800';
        const nums = Array.from({ length: pages }, (_, i) => i + 1).map((p) => p === currentPage
          ? `<button type="button" data-page="${p}" aria-current="page" class="${base} w-9 bg-brand-600 text-white shadow-sm">${p}</button>`
          : `<button type="button" data-page="${p}" class="${base} ${idle} hidden w-9 sm:inline-flex">${p}</button>`).join('');
        document.getElementById('pagination').innerHTML = `
          <button type="button" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''} class="${base} ${idle} gap-1 px-3"><i data-lucide="chevron-left" class="h-4 w-4"></i>Anterior</button>
          ${nums}
          <button type="button" data-page="${currentPage + 1}" ${currentPage >= pages ? 'disabled' : ''} class="${base} ${idle} gap-1 px-3">Siguiente<i data-lucide="chevron-right" class="h-4 w-4"></i></button>`;
        lucide.createIcons();
      }

      document.getElementById('pagination').addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-page]');
        if (!btn || btn.disabled) return;
        currentPage = Number(btn.dataset.page);
        renderInventory();
      });
      toggleFiltersBtn.addEventListener('click', () => {
        const open = filtersForm.classList.toggle('hidden') === false;
        filtersForm.classList.toggle('grid', open);
        toggleFiltersBtn.setAttribute('aria-expanded', String(open));
      });
      unitSearch.addEventListener('input', () => { currentPage = 1; renderInventory(); });
      filtersForm.addEventListener('change', () => { currentPage = 1; renderInventory(); });
      filtersForm.addEventListener('reset', () => setTimeout(() => { currentPage = 1; renderInventory(); }));
      filtersForm.addEventListener('submit', (e) => e.preventDefault());

      document.getElementById('exportBtn').addEventListener('click', () => {
        const header = ['Código', 'Proyecto', 'Tipo', 'Área (m²)', 'Precio (S/)', 'Estado'];
        const csv = [header, ...units.map((u) => [u.code, u.project, u.type, u.area, u.price, u.status])]
          .map((r) => r.map((c) => `"${String(c).replace(/"/g, '""')}"`).join(',')).join('\n');
        const url = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8' }));
        const a = Object.assign(document.createElement('a'), { href: url, download: 'inventario-unidades.csv' });
        a.click();
        URL.revokeObjectURL(url);
      });

      renderInventory();
    }

