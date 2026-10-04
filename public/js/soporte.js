if (document.getElementById('soporteView')) {
  const search = document.getElementById('soporteSearch');
  const rows = document.querySelectorAll('.soporte-row');

  if (search) {
    search.addEventListener('input', (event) => {
      const query = event.target.value.trim().toLowerCase();

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }
}
