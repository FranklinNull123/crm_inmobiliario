if (document.getElementById('cajaView')) {
  const message = document.getElementById('cajaMessage');
  const buttons = document.querySelectorAll('.mark-paid-btn');

  const showMessage = (text, isError = false) => {
    if (!message) return;
    message.textContent = text;
    message.className = `rounded-md border px-4 py-3 text-sm ${isError
      ? 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300'
      : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'}`;
    message.classList.remove('hidden');
  };

  buttons.forEach((button) => {
    button.addEventListener('click', async () => {
      const cuotaId = Number(button.dataset.cuotaId || 0);
      if (!cuotaId) {
        showMessage('No se pudo identificar la cuota.', true);
        return;
      }

      try {
        const response = await fetch('?api=caja', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': cajaCsrfToken,
          },
          body: JSON.stringify({ action: 'mark_paid', cuota_id: cuotaId }),
        });

        const result = await response.json();
        if (!response.ok) {
          throw new Error(result.message || 'No se pudo registrar el cobro.');
        }

        showMessage('Cobro registrado correctamente.');
        setTimeout(() => window.location.reload(), 600);
      } catch (error) {
        showMessage(error.message || 'Error al registrar el cobro.', true);
      }
    });
  });
}
