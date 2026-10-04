if (document.getElementById('contratosView')) {
  const form = document.getElementById('contractForm');
  const message = document.getElementById('contratoMessage');
  const toggleBtn = document.getElementById('newContractBtn');

  const showMessage = (text, isError = false) => {
    if (!message) return;
    message.textContent = text;
    message.className = `rounded-md border px-4 py-3 text-sm ${isError
      ? 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300'
      : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'}`;
    message.classList.remove('hidden');
  };

  toggleBtn?.addEventListener('click', () => {
    form?.classList.toggle('hidden');
  });

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(form));

    try {
      const response = await fetch('?api=contratos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': contratosCsrfToken },
        body: JSON.stringify({ action: 'create', ...formData }),
      });
      const result = await response.json();
      if (!response.ok) throw new Error(result.message || 'No se pudo guardar el contrato.');
      showMessage('Contrato registrado correctamente.');
      form.reset();
      setTimeout(() => window.location.reload(), 600);
    } catch (error) {
      showMessage(error.message, true);
    }
  });
}
