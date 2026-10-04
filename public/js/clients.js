    // CLIENTES: muestra el listado, la ficha, propiedades y pagos del cliente.
    // Los registros llegan desde PHP; este archivo solo construye la interfaz.
    // ---------- Clientes ----------
    if (document.getElementById('clientsView') || document.getElementById('clientDetailView')) {
      const DAY = 86400000;
      const today = new Date(); today.setHours(0, 0, 0, 0);
      const fmtDate = (d) => d.toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
      const clientStatus = {
        'Cliente Activo': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/30',
        'En Mora': 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/30',
        'Cancelado': 'bg-gray-100 text-slate-600 ring-slate-500/20 dark:bg-slate-700/50 dark:text-slate-300 dark:ring-slate-500/30',
      };
      // DATOS (inyectados desde PHP): clients — clientes con propiedades y cronograma.
      clients.forEach((c) => {
        c.invested = c.properties.reduce((s, p) => s + p.price, 0);
        c.pending = c.installment * (c.totalCount - c.paidCount);
      });
      const badge = (text, cls, icon) => `<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${cls}">${icon ? `<i data-lucide="${icon}" class="h-3.5 w-3.5"></i>` : ''}${text}</span>`;
      const initials = (name) => name.split(' ').slice(0, 2).map((n) => n[0]).join('');
      let currentClient = null;

      if (document.getElementById('clientsTable')) {
        const clientSearch = document.getElementById('clientSearch');
        const clientsTable = document.getElementById('clientsTable');
        function renderClients() {
          const q = clientSearch.value.trim().toLowerCase();
          const rows = clients.filter((c) => !q || c.name.toLowerCase().includes(q) || c.dni.includes(q));
          clientsTable.innerHTML = rows.length ? rows.map((c) => `
            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40">
              <td class="px-5 py-3">
                <div class="flex items-center gap-3">
                  <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-xs font-semibold text-brand-700 dark:bg-brand-600/15 dark:text-brand-400">${initials(c.name)}</span>
                  <div class="min-w-0"><p class="font-medium text-slate-900 dark:text-white">${c.name}</p><p class="text-xs text-slate-500 dark:text-slate-400">${c.email}</p></div>
                </div>
              </td>
              <td class="px-5 py-3 tabular-nums text-slate-600 dark:text-slate-300">${c.dni}</td>
              <td class="px-5 py-3 tabular-nums text-slate-600 dark:text-slate-300">${c.phone}</td>
              <td class="px-5 py-3 text-slate-600 dark:text-slate-300">${c.advisor}</td>
              <td class="px-5 py-3 text-right font-medium tabular-nums text-slate-900 dark:text-white">${money.format(c.pending)}</td>
              <td class="px-5 py-3">${badge(c.status, clientStatus[c.status])}</td>
              <td class="px-5 py-3">
                <div class="flex justify-end gap-1">
                  <a href="?view=detalle&id=${c.id}" title="Ver detalle" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-gray-100 hover:text-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40 dark:hover:bg-slate-800 dark:hover:text-brand-400"><i data-lucide="eye" class="h-4 w-4"></i><span class="sr-only">Ver detalle de ${c.name}</span></a>
                  ${actionBtn('pencil', 'Editar', 'hover:text-amber-600 dark:hover:text-amber-400')}
                </div>
              </td>
            </tr>`).join('')
            : '<tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No se encontraron clientes.</td></tr>';
          lucide.createIcons();
        }
        clientSearch.addEventListener('input', renderClients);
        renderClients();
      }

      if (document.getElementById('clientDetailView')) {
        function buildSchedule(c) {
          const rows = [];
          const start = Math.max(1, c.paidCount - 2);
          const end = Math.min(c.totalCount, c.paidCount + 4);
          for (let n = start; n <= end; n++) {
            const offset = c.dueInDays === null ? (n - c.totalCount - 1) * 30 : c.dueInDays + (n - c.paidCount - 1) * 30;
            const date = new Date(today.getTime() + offset * DAY);
            const status = n <= c.paidCount ? 'Pagado' : date < today ? 'Vencido' : 'Pendiente';
            rows.push({ n, date, amount: c.installment || Math.round(c.invested / c.totalCount), status });
          }
          return rows;
        }

        function infoRow(icon, label, value) {
          return `<div class="flex items-start gap-4 py-3.5 first:pt-0 last:pb-0">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400"><i data-lucide="${icon}" class="h-4 w-4"></i></span>
            <div class="min-w-0"><dt class="text-xs font-medium text-slate-500 dark:text-slate-400">${label}</dt><dd class="mt-0.5 break-words text-sm font-medium text-slate-900 dark:text-white">${value}</dd></div>
          </div>`;
        }

        function renderClientContacts(c) {
          const list = document.getElementById('clientContacts');
          const contactos = c.contactos || [];
          list.innerHTML = contactos.length ? contactos.map((contact) => `
            <div class="flex items-center justify-between gap-3 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-slate-800 dark:bg-slate-800/50">
              <div>
                <div class="flex items-center gap-2">
                  <p class="text-sm font-semibold text-slate-900 dark:text-white">${contact.tipo}</p>
                  ${contact.principal ? badge('Principal', 'bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-600/15 dark:text-brand-300 dark:ring-brand-500/30', 'star') : ''}
                </div>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">${contact.valor}</p>
              </div>
              <button type="button" data-contact-id="${contact.id}" class="rounded-lg p-2 text-slate-400 transition hover:bg-white hover:text-brand-600 dark:hover:bg-slate-900 dark:hover:text-brand-400" title="Editar contacto"><i data-lucide="pencil" class="h-4 w-4"></i></button>
            </div>
          `).join('') : '<div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-5 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-800/40 dark:text-slate-400">No hay contactos registrados para este cliente.</div>';
          list.querySelectorAll('[data-contact-id]').forEach((btn) => {
            btn.addEventListener('click', () => {
              const contact = contactos.find((item) => String(item.id) === btn.dataset.contactId);
              if (!contact) return;
              const nextType = window.prompt('Tipo de contacto', contact.tipo || 'Celular');
              const nextValue = window.prompt('Valor del contacto', contact.valor || '');
              if (!nextType || !nextValue) return;
              fetch('?api=contactos', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': leadCsrfToken },
                body: JSON.stringify({ action: 'create', cliente_id: c.id, id_contacto: contact.id, tipo_contacto: nextType.trim(), telefono: nextValue.trim(), es_principal: contact.principal ? 1 : 0 })
              })
                .then(async (response) => {
                  const result = await response.json();
                  if (!response.ok) throw new Error(result.message || 'No se pudo actualizar el contacto.');
                  const updated = result.contactos || [];
                  c.contactos = updated;
                  renderClientContacts(c);
                  lucide.createIcons();
                })
                .catch((error) => window.alert(error.message || 'No se pudo actualizar el contacto.'));
            });
          });
          lucide.createIcons();
        }

        function renderFinance(c) {
          const paidPct = Math.round((c.paidCount / c.totalCount) * 100);
          let due = `<p class="text-sm font-medium text-slate-500 dark:text-slate-400">Sin cuotas pendientes</p>`;
          if (c.dueInDays !== null) {
            const dueDate = new Date(today.getTime() + c.dueInDays * DAY);
            const urgent = c.dueInDays <= 7;
            const label = c.dueInDays < 0 ? `Vencido hace ${-c.dueInDays} días` : c.dueInDays === 0 ? 'Vence hoy' : `Vence en ${c.dueInDays} días`;
            due = `<p class="text-xl font-bold tabular-nums ${urgent ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white'}">${fmtDate(dueDate)}</p>
              <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                Cuota ${c.paidCount + 1} · ${money.format(c.installment)}
                ${urgent ? badge(label, clientStatus['En Mora'], 'alert-triangle') : `<span>${label}</span>`}
              </p>`;
          }
          document.getElementById('clientFinance').innerHTML = `
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="rounded-xl bg-gray-50 p-4 dark:bg-slate-800/50">
                <p class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400"><i data-lucide="trending-up" class="h-4 w-4 text-emerald-500"></i>Total Invertido</p>
                <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900 dark:text-white">${money.format(c.invested)}</p>
              </div>
              <div class="rounded-xl bg-gray-50 p-4 dark:bg-slate-800/50">
                <p class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400"><i data-lucide="hourglass" class="h-4 w-4 text-amber-500"></i>Saldo Pendiente</p>
                <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900 dark:text-white">${money.format(c.pending)}</p>
              </div>
            </div>
            <div>
              <div class="flex items-center justify-between text-xs font-medium text-slate-500 dark:text-slate-400"><span>Cuotas pagadas ${c.paidCount} de ${c.totalCount}</span><span class="tabular-nums">${paidPct}%</span></div>
              <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-slate-800" role="progressbar" aria-label="Avance de pagos" aria-valuenow="${paidPct}" aria-valuemin="0" aria-valuemax="100">
                <div class="h-full rounded-full bg-brand-600" style="width:${paidPct}%"></div>
              </div>
            </div>
            <div class="rounded-xl border border-gray-200 p-4 dark:border-slate-800">
              <p class="mb-2 flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400"><i data-lucide="calendar-clock" class="h-4 w-4"></i>Próximo Vencimiento</p>
              ${due}
            </div>`;
        }

        const payStatus = {
          Pagado: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/30',
          Pendiente: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/15 dark:text-yellow-300 dark:ring-yellow-500/30',
          Vencido: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/30',
        };
        const cardCls = 'rounded-2xl border border-gray-200/70 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900';

        function renderClientTabs(c) {
          document.getElementById('panel-propiedades').innerHTML = `<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">${c.properties.map((p) => `
            <article class="${cardCls} p-5">
              <div class="flex items-start justify-between gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-600/15 dark:text-brand-400"><i data-lucide="${p.type === 'Lote' ? 'map' : 'building'}" class="h-5 w-5"></i></span>
                ${badge(p.status, unitStatus[p.status].cls)}
              </div>
              <h3 class="mt-4 font-semibold text-slate-900 dark:text-white">${p.project}</h3>
              <p class="text-sm text-slate-500 dark:text-slate-400">${p.type} · ${p.code}</p>
              <dl class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-sm dark:border-slate-800">
                <div><dt class="text-xs text-slate-500 dark:text-slate-400">Área</dt><dd class="font-medium tabular-nums text-slate-900 dark:text-white">${p.area} m²</dd></div>
                <div class="text-right"><dt class="text-xs text-slate-500 dark:text-slate-400">Precio</dt><dd class="font-medium tabular-nums text-slate-900 dark:text-white">${money.format(p.price)}</dd></div>
              </dl>
            </article>`).join('')}</div>`;

          document.getElementById('panel-cronograma').innerHTML = `
            <article class="${cardCls} overflow-hidden">
              <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                  <caption class="sr-only">Cronograma de pagos</caption>
                  <thead class="border-b border-gray-200 bg-gray-50/80 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/40 dark:text-slate-400">
                    <tr><th scope="col" class="px-5 py-3">Cuota</th><th scope="col" class="px-5 py-3">Vencimiento</th><th scope="col" class="px-5 py-3 text-right">Monto</th><th scope="col" class="px-5 py-3">Estado</th></tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100 dark:divide-slate-800">${buildSchedule(c).map((r) => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40">
                      <td class="px-5 py-3 font-medium text-slate-900 dark:text-white">${r.n} / ${c.totalCount}</td>
                      <td class="px-5 py-3 tabular-nums ${r.status === 'Vencido' ? 'font-medium text-rose-600 dark:text-rose-400' : 'text-slate-600 dark:text-slate-300'}">${fmtDate(r.date)}</td>
                      <td class="px-5 py-3 text-right tabular-nums text-slate-900 dark:text-white">${money.format(r.amount)}</td>
                      <td class="px-5 py-3">${badge(r.status, payStatus[r.status])}</td>
                    </tr>`).join('')}
                  </tbody>
                </table>
              </div>
            </article>`;

        // DATOS (inyectados desde PHP): documentos del cliente (p. ej. window.clientDocs)
        const docs = window.clientDocs || [];
        const documentList = document.getElementById('panel-documentos');
        documentList.innerHTML = `
          <div class="flex justify-end">
            <button type="button" id="uploadClientDocument" data-client-id="${c.id}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
              <i data-lucide="plus" class="h-4 w-4"></i>Subir documento
            </button>
          </div>
          <ul class="${cardCls} mt-4 divide-y divide-gray-100 dark:divide-slate-800">${docs.map(([name, icon, size]) => `
            <li class="flex items-center gap-4 px-5 py-4">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400"><i data-lucide="${icon}" class="h-5 w-5"></i></span>
              <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium text-slate-900 dark:text-white">${name}</p><p class="text-xs text-slate-500 dark:text-slate-400">${size}</p></div>
              ${actionBtn('download', 'Descargar ' + name, 'hover:text-brand-600 dark:hover:text-brand-400')}
            </li>`).join('')}
          </ul>`;

        const uploadButton = document.getElementById('uploadClientDocument');
        uploadButton?.addEventListener('click', async () => {
          const clientId = Number(uploadButton.dataset.clientId || 0);
          const name = window.prompt('Nombre del documento', 'Contrato.pdf');
          if (!name || !clientId) return;
          try {
            const response = await fetch('?api=documentos', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': leadCsrfToken },
              body: JSON.stringify({
                action: 'create',
                cliente_id: clientId,
                nombre: name.trim(),
                tipo_archivo: 'file-signature',
                tamano: 'Nuevo archivo',
              })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'No se pudo guardar el documento.');
            const nextDocs = [...(window.clientDocs || []), [name.trim(), 'file-signature', 'Nuevo archivo']];
            window.clientDocs = nextDocs;
            renderClientTabs(currentClient);
          } catch (error) {
            window.alert(error.message || 'No se pudo guardar el documento.');
          }
        });
      }

        const clientTabs = [...document.querySelectorAll('.client-tab')];
        function selectTab(tab, focus) {
          clientTabs.forEach((t) => {
            const on = t === tab;
            t.setAttribute('aria-selected', String(on));
            t.tabIndex = on ? 0 : -1;
            document.getElementById(t.getAttribute('aria-controls')).classList.toggle('hidden', !on);
          });
          if (focus) tab.focus();
        }
        clientTabs.forEach((t) => t.addEventListener('click', () => selectTab(t)));
        document.getElementById('clientTabs').addEventListener('keydown', (e) => {
          const i = clientTabs.indexOf(document.activeElement);
          if (i < 0) return;
          const map = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: clientTabs.length - 1 };
          if (!(e.key in map)) return;
          e.preventDefault();
          selectTab(clientTabs[(map[e.key] + clientTabs.length) % clientTabs.length], true);
        });

        function openClient(id) {
          const c = clients.find((x) => x.id === id);
          if (!c) return;
          currentClient = c;
          document.getElementById('clientName').textContent = c.name;
          document.getElementById('clientDni').textContent = c.dni;
          document.getElementById('clientSince').textContent = c.since;
          document.getElementById('clientBadges').innerHTML =
            badge(c.status, clientStatus[c.status], 'badge-check') +
            badge('Asesor: ' + c.advisor, 'bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-600/15 dark:text-brand-400 dark:ring-brand-500/30', 'user-check');
          document.getElementById('clientPersonal').innerHTML = [
            infoRow('phone', 'Teléfono Principal', c.phone),
            infoRow('mail', 'Correo', c.email),
            infoRow('map-pin', 'Dirección', c.address),
            infoRow('briefcase', 'Ocupación', c.occupation),
            infoRow('heart', 'Estado Civil', c.civil),
          ].join('');
          renderFinance(c);
          renderClientContacts(c);
          renderClientTabs(c);
          selectTab(clientTabs[0]);
          renderBreadcrumbs(['Inicio', 'Clientes', c.name]);
          lucide.createIcons();
          window.scrollTo({ top: 0 });
          document.getElementById('clientName').focus({ preventScroll: true });
        }
        document.getElementById('backToClients').addEventListener('click', () => {
          // Sin enrutador SPA: "Volver" regresa al listado por URL real.
          window.location.href = '?view=clientes';
        });

        document.getElementById('addClientContactBtn')?.addEventListener('click', async () => {
          if (!currentClient) return;
          const type = window.prompt('Tipo de contacto', 'WhatsApp');
          const value = window.prompt('Valor del contacto', '999999999');
          if (!type || !value) return;

          try {
            const response = await fetch('?api=contactos', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': leadCsrfToken },
              body: JSON.stringify({
                action: 'create',
                cliente_id: currentClient.id,
                tipo_contacto: type.trim(),
                telefono: value.trim(),
                es_principal: (currentClient.contactos || []).length === 0 ? 1 : 0,
              })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'No se pudo guardar el contacto.');
            currentClient.contactos = result.contactos || [];
            renderClientContacts(currentClient);
          } catch (error) {
            window.alert(error.message || 'No se pudo guardar el contacto.');
          }
        });

        document.getElementById('clientActionsMenu').addEventListener('click', (e) => {
          const item = e.target.closest('[data-client-action]');
          if (!item || !currentClient) return;
          const c = currentClient;
          const action = item.dataset.clientAction;
          if (action === 'whatsapp') {
            const text = encodeURIComponent(`Hola ${c.name.split(' ')[0]}, le saludamos de Inmobi.`);
            window.open(`https://wa.me/${c.phone.replace(/\D/g, '')}?text=${text}`, '_blank', 'noopener');
          } else if (action === 'email') {
            window.location.href = `mailto:${c.email}`;
          } else if (action === 'statement') {
            const lines = [['Cuota', 'Vencimiento', 'Monto', 'Estado'], ...buildSchedule(c).map((r) => [`${r.n}/${c.totalCount}`, r.date.toISOString().slice(0, 10), r.amount, r.status])];
            const header = `Estado de Cuenta - ${c.name} (DNI ${c.dni})\nTotal invertido,${c.invested}\nSaldo pendiente,${c.pending}\n\n`;
            const blob = new Blob(['\ufeff' + header + lines.map((l) => l.join(',')).join('\n')], { type: 'text/csv;charset=utf-8' });
            const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(blob), download: `estado-cuenta-${c.dni}.csv` });
            a.click();
            URL.revokeObjectURL(a.href);
          } else if (action === 'separation') {
            closeAllMenus();
            openSeparation(c);
            return;
          }
          closeAllMenus();
        });

        // Si PHP sirvió la página de detalle con ?id=..., abre esa ficha al cargar.
        const detailId = Number(new URLSearchParams(window.location.search).get('id'));
        if (detailId) openClient(detailId);
      }
    }

