(function () {
  const view = document.getElementById('expedientesView');
  if (!view) return;

  view.querySelectorAll('table tbody tr').forEach((row) => {
    row.addEventListener('mouseenter', () => row.classList.add('bg-slate-50', 'dark:bg-slate-800/50'));
    row.addEventListener('mouseleave', () => row.classList.remove('bg-slate-50', 'dark:bg-slate-800/50'));
  });
})();
