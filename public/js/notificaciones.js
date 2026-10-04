if (document.getElementById('notificacionesView')) {
  const rows = document.querySelectorAll('#notificacionesView .rounded-full');
  rows.forEach((pill) => {
    pill.classList.add('shadow-sm');
  });
}
