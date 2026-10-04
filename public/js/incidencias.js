if (document.getElementById('incidenciasView')) {
  const cards = document.querySelectorAll('#incidenciasView .rounded-2xl');
  cards.forEach((card) => {
    card.classList.add('shadow-sm');
  });
}
