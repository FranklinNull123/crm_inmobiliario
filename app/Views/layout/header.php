<?php
/** @var PDO $pdo */
/** @var string $csrfToken */
$headerUser = cms_current_user() ?? [];
$headerName = htmlspecialchars((string) ($headerUser['name'] ?? 'Usuario'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$headerEmail = htmlspecialchars((string) ($headerUser['email'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$headerAvatar = trim((string) ($headerUser['avatar'] ?? ''));
$headerInitials = '';
foreach (array_slice(preg_split('/\s+/u', trim((string) ($headerUser['name'] ?? 'U'))) ?: ['U'], 0, 2) as $part) {
  $headerInitials .= mb_strtoupper(mb_substr($part, 0, 1));
}
?>
<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inmobi CRM · Panel Administrativo</title>
  <meta name="description" content="Plataforma CRM para gestión inmobiliaria." />

  <script>
    // Apply the saved theme before paint to avoid a flash of the wrong theme.
    (function() {
      var saved = localStorage.getItem('cms-theme');
      var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (saved === 'dark' || (!saved && prefersDark)) document.documentElement.classList.add('dark');
    })();
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
          },
          colors: {
            brand: {
              50: '#eff6ff',
              100: '#dbeafe',
              400: '#60a5fa',
              500: '#3b82f6',
              600: '#2563eb',
              700: '#1d4ed8'
            },
          },
        },
      },
    };
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <link href="globals.css" rel="stylesheet" />
</head>

<body class="h-full bg-gray-100 font-sans text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">
  <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-slate-900 focus:shadow">Saltar al contenido</a>

  <!-- Mobile overlay -->
  <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

  <!-- Sidebar -->
  <aside id="sidebar"
    class="sidebar fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-slate-900 text-slate-300 shadow-xl lg:translate-x-0 dark:bg-slate-900 dark:border-r dark:border-slate-800"
    aria-label="Menú principal">

    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-800 px-5">
      <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-600 text-white">
        <i data-lucide="building-2" class="h-5 w-5"></i>
      </div>
      <div class="brand-text leading-tight">
        <p class="text-sm font-semibold text-white">Inmobi CRM</p>
        <p class="text-xs text-slate-400">Bienes Raíces</p>
      </div>
      <button id="closeSidebarMobile" type="button" class="ml-auto rounded-md p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white lg:hidden" aria-label="Cerrar menú">
        <i data-lucide="x" class="h-5 w-5"></i>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-scroll flex-1 overflow-y-auto px-3 py-5">
      <p class="sidebar-section-title mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Principal</p>
      <ul class="space-y-1" id="navList">
        <li><a href="?view=dashboard" data-module="Dashboard" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0"></i><span class="nav-label">Dashboard</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Dashboard</span></a></li>
        <li><a href="?view=crm" data-module="CRM" data-sub="Leads" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="users" class="h-5 w-5 shrink-0"></i><span class="nav-label">CRM</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">CRM</span></a></li>
        <li><a href="?view=campanas" data-module="Campañas" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="megaphone" class="h-5 w-5 shrink-0"></i><span class="nav-label">Campañas</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Campañas</span></a></li>
        <li><a href="?view=alertas" data-module="Alertas" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="bell-ring" class="h-5 w-5 shrink-0"></i><span class="nav-label">Alertas</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Alertas</span></a></li>
        <li><a href="?view=cartera" data-module="Cartera" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="briefcase-business" class="h-5 w-5 shrink-0"></i><span class="nav-label">Cartera</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Cartera</span></a></li>
        <li><a href="?view=rendimiento" data-module="Rendimiento" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="trending-up" class="h-5 w-5 shrink-0"></i><span class="nav-label">Rendimiento</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Rendimiento</span></a></li>
        <li><a href="?view=expedientes" data-module="Expedientes" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="file-check-2" class="h-5 w-5 shrink-0"></i><span class="nav-label">Expedientes</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Expedientes</span></a></li>
        <li><a href="?view=recordatorios" data-module="Recordatorios" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="calendar-clock" class="h-5 w-5 shrink-0"></i><span class="nav-label">Recordatorios</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Recordatorios</span></a></li>
        <li><a href="?view=notificaciones" data-module="Notificaciones" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="bell-dot" class="h-5 w-5 shrink-0"></i><span class="nav-label">Notificaciones</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Notificaciones</span></a></li>
        <li><a href="?view=seguimiento" data-module="Seguimiento" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="activity" class="h-5 w-5 shrink-0"></i><span class="nav-label">Seguimiento</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Seguimiento</span></a></li>
        <li><a href="?view=tareas" data-module="Tareas" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="check-square" class="h-5 w-5 shrink-0"></i><span class="nav-label">Tareas</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Tareas</span></a></li>
        <li><a href="?view=agenda" data-module="Agenda" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="calendar-days" class="h-5 w-5 shrink-0"></i><span class="nav-label">Agenda</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Agenda</span></a></li>
        <li><a href="?view=pipeline" data-module="Pipeline" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="route" class="h-5 w-5 shrink-0"></i><span class="nav-label">Pipeline</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Pipeline</span></a></li>
        <li><a href="?view=prospeccion" data-module="Prospección" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="target" class="h-5 w-5 shrink-0"></i><span class="nav-label">Prospección</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Prospección</span></a></li>
        <li><a href="?view=incidencias" data-module="Incidencias" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="alert-triangle" class="h-5 w-5 shrink-0"></i><span class="nav-label">Incidencias</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Incidencias</span></a></li>
        <li><a href="?view=proyectos" data-module="Proyectos" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="folder-kanban" class="h-5 w-5 shrink-0"></i><span class="nav-label">Proyectos</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Proyectos</span></a></li>
        <li><a href="?view=inventario" data-module="Inventario" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="home" class="h-5 w-5 shrink-0"></i><span class="nav-label">Inventario</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Inventario</span></a></li>
        <li><a href="?view=clientes" data-module="Clientes" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="contact" class="h-5 w-5 shrink-0"></i><span class="nav-label">Clientes</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Clientes</span></a></li>
      </ul>

      <p class="sidebar-section-title mb-2 mt-6 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Finanzas</p>
      <ul class="space-y-1">
        <li><a href="?view=ventas" data-module="Ventas" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="handshake" class="h-5 w-5 shrink-0"></i><span class="nav-label">Ventas</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Ventas</span></a></li>
        <li><a href="?view=caja" data-module="Caja" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="wallet" class="h-5 w-5 shrink-0"></i><span class="nav-label">Caja</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Caja</span></a></li>
        <li><a href="?view=cobranzas" data-module="Cobranza" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="receipt-text" class="h-5 w-5 shrink-0"></i><span class="nav-label">Cobranza</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Cobranza</span></a></li>
        <li><a href="?view=comisiones" data-module="Comisiones" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="percent" class="h-5 w-5 shrink-0"></i><span class="nav-label">Comisiones</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Comisiones</span></a></li>
        <li><a href="?view=contratos" data-module="Contratos" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="file-signature" class="h-5 w-5 shrink-0"></i><span class="nav-label">Contratos</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Contratos</span></a></li>
        <li><a href="?view=reportes" data-module="Reportes" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="bar-chart-3" class="h-5 w-5 shrink-0"></i><span class="nav-label">Reportes</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Reportes</span></a></li>
      </ul>

      <p class="sidebar-section-title mb-2 mt-6 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Sistema</p>
      <ul class="space-y-1">
        <?php if (cms_has_permission($pdo, 'configuracion', 'ver')): ?>
          <li><a href="?view=configuracion" data-module="Configuración" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="settings" class="h-5 w-5 shrink-0"></i><span class="nav-label">Configuración</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Configuración</span></a></li>
        <?php endif; ?>
        <?php if (cms_has_permission($pdo, 'auditoria', 'ver')): ?>
          <li><a href="?view=auditoria" data-module="Auditoría" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"><i data-lucide="scroll-text" class="h-5 w-5 shrink-0"></i><span class="nav-label">Auditoría</span><span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Auditoría</span></a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <!-- Sidebar footer -->
    <div class="shrink-0 border-t border-slate-800 p-3">
      <a href="?view=soporte" data-module="Soporte" class="nav-link group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white">
        <i data-lucide="life-buoy" class="h-5 w-5 shrink-0"></i><span class="nav-label">Soporte</span>
        <span class="nav-tooltip absolute left-full ml-3 whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs text-white opacity-0 shadow group-hover:opacity-100">Soporte</span>
      </a>
    </div>
  </aside>

  <!-- Main shell -->
  <div id="mainShell" class="main-shell flex min-h-full flex-col lg:pl-64">

    <!-- Navbar -->
    <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-gray-200 bg-white px-4 sm:px-6 dark:border-slate-800 dark:bg-slate-900">
      <button id="toggleSidebar" type="button" class="rounded-lg p-2 text-slate-500 hover:bg-gray-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Alternar menú lateral" aria-controls="sidebar" aria-expanded="true">
        <i data-lucide="menu" class="h-5 w-5"></i>
      </button>

      <!-- Global search -->
      <div class="flex flex-1 justify-center">
        <form role="search" class="relative w-full max-w-xl" onsubmit="event.preventDefault()">
          <label for="globalSearch" class="sr-only">Buscador global</label>
          <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
          <input id="globalSearch" type="search" placeholder="Buscar clientes, propiedades, proyectos…"
            class="h-10 w-full rounded-xl border border-gray-200 bg-gray-50 pl-10 pr-16 text-sm text-slate-800 placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/15 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:focus:bg-slate-800" />
          <kbd class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-md border border-gray-200 bg-white px-1.5 py-0.5 text-[10px] font-medium text-slate-500 sm:block dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300">Ctrl K</kbd>
        </form>
      </div>

      <div class="flex items-center gap-1 sm:gap-2">
        <!-- Theme switch -->
        <button id="themeSwitch" type="button" role="switch" aria-checked="false" aria-label="Modo oscuro"
          class="relative hidden h-7 w-14 shrink-0 items-center rounded-full bg-gray-200 p-0.5 transition-colors sm:inline-flex dark:bg-brand-600">
          <span class="theme-knob flex h-6 w-6 items-center justify-center rounded-full bg-white text-amber-500 shadow transition-transform dark:translate-x-7 dark:text-brand-600">
            <i data-lucide="sun" class="h-3.5 w-3.5 dark:hidden"></i>
            <i data-lucide="moon" class="hidden h-3.5 w-3.5 dark:block"></i>
          </span>
        </button>

        <!-- Notifications -->
        <div class="relative">
          <button id="notifBtn" type="button" class="relative rounded-lg p-2 text-slate-500 hover:bg-gray-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Notificaciones" aria-haspopup="true" aria-expanded="false">
            <i data-lucide="bell" class="h-5 w-5"></i>
          </button>
          <div id="notifMenu" class="absolute right-0 mt-2 hidden w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu">
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-slate-700">
              <p class="text-sm font-semibold text-slate-900 dark:text-white">Notificaciones</p>
              <button type="button" class="text-xs font-medium text-brand-600 hover:underline dark:text-brand-400">Marcar como leídas</button>
            </div>
            <div id="notifItems" class="max-h-96 overflow-auto"></div>
          </div>
        </div>

        <!-- Profile -->
        <div class="relative">
          <button id="profileBtn" type="button" class="flex items-center gap-2 rounded-lg p-1 pr-2 hover:bg-gray-100 dark:hover:bg-slate-800" aria-haspopup="true" aria-expanded="false">
            <?php if ($headerAvatar !== ''): ?>
              <img src="<?= htmlspecialchars($headerAvatar, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" alt="Perfil" class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-200 dark:ring-slate-700" />
            <?php else: ?>
              <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-semibold text-white"><?= htmlspecialchars($headerInitials, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></span>
            <?php endif; ?>
            <span class="hidden text-left leading-tight md:block">
              <span class="block text-sm font-semibold text-slate-900 dark:text-white"><?= $headerName ?></span>
              <span class="block text-xs text-slate-500 dark:text-slate-400"><?= $headerEmail ?></span>
            </span>
            <i data-lucide="chevron-down" class="hidden h-4 w-4 text-slate-400 md:block"></i>
          </button>
          <div id="profileMenu" class="absolute right-0 mt-2 hidden w-60 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu">
            <div class="border-b border-gray-100 px-4 py-3 dark:border-slate-700">
              <p class="text-sm font-semibold text-slate-900 dark:text-white"><?= $headerName ?></p>
              <p class="truncate text-xs text-slate-500 dark:text-slate-400"><?= $headerEmail ?></p>
            </div>
            <button type="button" id="profileOpenModal" role="menuitem" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-slate-700 hover:bg-gray-50 dark:text-slate-200 dark:hover:bg-slate-700/50"><i data-lucide="user" class="h-4 w-4"></i>Mi perfil</button>
            <a href="?view=configuracion" role="menuitem" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-50 dark:text-slate-200 dark:hover:bg-slate-700/50"><i data-lucide="settings" class="h-4 w-4"></i>Preferencias</a>
            <button type="button" id="themeSwitchMenu" role="menuitem" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-50 sm:hidden dark:text-slate-200 dark:hover:bg-slate-700/50">
              <i data-lucide="moon" class="h-4 w-4 dark:hidden"></i><i data-lucide="sun" class="hidden h-4 w-4 dark:block"></i>
              <span class="dark:hidden">Modo oscuro</span><span class="hidden dark:inline">Modo claro</span>
            </button>
            <div class="my-1 border-t border-gray-100 dark:border-slate-700"></div>
            <form method="post" action="?action=logout">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
              <button type="submit" role="menuitem" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"><i data-lucide="log-out" class="h-4 w-4"></i>Cerrar sesión</button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main id="contenido" class="flex-1 p-4 sm:p-6 lg:p-8">
      <nav aria-label="Migas de pan" class="mb-4">
        <ol id="breadcrumbs" class="flex flex-wrap items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400"></ol>
      </nav>

      <div id="pageHeader" class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 id="pageTitle" class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Leads</h1>
          <p id="pageSubtitle" class="mt-1 text-sm text-slate-500 dark:text-slate-400">Gestiona la información de este módulo.</p>
        </div>
        <button id="newRecordBtn" type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-500/30">
          <i data-lucide="plus" class="h-4 w-4"></i>Nuevo registro
        </button>
      </div>

      <!-- Dynamic module container -->
      <section id="moduleContainer" aria-live="polite">