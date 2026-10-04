if (document.getElementById('cobranzasView')) {
  const select = document.getElementById('cobranzaFilter');
  const rows = document.querySelectorAll('.cobranza-row');

  if (select) {
    select.addEventListener('change', (event) => {
      const filter = event.target.value;
      rows.forEach((row) => {
        const matches = filter === 'all' || row.dataset.status === filter;
        row.style.display = matches ? '' : 'none';
      });
    });
  }
}
