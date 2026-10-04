    // CRM: organiza los leads recibidos desde PHP en columnas Kanban.
    // ---------- CRM Kanban ----------
    if (document.getElementById('crmView')) {
      const stages = [
        { id: 'nuevo', status: 'Nuevo', name: 'Nuevo Lead', icon: 'sparkles', bar: 'bg-sky-500', tint: 'bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400' },
        { id: 'contactado', status: 'Contactado', name: 'Contactado', icon: 'phone-outgoing', bar: 'bg-brand-600', tint: 'bg-brand-50 text-brand-600 dark:bg-brand-600/15 dark:text-brand-400' },
        { id: 'visita', status: 'Visita Realizada', name: 'Visita Realizada', icon: 'map-pin-check', bar: 'bg-violet-500', tint: 'bg-violet-50 text-violet-600 dark:bg-violet-500/15 dark:text-violet-400' },
        { id: 'negociacion', status: 'Negociación', name: 'Negociación', icon: 'messages-square', bar: 'bg-amber-500', tint: 'bg-amber-50 text-amber-600 dark:bg-amber-500/15 dark:text-amber-400' },
        { id: 'cerrada', status: 'Venta Cerrada', name: 'Venta Cerrada', icon: 'badge-check', bar: 'bg-emerald-500', tint: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400' },
        { id: 'otros', status: null, name: 'Otros estados', icon: 'circle-help', bar: 'bg-slate-400', tint: 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300' },
      ];
      const statusAliases = {
        nuevo: 'nuevo',
        'nuevo lead': 'nuevo',
        contactado: 'contactado',
        visita: 'visita',
        'visita realizada': 'visita',
        negociacion: 'negociacion',
        cerrado: 'cerrada',
        cerrada: 'cerrada',
        'venta cerrada': 'cerrada',
      };
      const normalizeStatus = (status) => String(status || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
      const escapeHTML = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
      })[character]);
      const board = document.getElementById('kanbanBoard');
      const leadSearch = document.getElementById('leadSearch');
      const leadFeedback = document.getElementById('leadFeedback');
      let draggedLeadId = null;

      function showLeadFeedback(message, isError = false) {
        leadFeedback.textContent = message;
        leadFeedback.className = `mb-4 rounded-lg border px-3 py-2 text-sm ${isError
          ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300'
          : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'}`;
      }

      async function saveLeadStatus(id, status) {
        const response = await fetch('?view=crm', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': leadCsrfToken,
          },
          body: JSON.stringify({ action: 'update_status', id, status }),
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'No se pudo guardar el estado.');
      }

      function leadCard(l) {
        const details = [
          l.campaign && `Campaña: ${escapeHTML(l.campaign)}`,
          l.channel && `Canal: ${escapeHTML(l.channel)}`,
          l.advisor && `Asesor: ${escapeHTML(l.advisor)}`,
        ].filter(Boolean).join(' · ');

        return `
          <li draggable="true" data-lead-id="${Number(l.id)}" class="cursor-grab rounded-xl border border-gray-200/70 bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-500/40 hover:shadow-md active:cursor-grabbing dark:border-slate-700/70 dark:bg-slate-900">
            <p class="truncate text-sm font-bold text-slate-900 dark:text-white">${escapeHTML(l.name)}</p>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Estado: ${escapeHTML(l.status || 'Sin estado')}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">${details || 'Sin campaña, canal ni asesor asignados'}</p>
            <div class="mt-3 flex justify-end"><i data-lucide="grip-vertical" class="h-4 w-4 text-slate-300 dark:text-slate-600" aria-hidden="true"></i></div>
          </li>`;
      }

      function renderBoard() {
        const q = leadSearch.value.trim().toLowerCase();
        if (leadError) {
          board.innerHTML = `<li class="w-full py-10 text-center text-sm text-rose-600 dark:text-rose-400">${escapeHTML(leadError)}</li>`;
          return;
        }
        const visible = leads.filter((l) =>
          !q || [l.name, l.status, l.campaign, l.channel, l.advisor].some((value) => String(value || '').toLowerCase().includes(q)));
        board.innerHTML = stages.map((s) => {
          const items = visible.filter((l) => (statusAliases[normalizeStatus(l.status)] || 'otros') === s.id);
          return `
            <section role="listitem" aria-label="${s.name}" class="flex w-72 shrink-0 flex-col overflow-hidden rounded-2xl border border-gray-200/70 bg-gray-50/80 dark:border-slate-800 dark:bg-slate-900/50">
              <div class="h-1 ${s.bar}"></div>
              <header class="flex items-center justify-between px-3.5 py-3">
                <div class="flex items-center gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-lg ${s.tint}"><i data-lucide="${s.icon}" class="h-4 w-4"></i></div>
                  <h2 class="text-sm font-semibold text-slate-900 dark:text-white">${s.name}</h2>
                </div>
                <output class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-gray-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">${items.length}</output>
              </header>
              <ul data-stage="${s.id}" class="kanban-drop flex min-h-32 flex-1 flex-col gap-3 px-3 pb-3 transition-colors">
                ${items.length ? items.map(leadCard).join('') : `<li class="flex flex-1 items-center justify-center rounded-xl border-2 border-dashed border-gray-200 py-8 text-xs text-slate-400 dark:border-slate-700">Sin leads</li>`}
              </ul>
            </section>`;
        }).join('');
        lucide.createIcons();
      }

      leadSearch.addEventListener('input', renderBoard);

      board.addEventListener('dragstart', (event) => {
        const card = event.target.closest('[data-lead-id]');
        if (!card) return;
        draggedLeadId = Number(card.dataset.leadId);
        event.dataTransfer.setData('text/plain', String(draggedLeadId));
        event.dataTransfer.effectAllowed = 'move';
      });
      board.addEventListener('dragend', () => {
        draggedLeadId = null;
        board.querySelectorAll('.kanban-drop').forEach((column) => column.classList.remove('bg-brand-500/5'));
      });
      board.addEventListener('dragover', (event) => {
        const column = event.target.closest('.kanban-drop');
        if (!column || column.dataset.stage === 'otros') return;
        event.preventDefault();
        column.classList.add('bg-brand-500/5');
      });
      board.addEventListener('drop', async (event) => {
        const column = event.target.closest('.kanban-drop');
        if (!column || draggedLeadId === null || column.dataset.stage === 'otros') return;
        event.preventDefault();
        const stage = stages.find((item) => item.id === column.dataset.stage);
        const lead = leads.find((item) => Number(item.id) === draggedLeadId);
        if (!stage || !lead || statusAliases[normalizeStatus(lead.status)] === stage.id) return;

        try {
          await saveLeadStatus(lead.id, stage.status);
          lead.status = stage.status;
          renderBoard();
          showLeadFeedback('Estado actualizado y guardado.');
        } catch (error) {
          renderBoard();
          showLeadFeedback(error.message, true);
        }
      });

      const leadModal = document.getElementById('leadModal');
      const leadName = document.getElementById('leadName');
      const openLeadModal = () => {
        leadModal.classList.remove('hidden');
        lucide.createIcons();
        leadName.focus();
      };
      document.getElementById('newLeadBtn').addEventListener('click', openLeadModal);
      document.getElementById('newRecordBtn')?.addEventListener('click', openLeadModal);
      leadModal.querySelectorAll('[data-lead-close]').forEach((button) => {
        button.addEventListener('click', () => leadModal.classList.add('hidden'));
      });
      leadModal.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') leadModal.classList.add('hidden');
      });

      renderBoard();
    }

