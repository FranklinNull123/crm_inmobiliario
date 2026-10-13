<?php
$mapaData = $mapaData ?? ['departments' => [], 'provinces' => [], 'districts' => [], 'projects' => [], 'leads' => [], 'mapData' => []];
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<div class="space-y-6">
  <div>
    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Ubicación comercial</p>
    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Mapa Comercial del Perú</h1>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Explora inventario, ventas y leads por departamento, provincia y distrito.</p>
  </div>

  <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="grid gap-4 md:grid-cols-3">
      <div>
        <label for="mapDepartment" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Departamento</label>
        <select id="mapDepartment" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-800">
          <option value="">Todo el Perú</option>
          <?php foreach ($mapaData['departments'] as $department): ?>
            <option value="<?= htmlspecialchars((string) $department['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(trim((string) $department['name']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="mapProvince" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Provincia</label>
        <select id="mapProvince" disabled class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800">
          <option value="">Todas las provincias</option>
        </select>
      </div>
      <div>
        <label for="mapDistrict" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Distrito</label>
        <select id="mapDistrict" disabled class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800">
          <option value="">Todos los distritos</option>
        </select>
      </div>
    </div>
    <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-600 dark:text-slate-300">
      <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Propiedades / inventario</span>
      <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>Ventas</span>
      <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Leads interesados</span>
    </div>
  </section>

  <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(340px,0.8fr)]">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
        <div>
          <h2 class="font-semibold text-slate-900 dark:text-white">Mapa</h2>
          <p id="mapHint" class="mt-1 text-xs text-slate-500 dark:text-slate-400">Elige una ubicación para filtrar. Usa “Marcar punto” para ubicar un proyecto o lead.</p>
        </div>
        <button id="mapPlaceToggle" type="button" class="inline-flex h-9 items-center justify-center gap-2 rounded-lg border border-gray-200 px-3 text-sm font-semibold text-slate-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
          Marcar punto
        </button>
      </div>
      <div id="commercialMap" class="h-[420px] w-full bg-slate-100 dark:bg-slate-800" role="application" aria-label="Mapa interactivo del Perú"></div>
      <p id="mapStatus" role="status" aria-live="polite" class="hidden border-t border-slate-200 px-5 py-3 text-sm dark:border-slate-800"></p>
    </section>

    <section class="space-y-5">
      <form id="mapLocationForm" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div>
          <h2 class="font-semibold text-slate-900 dark:text-white">Asignar ubicación</h2>
          <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Selecciona un proyecto o lead, el distrito y marca su punto exacto en el mapa.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <div>
            <label for="mapEntityType" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Registro</label>
            <select id="mapEntityType" class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-800">
              <option value="project">Proyecto / propiedades</option>
              <option value="lead">Lead interesado</option>
            </select>
          </div>
          <div>
            <label for="mapEntity" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Seleccionar</label>
            <select id="mapEntity" required class="h-10 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-slate-700 dark:bg-slate-800"></select>
          </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <div>
            <label for="assignDepartment" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Departamento</label>
            <select id="assignDepartment" required class="h-10 w-full rounded-lg border border-gray-200 bg-white px-2 text-sm dark:border-slate-700 dark:bg-slate-800">
              <option value="">Selecciona</option>
              <?php foreach ($mapaData['departments'] as $department): ?>
                <option value="<?= htmlspecialchars((string) $department['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(trim((string) $department['name']), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="assignProvince" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Provincia</label>
            <select id="assignProvince" required disabled class="h-10 w-full rounded-lg border border-gray-200 bg-white px-2 text-sm disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800"><option value="">Selecciona</option></select>
          </div>
          <div>
            <label for="assignDistrict" class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Distrito</label>
            <select id="assignDistrict" name="district" required disabled class="h-10 w-full rounded-lg border border-gray-200 bg-white px-2 text-sm disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800"><option value="">Selecciona</option></select>
          </div>
        </div>
        <input id="mapLatitude" name="latitude" type="hidden">
        <input id="mapLongitude" name="longitude" type="hidden">
        <p id="mapCoordinates" class="text-xs text-slate-500 dark:text-slate-400">No hay punto exacto seleccionado; se guardará solo el distrito.</p>
        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">Guardar ubicación</button>
      </form>

      <div class="grid grid-cols-3 gap-3">
        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs text-slate-500 dark:text-slate-400">Disponibles</p><p id="mapAvailableCount" class="mt-2 text-2xl font-bold text-emerald-600">0</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs text-slate-500 dark:text-slate-400">Ventas</p><p id="mapSalesCount" class="mt-2 text-2xl font-bold text-blue-600">0</p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
          <p class="text-xs text-slate-500 dark:text-slate-400">Leads</p><p id="mapLeadsCount" class="mt-2 text-2xl font-bold text-amber-600">0</p>
        </article>
      </div>
    </section>
  </div>

  <section class="grid gap-5 lg:grid-cols-3">
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="font-semibold text-slate-900 dark:text-white">Propiedades e inventario</h2>
      <div id="mapProjectsList" class="mt-3 space-y-2"></div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="font-semibold text-slate-900 dark:text-white">Ventas</h2>
      <div id="mapSalesList" class="mt-3 space-y-2"></div>
    </article>
    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="font-semibold text-slate-900 dark:text-white">Leads interesados</h2>
      <div id="mapLeadsList" class="mt-3 space-y-2"></div>
    </article>
  </section>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const departments = <?= json_encode($mapaData['departments'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const provinces = <?= json_encode($mapaData['provinces'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const districts = <?= json_encode($mapaData['districts'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const projects = <?= json_encode($mapaData['projects'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const leads = <?= json_encode($mapaData['leads'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const mapData = <?= json_encode($mapaData['mapData'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const csrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const districtLocations = new Map(districts.map((district) => [district.id, district]));

    const map = L.map('commercialMap').setView([-9.19, -75.015], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    const markerLayer = L.layerGroup().addTo(map);
    let placingPoint = false;
    let previewMarker = null;

    const el = (id) => document.getElementById(id);
    const addOptions = (select, rows, placeholder) => {
      select.replaceChildren(new Option(placeholder, ''));
      rows.forEach((row) => select.add(new Option(row.name.trim(), row.id)));
    };
    const wireHierarchy = (department, province, district) => {
      department.addEventListener('change', () => {
        const rows = provinces.filter((item) => item.department_id === department.value);
        addOptions(province, rows, 'Todas las provincias');
        province.disabled = !department.value;
        addOptions(district, [], 'Todos los distritos');
        district.disabled = true;
      });
      province.addEventListener('change', () => {
        const rows = districts.filter((item) => item.province_id === province.value);
        addOptions(district, rows, 'Todos los distritos');
        district.disabled = !province.value;
      });
    };

    const mapDepartment = el('mapDepartment');
    const mapProvince = el('mapProvince');
    const mapDistrict = el('mapDistrict');
    const assignDepartment = el('assignDepartment');
    const assignProvince = el('assignProvince');
    const assignDistrict = el('assignDistrict');
    wireHierarchy(mapDepartment, mapProvince, mapDistrict);
    wireHierarchy(assignDepartment, assignProvince, assignDistrict);

    const entityType = el('mapEntityType');
    const entity = el('mapEntity');
    const setCoordinates = (latitude, longitude) => {
      el('mapLatitude').value = latitude;
      el('mapLongitude').value = longitude;
      el('mapCoordinates').textContent = latitude && longitude
        ? `Punto seleccionado: ${Number(latitude).toFixed(6)}, ${Number(longitude).toFixed(6)}`
        : 'No hay punto exacto seleccionado; se guardará solo el distrito.';
    };
    const setAssignmentDistrict = (code) => {
      if (!code) {
        assignDepartment.value = '';
        assignDepartment.dispatchEvent(new Event('change'));
        return;
      }
      assignDepartment.value = code.slice(0, 2);
      assignDepartment.dispatchEvent(new Event('change'));
      assignProvince.value = code.slice(0, 4);
      assignProvince.dispatchEvent(new Event('change'));
      assignDistrict.value = code;
    };
    const updateEntities = () => {
      entity.replaceChildren();
      const options = entityType.value === 'project' ? projects : leads;
      options.forEach((item) => {
        const option = new Option(item.name + (item.project ? ' · ' + item.project : ''), item.id);
        option.dataset.district = item.district || '';
        option.dataset.latitude = item.latitude || '';
        option.dataset.longitude = item.longitude || '';
        entity.add(option);
      });
      if (options.length === 0) entity.add(new Option('No hay registros disponibles', ''));
      const selected = entity.selectedOptions[0];
      setAssignmentDistrict(selected?.dataset.district || '');
      setCoordinates(selected?.dataset.latitude || '', selected?.dataset.longitude || '');
    };
    entityType.addEventListener('change', updateEntities);
    updateEntities();

    entity.addEventListener('change', () => {
      const selected = entity.selectedOptions[0];
      setAssignmentDistrict(selected?.dataset.district || '');
      setCoordinates(selected?.dataset.latitude || '', selected?.dataset.longitude || '');
    });

    const textElement = (tag, text, className = '') => {
      const node = document.createElement(tag);
      node.textContent = text;
      if (className) node.className = className;
      return node;
    };
    const matchesDistrict = (code, selected) => !selected || (code && code.startsWith(selected));
    const selectedGeoCode = () => mapDistrict.value || mapProvince.value || mapDepartment.value;
    const renderList = (target, rows, label, emptyText) => {
      target.replaceChildren();
      if (!rows.length) {
        target.append(textElement('p', emptyText, 'text-sm text-slate-500 dark:text-slate-400'));
        return;
      }
      rows.forEach((row) => target.append(textElement('p', label(row), 'rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-200')));
    };
    const addMarker = (row, color, title, detail) => {
      const districtLocation = districtLocations.get(row.district);
      const latitude = row.latitude !== null && row.latitude !== '' ? row.latitude : districtLocation?.latitude;
      const longitude = row.longitude !== null && row.longitude !== '' ? row.longitude : districtLocation?.longitude;
      if (latitude === null || longitude === null || latitude === undefined || longitude === undefined) return;
      const marker = L.circleMarker([Number(latitude), Number(longitude)], {
        radius: 8, color, fillColor: color, fillOpacity: 0.8, weight: 2
      });
      const popup = document.createElement('div');
      popup.append(textElement('strong', title));
      const districtName = districtLocation ? ` · ${districtLocation.name}` : '';
      popup.append(textElement('div', detail + districtName));
      marker.bindPopup(popup).addTo(markerLayer);
    };

    const focusScope = () => {
      const code = selectedGeoCode();
      if (!code) {
        map.setView([-9.19, -75.015], 5);
        return;
      }
      const locations = districts.filter((district) => district.id.startsWith(code) && district.latitude !== null && district.longitude !== null);
      if (locations.length === 0) return;
      const latitude = locations.reduce((sum, item) => sum + Number(item.latitude), 0) / locations.length;
      const longitude = locations.reduce((sum, item) => sum + Number(item.longitude), 0) / locations.length;
      const zoom = code.length === 2 ? 6 : code.length === 4 ? 9 : 12;
      map.setView([latitude, longitude], zoom);
    };

    const refresh = () => {
      const code = selectedGeoCode();
      const selectedProjects = mapData.projects.filter((item) => matchesDistrict(item.district, code));
      const selectedSales = mapData.sales.filter((item) => matchesDistrict(item.district, code));
      const selectedLeads = mapData.leads.filter((item) => matchesDistrict(item.district, code));
      el('mapAvailableCount').textContent = selectedProjects.reduce((total, item) => total + Number(item.units_available || 0), 0);
      el('mapSalesCount').textContent = selectedSales.length;
      el('mapLeadsCount').textContent = selectedLeads.length;
      renderList(el('mapProjectsList'), selectedProjects, (item) => `${item.name} · ${item.units_available || 0} disponibles de ${item.units_total || 0}`, 'No hay proyectos con ubicación en esta zona.');
      renderList(el('mapSalesList'), selectedSales, (item) => `${item.client} · ${item.unit} · ${item.date}`, 'No hay ventas asociadas a esta zona.');
      renderList(el('mapLeadsList'), selectedLeads, (item) => `${item.name} · ${item.project || item.status}`, 'No hay leads interesados registrados en esta zona.');

      markerLayer.clearLayers();
      selectedProjects.forEach((item) => addMarker(item, '#10b981', item.name, `${item.units_available || 0} propiedades disponibles`));
      selectedSales.forEach((item) => addMarker(item, '#3b82f6', item.client, `Venta de ${item.unit} · ${item.project}`));
      selectedLeads.forEach((item) => addMarker(item, '#f59e0b', item.name, item.project || 'Lead interesado'));
    };
    [mapDepartment, mapProvince, mapDistrict].forEach((select) => select.addEventListener('change', () => {
      refresh();
      focusScope();
    }));
    refresh();

    el('mapPlaceToggle').addEventListener('click', () => {
      placingPoint = !placingPoint;
      el('mapPlaceToggle').textContent = placingPoint ? 'Cancelar punto' : 'Marcar punto';
      el('mapHint').textContent = placingPoint ? 'Haz clic en el mapa para guardar la ubicación exacta del registro seleccionado.' : 'Elige una ubicación para filtrar. Usa “Marcar punto” para ubicar un proyecto o lead.';
    });
    map.on('click', (event) => {
      if (!placingPoint) return;
      const { lat, lng } = event.latlng;
      setCoordinates(lat, lng);
      if (previewMarker) map.removeLayer(previewMarker);
      previewMarker = L.circleMarker([lat, lng], { radius: 9, color: '#7c3aed', fillColor: '#8b5cf6', fillOpacity: 0.9 }).addTo(map);
      placingPoint = false;
      el('mapPlaceToggle').textContent = 'Marcar punto';
      el('mapHint').textContent = 'Punto marcado. Guarda la ubicación para asociarlo al registro.';
    });

    el('mapLocationForm').addEventListener('submit', async (event) => {
      event.preventDefault();
      const status = el('mapStatus');
      status.classList.remove('hidden', 'text-rose-600', 'text-emerald-600');
      status.textContent = 'Guardando ubicación…';
      try {
        const response = await fetch('?api=mapa_ubicacion', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
          body: JSON.stringify({
            type: entityType.value,
            id: entity.value,
            district: assignDistrict.value,
            latitude: el('mapLatitude').value,
            longitude: el('mapLongitude').value
          })
        });
        const result = await response.json();
        if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo guardar la ubicación.');
        status.classList.add('text-emerald-600');
        status.textContent = 'Ubicación guardada. Actualizando el mapa…';
        window.location.reload();
      } catch (error) {
        status.classList.add('text-rose-600');
        status.textContent = error instanceof Error ? error.message : 'Ocurrió un error al guardar la ubicación.';
      }
    });
  });
</script>
