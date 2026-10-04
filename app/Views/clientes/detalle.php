<!-- Ficha del Cliente -->
<div id="clientDetailView">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <button id="backToClients" type="button" class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-slate-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
      <i data-lucide="arrow-left" class="h-4 w-4"></i>Volver
    </button>
    <div class="flex items-center gap-2">
      <button type="button" class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
        <i data-lucide="pencil" class="h-4 w-4"></i>Editar Cliente
      </button>
      <div class="relative">
        <button id="clientActionsBtn" type="button" aria-haspopup="menu" aria-expanded="false" aria-controls="clientActionsMenu" class="inline-flex h-10 items-center gap-2 rounded-lg bg-brand-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
          Acciones<i data-lucide="chevron-down" class="h-4 w-4"></i>
        </button>
        <div id="clientActionsMenu" role="menu" class="absolute right-0 z-30 mt-2 hidden w-60 rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-slate-700 dark:bg-slate-900">
          <button type="button" role="menuitem" data-client-action="whatsapp" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"><i data-lucide="message-circle" class="h-4 w-4 text-emerald-500"></i>Enviar WhatsApp</button>
          <button type="button" role="menuitem" data-client-action="statement" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"><i data-lucide="file-text" class="h-4 w-4 text-brand-500"></i>Generar Estado de Cuenta</button>
          <button type="button" role="menuitem" data-client-action="email" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"><i data-lucide="mail" class="h-4 w-4 text-slate-400"></i>Enviar Correo</button>
          <div class="my-1 h-px bg-gray-100 dark:bg-slate-800" role="separator"></div>
          <button type="button" role="menuitem" data-client-action="separation" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-gray-100 dark:text-slate-200 dark:hover:bg-slate-800"><i data-lucide="file-signature" class="h-4 w-4 text-orange-500"></i>Registrar Separación</button>
        </div>
      </div>
    </div>
  </div>

  <article class="overflow-hidden rounded-2xl border border-gray-200/70 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="h-20 bg-gradient-to-r from-brand-700 via-brand-600 to-brand-500" aria-hidden="true"></div>
    <div class="flex flex-col gap-4 px-6 pb-6 sm:flex-row sm:items-end">
      <div class="-mt-10 flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-4 border-white bg-slate-100 text-slate-400 shadow-sm dark:border-slate-900 dark:bg-slate-800 dark:text-slate-500">
        <i data-lucide="user-round" class="h-9 w-9"></i>
      </div>
      <div class="min-w-0 flex-1">
        <h1 id="clientName" tabindex="-1" class="text-balance text-2xl font-bold tracking-tight text-slate-900 focus:outline-none sm:text-3xl dark:text-white"></h1>
        <p class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500 dark:text-slate-400">
          <span class="inline-flex items-center gap-1.5"><i data-lucide="id-card" class="h-4 w-4"></i>DNI <span id="clientDni" class="font-medium tabular-nums text-slate-700 dark:text-slate-200"></span></span>
          <span class="inline-flex items-center gap-1.5"><i data-lucide="calendar" class="h-4 w-4"></i>Cliente desde <span id="clientSince"></span></span>
        </p>
      </div>
      <div id="clientBadges" class="flex flex-wrap gap-2"></div>
    </div>
  </article>

  <div class="mt-6 overflow-x-auto border-b border-gray-200 dark:border-slate-800">
    <div id="clientTabs" role="tablist" aria-label="Información del cliente" class="-mb-px flex min-w-max gap-6">
      <button type="button" role="tab" id="tab-resumen" data-tab="resumen" aria-controls="panel-resumen" class="client-tab inline-flex items-center gap-2 border-b-2 border-transparent py-3 text-sm font-medium text-slate-500 hover:text-slate-900 aria-selected:border-brand-600 aria-selected:text-brand-600 dark:text-slate-400 dark:hover:text-white dark:aria-selected:border-brand-400 dark:aria-selected:text-brand-400"><i data-lucide="layout-grid" class="h-4 w-4"></i>Resumen</button>
      <button type="button" role="tab" id="tab-propiedades" data-tab="propiedades" aria-controls="panel-propiedades" class="client-tab inline-flex items-center gap-2 border-b-2 border-transparent py-3 text-sm font-medium text-slate-500 hover:text-slate-900 aria-selected:border-brand-600 aria-selected:text-brand-600 dark:text-slate-400 dark:hover:text-white dark:aria-selected:border-brand-400 dark:aria-selected:text-brand-400"><i data-lucide="building-2" class="h-4 w-4"></i>Propiedades</button>
      <button type="button" role="tab" id="tab-cronograma" data-tab="cronograma" aria-controls="panel-cronograma" class="client-tab inline-flex items-center gap-2 border-b-2 border-transparent py-3 text-sm font-medium text-slate-500 hover:text-slate-900 aria-selected:border-brand-600 aria-selected:text-brand-600 dark:text-slate-400 dark:hover:text-white dark:aria-selected:border-brand-400 dark:aria-selected:text-brand-400"><i data-lucide="calendar-clock" class="h-4 w-4"></i>Cronograma de Pagos</button>
      <button type="button" role="tab" id="tab-documentos" data-tab="documentos" aria-controls="panel-documentos" class="client-tab inline-flex items-center gap-2 border-b-2 border-transparent py-3 text-sm font-medium text-slate-500 hover:text-slate-900 aria-selected:border-brand-600 aria-selected:text-brand-600 dark:text-slate-400 dark:hover:text-white dark:aria-selected:border-brand-400 dark:aria-selected:text-brand-400"><i data-lucide="folder-open" class="h-4 w-4"></i>Documentos</button>
    </div>
  </div>

  <div id="panel-resumen" role="tabpanel" aria-labelledby="tab-resumen" tabindex="0" class="client-panel mt-6 grid grid-cols-1 gap-6 focus:outline-none lg:grid-cols-2">
    <article class="rounded-2xl border border-gray-200/70 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white"><i data-lucide="user" class="h-5 w-5 text-brand-600 dark:text-brand-400"></i>Datos Personales</h2>
      <dl id="clientPersonal" class="mt-5 divide-y divide-gray-100 dark:divide-slate-800"></dl>
    </article>
    <article class="rounded-2xl border border-gray-200/70 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
      <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white"><i data-lucide="wallet" class="h-5 w-5 text-brand-600 dark:text-brand-400"></i>Resumen Financiero</h2>
      <div id="clientFinance" class="mt-5 flex flex-col gap-4"></div>
    </article>
    <article class="rounded-2xl border border-gray-200/70 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:col-span-2">
      <div class="flex items-center justify-between gap-3">
        <h2 class="flex items-center gap-2 text-base font-semibold text-slate-900 dark:text-white"><i data-lucide="contact-round" class="h-5 w-5 text-brand-600 dark:text-brand-400"></i>Contactos</h2>
        <button id="addClientContactBtn" type="button" class="inline-flex items-center gap-2 rounded-lg border border-brand-200 bg-brand-50 px-3 py-2 text-sm font-medium text-brand-700 hover:bg-brand-100 focus:outline-none focus:ring-4 focus:ring-brand-500/20 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-300 dark:hover:bg-brand-500/15">
          <i data-lucide="plus" class="h-4 w-4"></i>Agregar
        </button>
      </div>
      <div id="clientContacts" class="mt-5 space-y-3"></div>
    </article>
  </div>
  <div id="panel-propiedades" role="tabpanel" aria-labelledby="tab-propiedades" tabindex="0" class="client-panel mt-6 hidden focus:outline-none"></div>
  <div id="panel-cronograma" role="tabpanel" aria-labelledby="tab-cronograma" tabindex="0" class="client-panel mt-6 hidden focus:outline-none"></div>
  <div id="panel-documentos" role="tabpanel" aria-labelledby="tab-documentos" tabindex="0" class="client-panel mt-6 hidden focus:outline-none"></div>
</div>