    // SEPARACIONES: valida el formulario y actualiza estado solo en memoria.
    // Todavía no guarda separaciones en la base de datos.
    // ---------- Modal: Registrar Separación ----------
    const sepModal = document.getElementById('sepModal');
    const sepForm = document.getElementById('sepForm');
    const sepClient = document.getElementById('sepClient');
    const sepClientList = document.getElementById('sepClientList');
    const sepAdvisor = document.getElementById('sepAdvisor');
    const sepProject = document.getElementById('sepProject');
    const sepUnit = document.getElementById('sepUnit');
    const sepListPrice = document.getElementById('sepListPrice');
    const sepAmount = document.getElementById('sepAmount');
    const sepMethod = document.getElementById('sepMethod');
    const sepDue = document.getElementById('sepDue');
    const sepFile = document.getElementById('sepFile');
    const sepDrop = document.getElementById('sepDrop');
    const sepFileInfo = document.getElementById('sepFileInfo');
    const sepFileError = document.getElementById('sepFileError');
    // DATOS (inyectados desde PHP): sepUnits — unidades del modal de separación.
    let sepSelectedClient = null;
    let sepActiveIndex = -1;
    let sepLastFocus = null;
    const isoDate = (d) => new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 10);

    sepProject.innerHTML += [...new Set(sepUnits.map((u) => u.project))].map((p) => `<option>${p}</option>`).join('');

    const fieldWrap = (el) => el.closest('div:has(> label)');
    function setInvalid(el, invalid) {
      const wrap = fieldWrap(el);
      wrap.dataset.invalid = String(invalid);
      el.setAttribute('aria-invalid', String(invalid));
    }

    function renderClientOptions(query) {
      const q = query.trim().toLowerCase();
      const matches = clients.filter((c) => !q || c.name.toLowerCase().includes(q) || c.dni.includes(q)).slice(0, 6);
      sepActiveIndex = -1;
      sepClientList.innerHTML = matches.length
        ? matches.map((c, i) => `<li id="sepOpt-${c.id}" role="option" data-client-id="${c.id}" aria-selected="false" class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 text-sm hover:bg-gray-100 aria-selected:bg-brand-50 dark:hover:bg-slate-800 dark:aria-selected:bg-brand-600/15">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700 dark:bg-brand-600/20 dark:text-brand-300">${c.name.split(' ').slice(0, 2).map((n) => n[0]).join('')}</span>
            <span class="min-w-0"><span class="block truncate font-medium text-slate-900 dark:text-white">${c.name}</span><span class="block text-xs text-slate-500 dark:text-slate-400">DNI ${c.dni}</span></span>
          </li>`).join('')
        : '<li class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400">Sin coincidencias</li>';
      sepClientList.classList.remove('hidden');
      sepClient.setAttribute('aria-expanded', 'true');
    }
    function closeClientList() {
      sepClientList.classList.add('hidden');
      sepClient.setAttribute('aria-expanded', 'false');
      sepClient.removeAttribute('aria-activedescendant');
    }
    function selectClient(c) {
      sepSelectedClient = c;
      sepClient.value = `${c.name} · ${c.dni}`;
      sepAdvisor.value = c.advisor;
      setInvalid(sepClient, false);
      closeClientList();
    }
    function highlightOption(i) {
      const opts = [...sepClientList.querySelectorAll('[role="option"]')];
      if (!opts.length) return;
      sepActiveIndex = (i + opts.length) % opts.length;
      opts.forEach((o, idx) => o.setAttribute('aria-selected', String(idx === sepActiveIndex)));
      sepClient.setAttribute('aria-activedescendant', opts[sepActiveIndex].id);
      opts[sepActiveIndex].scrollIntoView({ block: 'nearest' });
    }
    sepClient.addEventListener('input', () => {
      sepSelectedClient = null;
      sepAdvisor.value = '';
      renderClientOptions(sepClient.value);
    });
    sepClient.addEventListener('focus', () => { if (!sepSelectedClient) renderClientOptions(sepClient.value); });
    sepClient.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown') { e.preventDefault(); if (sepClientList.classList.contains('hidden')) renderClientOptions(sepClient.value); highlightOption(sepActiveIndex + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); highlightOption(sepActiveIndex - 1); }
      else if (e.key === 'Enter' && !sepClientList.classList.contains('hidden')) {
        if (e.nativeEvent?.isComposing || e.isComposing || e.keyCode === 229) return;
        e.preventDefault();
        const opt = sepClientList.querySelectorAll('[role="option"]')[Math.max(sepActiveIndex, 0)];
        if (opt) selectClient(clients.find((c) => c.id === Number(opt.dataset.clientId)));
      } else if (e.key === 'Escape' && !sepClientList.classList.contains('hidden')) { e.stopPropagation(); closeClientList(); }
    });
    sepClientList.addEventListener('mousedown', (e) => {
      const opt = e.target.closest('[role="option"]');
      if (!opt) return;
      e.preventDefault();
      selectClient(clients.find((c) => c.id === Number(opt.dataset.clientId)));
    });
    sepClient.addEventListener('blur', () => setTimeout(closeClientList, 100));

    sepProject.addEventListener('change', () => {
      const list = sepUnits.filter((u) => u.project === sepProject.value);
      sepUnit.disabled = !list.length;
      sepUnit.innerHTML = `<option value="">${list.length ? 'Selecciona una unidad' : 'Primero elige un proyecto'}</option>` +
        list.map((u) => `<option value="${u.id}" ${u.status !== 'Disponible' ? 'disabled' : ''}>${u.code} · ${u.type} ${u.area} m²${u.status !== 'Disponible' ? ` (${u.status})` : ''}</option>`).join('');
      sepListPrice.value = '';
      if (sepProject.value) setInvalid(sepProject, false);
    });
    sepUnit.addEventListener('change', () => {
      const u = sepUnits.find((x) => Number(x.id) === Number(sepUnit.value));
      sepListPrice.value = u ? money.format(u.price) : '';
      if (u) setInvalid(sepUnit, false);
    });
    sepAmount.addEventListener('input', () => { if (Number(sepAmount.value) > 0) setInvalid(sepAmount, false); });
    sepMethod.addEventListener('change', () => { if (sepMethod.value) setInvalid(sepMethod, false); });
    sepDue.addEventListener('change', () => { if (sepDue.value) setInvalid(sepDue, false); });

    function setFile(file) {
      sepFileError.classList.add('hidden');
      if (!file) { sepFile.value = ''; sepFileInfo.classList.add('hidden'); sepFileInfo.classList.remove('flex'); return; }
      const okType = /\.(pdf|jpe?g|png)$/i.test(file.name);
      if (!okType || file.size > 5 * 1024 * 1024) {
        sepFileError.textContent = okType ? 'El archivo supera los 5 MB.' : 'Formato no permitido. Usa PDF, JPG o PNG.';
        sepFileError.classList.remove('hidden');
        return;
      }
      document.getElementById('sepFileName').textContent = `${file.name} (${(file.size / 1024).toFixed(0)} KB)`;
      sepFileInfo.classList.remove('hidden');
      sepFileInfo.classList.add('flex');
    }
    sepFile.addEventListener('change', () => setFile(sepFile.files[0]));
    document.getElementById('sepFileRemove').addEventListener('click', () => setFile(null));
    ['dragenter', 'dragover'].forEach((ev) => sepDrop.addEventListener(ev, (e) => { e.preventDefault(); sepDrop.classList.add('border-brand-500', 'bg-brand-50/60'); }));
    ['dragleave', 'drop'].forEach((ev) => sepDrop.addEventListener(ev, (e) => { e.preventDefault(); sepDrop.classList.remove('border-brand-500', 'bg-brand-50/60'); }));
    sepDrop.addEventListener('drop', (e) => {
      const file = e.dataTransfer.files[0];
      if (!file) return;
      const dt = new DataTransfer();
      dt.items.add(file);
      sepFile.files = dt.files;
      setFile(file);
    });

    function resetSeparation() {
      sepForm.reset();
      sepSelectedClient = null;
      sepProject.dispatchEvent(new Event('change'));
      setFile(null);
      sepForm.querySelectorAll('[data-invalid]').forEach((w) => { w.dataset.invalid = 'false'; });
      sepForm.querySelectorAll('[aria-invalid]').forEach((el) => el.setAttribute('aria-invalid', 'false'));
      const today = new Date();
      sepDue.min = isoDate(today);
      sepDue.value = isoDate(new Date(today.getTime() + 7 * 864e5));
      // Validación visual de ejemplo solicitada
      setInvalid(sepAmount, true);
    }
    function openSeparation(prefillClient) {
      sepLastFocus = document.activeElement;
      resetSeparation();
      if (prefillClient) selectClient(prefillClient);
      sepModal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      lucide.createIcons();
      (prefillClient ? sepProject : sepClient).focus();
    }
    function closeSeparation() {
      sepModal.classList.add('hidden');
      closeClientList();
      document.body.style.overflow = '';
      sepLastFocus?.focus();
    }
    document.querySelectorAll('[data-open-separation]').forEach((b) => b.addEventListener('click', () => openSeparation()));
    sepModal.querySelectorAll('[data-sep-close]').forEach((b) => b.addEventListener('click', closeSeparation));
    sepModal.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { e.stopPropagation(); closeSeparation(); return; }
      if (e.key !== 'Tab') return;
      const focusables = [...sepForm.querySelectorAll('button, input:not([tabindex="-1"]), select, [href]')].filter((el) => !el.disabled && el.offsetParent !== null);
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    sepForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const checks = [
        [sepClient, !!sepSelectedClient],
        [sepProject, !!sepProject.value],
        [sepUnit, !!sepUnit.value],
        [sepAmount, Number(sepAmount.value) > 0],
        [sepMethod, !!sepMethod.value],
        [sepDue, !!sepDue.value],
      ];
      checks.forEach(([el, ok]) => setInvalid(el, !ok));
      const firstBad = checks.find(([, ok]) => !ok);
      if (firstBad) { firstBad[0].focus(); return; }

      const unit = sepUnits.find((u) => Number(u.id) === Number(sepUnit.value));
      if (!unit || !sepSelectedClient) {
        return;
      }

      try {
        const response = await fetch('?api=separacion', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': leadCsrfToken },
          body: JSON.stringify({
            action: 'create',
            cliente_id: Number(sepSelectedClient.id),
            unidad_id: Number(unit.id),
            monto: Number(sepAmount.value),
            metodo_pago: sepMethod.value,
            fecha_vencimiento: sepDue.value,
            observaciones: `Separación registrada desde el CRM por ${sepSelectedClient.name}`,
          }),
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'No se pudo registrar la separación.');
        if (typeof renderInventory === 'function') renderInventory();
        closeSeparation();
        document.getElementById('sepToastText').textContent = `Separación de ${unit.code} registrada para ${sepSelectedClient.name.split(' ')[0]}.`;
        const toast = document.getElementById('sepToast');
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        setTimeout(() => { toast.classList.add('hidden'); toast.classList.remove('flex'); }, 3500);
      } catch (error) {
        window.alert(error.message || 'No se pudo registrar la separación.');
      }
    });

