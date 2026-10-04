</section>
    </main>

    <!-- Modal: Registrar Separación -->
    <div id="sepModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="sepModalTitle">
      <div data-sep-close class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
      <div class="relative flex min-h-full items-end justify-center p-0 sm:items-center sm:p-6">
        <form id="sepForm" novalidate class="relative flex max-h-[100dvh] w-full max-w-3xl flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900 sm:max-h-[90vh] sm:rounded-2xl">
          <header class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5 dark:border-slate-800">
            <div class="flex items-center gap-3">
              <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-600/15 dark:text-brand-400"><i data-lucide="file-signature" class="h-5 w-5"></i></span>
              <div>
                <h2 id="sepModalTitle" class="text-lg font-semibold text-slate-900 dark:text-white">Registrar Separación de Unidad</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Completa los datos para reservar la unidad a nombre del cliente.</p>
              </div>
            </div>
            <button type="button" data-sep-close aria-label="Cerrar" class="rounded-lg p-2 text-slate-400 hover:bg-gray-100 hover:text-slate-600 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:hover:bg-slate-800 dark:hover:text-slate-200">
              <i data-lucide="x" class="h-5 w-5"></i>
            </button>
          </header>

          <div class="flex-1 space-y-6 overflow-y-auto px-6 py-6">
            <fieldset>
              <legend class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="user" class="h-3.5 w-3.5"></i>Datos del Cliente</legend>
              <div class="grid gap-4 md:grid-cols-2">
                <div class="relative">
                  <label for="sepClient" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar Cliente (DNI o Nombre) <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input id="sepClient" type="text" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="sepClientList" autocomplete="off" placeholder="Buscar por DNI o nombre" class="sep-input pl-9">
                  </div>
                  <ul id="sepClientList" role="listbox" class="absolute left-0 right-0 z-10 mt-1 hidden max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-900"></ul>
                  <p class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
                <div>
                  <label for="sepAdvisor" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Asesor Asignado</label>
                  <input id="sepAdvisor" type="text" readonly tabindex="-1" placeholder="Se asigna al elegir cliente" class="sep-input sep-static">
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="building-2" class="h-3.5 w-3.5"></i>Propiedad</legend>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label for="sepProject" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Proyecto <span class="text-rose-500">*</span></label>
                  <select id="sepProject" class="sep-input"><option value="">Selecciona un proyecto</option></select>
                  <p class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
                <div>
                  <label for="sepUnit" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Unidad <span class="text-rose-500">*</span></label>
                  <select id="sepUnit" disabled class="sep-input"><option value="">Primero elige un proyecto</option></select>
                  <p class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
                <div class="md:col-span-2">
                  <label for="sepListPrice" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Precio de Lista (S/)</label>
                  <input id="sepListPrice" type="text" readonly tabindex="-1" placeholder="—" class="sep-input sep-static font-semibold tabular-nums">
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="receipt" class="h-3.5 w-3.5"></i>Condiciones</legend>
              <div class="grid gap-4 md:grid-cols-2">
                <div data-invalid="true">
                  <label for="sepAmount" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Importe de Separación (S/) <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400">S/</span>
                    <input id="sepAmount" type="number" min="1" step="0.01" inputmode="decimal" placeholder="0.00" aria-invalid="true" aria-describedby="sepAmountError" class="sep-input pl-9 tabular-nums">
                  </div>
                  <p id="sepAmountError" class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
                <div>
                  <label for="sepMethod" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Medio de Pago <span class="text-rose-500">*</span></label>
                  <select id="sepMethod" class="sep-input">
                    <option value="">Selecciona un medio</option>
                    <option>Transferencia bancaria</option>
                    <option>Depósito en cuenta</option>
                    <option>Efectivo</option>
                    <option>Tarjeta de crédito/débito</option>
                    <option>Yape / Plin</option>
                  </select>
                  <p class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
                <div>
                  <label for="sepDue" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-200">Vencimiento de la Separación <span class="text-rose-500">*</span></label>
                  <input id="sepDue" type="date" class="sep-input dark:[color-scheme:dark]">
                  <p class="sep-error mt-1.5 hidden items-center gap-1 text-xs font-medium text-rose-600 dark:text-rose-400"><i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>Campo obligatorio</p>
                </div>
              </div>
            </fieldset>

            <fieldset>
              <legend class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><i data-lucide="paperclip" class="h-3.5 w-3.5"></i>Archivos</legend>
              <label id="sepDrop" for="sepFile" class="flex cursor-pointer items-center gap-4 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50/60 px-5 py-4 transition-colors hover:border-brand-400 hover:bg-brand-50/40 dark:border-slate-700 dark:bg-slate-800/40 dark:hover:border-brand-500 dark:hover:bg-brand-600/10">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-brand-600 shadow-sm ring-1 ring-gray-200 dark:bg-slate-900 dark:text-brand-400 dark:ring-slate-700"><i data-lucide="upload-cloud" class="h-5 w-5"></i></span>
                <span class="min-w-0 text-sm">
                  <span class="block font-medium text-slate-700 dark:text-slate-200"><span class="text-brand-600 dark:text-brand-400">Haz clic para subir</span> o arrastra y suelta el comprobante</span>
                  <span class="block text-xs text-slate-500 dark:text-slate-400">PDF, JPG o PNG · máx. 5 MB</span>
                </span>
                <input id="sepFile" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
              </label>
              <div id="sepFileInfo" class="mt-2 hidden items-center gap-3 rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-slate-700">
                <i data-lucide="file-check-2" class="h-4 w-4 shrink-0 text-emerald-500"></i>
                <span id="sepFileName" class="min-w-0 flex-1 truncate text-slate-700 dark:text-slate-200"></span>
                <button type="button" id="sepFileRemove" aria-label="Quitar archivo" class="rounded p-1 text-slate-400 hover:bg-gray-100 hover:text-rose-500 dark:hover:bg-slate-800"><i data-lucide="trash-2" class="h-4 w-4"></i></button>
              </div>
              <p id="sepFileError" class="mt-1.5 hidden text-xs font-medium text-rose-600 dark:text-rose-400"></p>
            </fieldset>
          </div>

          <footer class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50/80 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/60 sm:flex-row sm:justify-end">
            <button type="button" data-sep-close class="inline-flex h-10 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 px-4 text-sm font-semibold text-slate-700 hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-slate-400/20 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Cancelar</button>
            <button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-brand-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
              <i data-lucide="save" class="h-4 w-4"></i>Guardar Separación
            </button>
          </footer>
        </form>
      </div>
    </div>

    <div id="sepToast" role="status" aria-live="polite" class="fixed bottom-6 right-6 z-[60] hidden items-center gap-3 rounded-xl bg-slate-900 px-4 py-3 text-sm font-medium text-white shadow-lg dark:bg-white dark:text-slate-900">
      <i data-lucide="check-circle-2" class="h-5 w-5 text-emerald-400 dark:text-emerald-600"></i><span id="sepToastText"></span>
    </div>

    <div id="profileModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="profileModalTitle">
      <div data-profile-close class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
      <div class="relative flex min-h-full items-center justify-center p-4">
        <form id="profileForm" class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Perfil</p>
              <h2 id="profileModalTitle" class="mt-2 text-xl font-bold text-slate-900 dark:text-white">Mi perfil</h2>
            </div>
            <button type="button" data-profile-close aria-label="Cerrar perfil" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200"><i data-lucide="x" class="h-5 w-5"></i></button>
          </div>

          <div class="mt-6 flex flex-col items-center gap-4">
            <div class="relative">
              <img id="profileAvatarPreview" src="" alt="Vista previa de foto" class="hidden h-24 w-24 rounded-full object-cover ring-4 ring-slate-200 dark:ring-slate-700" />
              <div id="profileAvatarFallback" class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-2xl font-bold text-white"><?= htmlspecialchars($headerInitials, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
            </div>
            <label id="profileAvatarLabel" for="profileAvatarInput" class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
              <i data-lucide="image-plus" class="h-4 w-4"></i>
              Cambiar foto
              <input id="profileAvatarInput" type="file" accept="image/*" class="hidden" />
            </label>
          </div>

          <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <label class="text-sm font-medium text-slate-700 dark:text-slate-200 sm:col-span-2">
              Nombre completo
              <input id="profileName" name="name" required maxlength="100" value="<?= $headerName ?>" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950" />
            </label>
            <label class="text-sm font-medium text-slate-700 dark:text-slate-200 sm:col-span-2">
              Correo electrónico
              <input id="profileEmail" name="email" required type="email" maxlength="120" value="<?= $headerEmail ?>" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950" />
            </label>
            <label class="text-sm font-medium text-slate-700 dark:text-slate-200 sm:col-span-2">
              Nueva contraseña (opcional)
              <input id="profilePassword" name="password" type="password" minlength="12" maxlength="4096" placeholder="Deja vacío si no quieres cambiarla" class="mt-1.5 h-10 w-full rounded-md border border-slate-300 bg-white px-3 dark:border-slate-700 dark:bg-slate-950" />
            </label>
          </div>

          <div class="mt-6 flex justify-end gap-3">
            <button type="button" data-profile-close class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Cancelar</button>
            <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>

    <footer class="px-4 pb-6 text-center text-xs text-slate-400 sm:px-6 lg:px-8">
      © 2026 Inmobi CRM · Todos los derechos reservados
    </footer>
  </div>

  <script src="js/main.js"></script>
  <script src="js/dashboard.js"></script>
  <script src="js/inventory.js"></script>
  <script src="js/crm.js"></script>
  <script src="js/clients.js"></script>
  <script src="js/separation.js"></script>
  <script src="js/projects.js"></script>
  <script src="js/sales.js"></script>
  <script src="js/caja.js"></script>
  <script src="js/cobranzas.js"></script>
  <script src="js/comisiones.js"></script>
  <script src="js/campanas.js"></script>
  <script src="js/alertas.js"></script>
  <script src="js/cartera.js"></script>
  <script src="js/rendimiento.js"></script>
  <script src="js/expedientes.js"></script>
  <script src="js/contratos.js"></script>
  <script src="js/recordatorios.js"></script>
  <script src="js/notificaciones.js"></script>
  <script src="js/seguimiento.js"></script>
  <script src="js/tareas.js"></script>
  <script src="js/agenda.js"></script>
  <script src="js/pipeline.js"></script>
  <script src="js/prospeccion.js"></script>
  <script src="js/soporte.js"></script>
  <script src="js/incidencias.js"></script>
  <script src="js/reportes.js"></script>
  <script>
    (() => {
      const profileMenu = document.getElementById('profileMenu');
      const profileOpenModal = document.getElementById('profileOpenModal');
      const profileModal = document.getElementById('profileModal');
      const profileForm = document.getElementById('profileForm');
      const avatarLabel = document.getElementById('profileAvatarLabel');
      const avatarInput = document.getElementById('profileAvatarInput');
      const avatarPreview = document.getElementById('profileAvatarPreview');
      const avatarFallback = document.getElementById('profileAvatarFallback');
      const currentAvatar = <?= json_encode($headerAvatar, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;

      const setAvatarPreview = (src) => {
        if (!src) {
          avatarPreview?.classList.add('hidden');
          avatarFallback?.classList.remove('hidden');
          return;
        }
        avatarPreview.src = src;
        avatarPreview?.classList.remove('hidden');
        avatarFallback?.classList.add('hidden');
      };

      const compressAvatar = async (dataUrl) => {
        const maxChars = 50000;
        if (!dataUrl || dataUrl.length <= maxChars) {
          return dataUrl;
        }
        return new Promise((resolve) => {
          const image = new Image();
          image.onload = () => {
            const canvas = document.createElement('canvas');
            const maxWidth = 900;
            const maxHeight = 900;
            const scale = Math.min(1, maxWidth / image.width, maxHeight / image.height);
            canvas.width = Math.max(1, Math.round(image.width * scale));
            canvas.height = Math.max(1, Math.round(image.height * scale));
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            const compressed = canvas.toDataURL('image/jpeg', 0.72);
            resolve(compressed.length <= maxChars ? compressed : canvas.toDataURL('image/jpeg', 0.45));
          };
          image.src = dataUrl;
        });
      };

      if (currentAvatar) {
        setAvatarPreview(currentAvatar);
      }

      profileOpenModal?.addEventListener('click', (event) => {
        event.preventDefault();
        profileMenu?.classList.add('hidden');
        profileModal?.classList.remove('hidden');
      });

      document.querySelectorAll('[data-profile-close]').forEach((button) => {
        button.addEventListener('click', () => profileModal?.classList.add('hidden'));
      });

      avatarLabel?.addEventListener('click', () => {
        avatarInput?.click();
      });

      avatarInput?.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = async () => {
          const dataUrl = typeof reader.result === 'string' ? reader.result : '';
          const compressed = await compressAvatar(dataUrl);
          setAvatarPreview(compressed);
        };
        reader.readAsDataURL(file);
      });

      profileForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(profileForm);
        const payload = {
          action: 'profile_update',
          name: formData.get('name') || '',
          email: formData.get('email') || '',
          password: formData.get('password') || '',
          avatar: avatarPreview?.classList.contains('hidden') ? '' : avatarPreview?.src || ''
        };
        const response = await fetch('?api=configuracion', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?> },
          body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok) {
          alert(result.message || 'No se pudo guardar el perfil.');
          return;
        }
        window.location.reload();
      });
    })();
  </script>
</body>
</html>