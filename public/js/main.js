/*
 * Controles comunes a todas las páginas: barra lateral, navegación,
 * menús, tema y formato compartido. Los módulos se cargan en footer.php
 * después de este archivo y usan los datos que PHP inyecta antes del footer.
 */
    lucide.createIcons();

    const root = document.documentElement;
    const sidebar = document.getElementById('sidebar');
    const mainShell = document.getElementById('mainShell');
    const overlay = document.getElementById('overlay');
    const toggleBtn = document.getElementById('toggleSidebar');
    const desktopQuery = window.matchMedia('(min-width: 1024px)');

    // ---------- Sidebar ----------
    function setCollapsed(collapsed) {
      sidebar.classList.toggle('sidebar-collapsed', collapsed);
      sidebar.classList.toggle('w-64', !collapsed);
      sidebar.classList.toggle('w-20', collapsed);
      mainShell.classList.toggle('lg:pl-64', !collapsed);
      mainShell.classList.toggle('lg:pl-20', collapsed);
      toggleBtn.setAttribute('aria-expanded', String(!collapsed));
      localStorage.setItem('cms-sidebar-collapsed', collapsed ? '1' : '0');
    }
    function openMobile() {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      toggleBtn.setAttribute('aria-expanded', 'true');
    }
    function closeMobile() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      toggleBtn.setAttribute('aria-expanded', 'false');
    }
    toggleBtn.addEventListener('click', () => {
      if (desktopQuery.matches) setCollapsed(!sidebar.classList.contains('sidebar-collapsed'));
      else sidebar.classList.contains('-translate-x-full') ? openMobile() : closeMobile();
    });
    overlay.addEventListener('click', closeMobile);
    document.getElementById('closeSidebarMobile').addEventListener('click', closeMobile);
    desktopQuery.addEventListener('change', (e) => {
      if (e.matches) { overlay.classList.add('hidden'); setCollapsed(localStorage.getItem('cms-sidebar-collapsed') === '1'); }
      else { setCollapsed(false); closeMobile(); }
    });
    if (desktopQuery.matches && localStorage.getItem('cms-sidebar-collapsed') === '1') setCollapsed(true);
    if (!desktopQuery.matches) toggleBtn.setAttribute('aria-expanded', 'false');

    // ---------- Navigation + breadcrumbs ----------
    const activeClasses = ['bg-brand-600', 'text-white', 'shadow-lg', 'shadow-brand-900/30'];
    const idleClasses = ['text-slate-400', 'hover:bg-slate-800', 'hover:text-white'];
    const navLinks = document.querySelectorAll('.nav-link[data-module]');

    function renderBreadcrumbs(items) {
      const ol = document.getElementById('breadcrumbs');
      ol.innerHTML = '';
      items.forEach((label, i) => {
        const li = document.createElement('li');
        li.className = 'flex items-center gap-1.5';
        const isLast = i === items.length - 1;
        if (i > 0) li.innerHTML = '<i data-lucide="chevron-right" class="h-3.5 w-3.5 text-slate-300 dark:text-slate-600" aria-hidden="true"></i>';
        const el = document.createElement(isLast ? 'span' : 'a');
        el.textContent = label;
        if (isLast) { el.className = 'font-medium text-slate-900 dark:text-white'; el.setAttribute('aria-current', 'page'); }
        else { el.href = '#'; el.className = 'hover:text-brand-600 dark:hover:text-brand-400 ' + (i === 0 ? 'inline-flex items-center gap-1' : ''); }
        if (i === 0) el.insertAdjacentHTML('afterbegin', '<i data-lucide="home" class="h-3.5 w-3.5" aria-hidden="true"></i>');
        li.appendChild(el);
        ol.appendChild(li);
      });
      lucide.createIcons();
    }

    function activate(link) {
      navLinks.forEach((l) => {
        l.classList.remove(...activeClasses);
        l.classList.add(...idleClasses);
        l.removeAttribute('aria-current');
      });
      link.classList.remove(...idleClasses);
      link.classList.add(...activeClasses);
      link.setAttribute('aria-current', 'page');
      const module = link.dataset.module;
      const sub = link.dataset.sub;
      renderBreadcrumbs(sub ? ['Inicio', module, sub] : ['Inicio', module]);
      const isDashboard = module === 'Dashboard';
      document.getElementById('pageTitle').textContent = isDashboard ? 'Dashboard Gerencial' : (sub || module);
      document.getElementById('pageSubtitle').textContent = isDashboard ? 'Resumen comercial y financiero de tus proyectos.' : 'Gestiona la información de este módulo.';
      document.getElementById('newRecordBtn').classList.toggle('hidden', !['CRM'].includes(module));
    }

    // ---------- Utilidades compartidas entre módulos ----------
    const unitStatus = {
      Disponible: { cls: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-400 dark:ring-emerald-500/30', dot: 'bg-emerald-500' },
      Reservado: { cls: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/15 dark:text-yellow-300 dark:ring-yellow-500/30', dot: 'bg-yellow-400' },
      Separado: { cls: 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/15 dark:text-orange-400 dark:ring-orange-500/30', dot: 'bg-orange-500' },
      Vendido: { cls: 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-400 dark:ring-rose-500/30', dot: 'bg-rose-500' },
      Bloqueado: { cls: 'bg-gray-100 text-slate-600 ring-slate-500/20 dark:bg-slate-700/50 dark:text-slate-300 dark:ring-slate-500/30', dot: 'bg-slate-400' },
    };
    const money = new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN', maximumFractionDigits: 0 });
    function actionBtn(icon, label, extra) {
      return `<button type="button" title="${label}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-500/40 dark:hover:bg-slate-800 ${extra}"><i data-lucide="${icon}" class="h-4 w-4"></i><span class="sr-only">${label}</span></button>`;
    }

    // ---------- Ruta activa (navegación real por GET) ----------
    // El sidebar ya usa href="?view=...": el navegador navega solo y PHP sirve
    // la vista correspondiente. Aquí solo se resalta la opción activa, se
    // pintan migas y títulos según el parámetro ?view= de la URL.
    const currentView = (new URLSearchParams(window.location.search).get('view') || 'dashboard').toLowerCase();
    // "detalle" es una subpágina de Clientes: resalta el ítem padre del menú.
    const navView = currentView === 'detalle' ? 'clientes' : currentView;
    const activeLink = document.querySelector(`.nav-link[href="?view=${navView}"]`) || document.querySelector('.nav-link[href="?view=dashboard"]');
    if (activeLink) activate(activeLink);

    // ---------- Dropdowns ----------
    function bindDropdown(btnId, menuId) {
      const btn = document.getElementById(btnId);
      const menu = document.getElementById(menuId);
      if (!btn || !menu) return null; // la página puede no incluir este menú (p. ej. la ficha de cliente)
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = menu.classList.contains('hidden');
        closeAllMenus();
        if (open) { menu.classList.remove('hidden'); btn.setAttribute('aria-expanded', 'true'); }
      });
      menu.addEventListener('click', (e) => e.stopPropagation());
      return { btn, menu };
    }
    const menus = [bindDropdown('notifBtn', 'notifMenu'), bindDropdown('profileBtn', 'profileMenu'), bindDropdown('clientActionsBtn', 'clientActionsMenu')].filter(Boolean);
    function closeAllMenus() {
      menus.forEach(({ btn, menu }) => { menu.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); });
    }
    document.addEventListener('click', closeAllMenus);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { closeAllMenus(); if (!desktopQuery.matches) closeMobile(); }
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); document.getElementById('globalSearch').focus(); }
    });

    // ---------- Theme ----------
    const themeSwitch = document.getElementById('themeSwitch');
    function syncSwitch() { themeSwitch.setAttribute('aria-checked', String(root.classList.contains('dark'))); }
    function toggleTheme() {
      const dark = root.classList.toggle('dark');
      localStorage.setItem('cms-theme', dark ? 'dark' : 'light');
      syncSwitch();
    }
    themeSwitch.addEventListener('click', toggleTheme);
    document.getElementById('themeSwitchMenu').addEventListener('click', toggleTheme);
    syncSwitch();
