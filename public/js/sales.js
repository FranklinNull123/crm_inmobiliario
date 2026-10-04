if (document.getElementById('salesView')) {
  const form = document.getElementById('saleForm');
  const message = document.getElementById('saleMessage');
  const unitSelect = form?.querySelector('[name="unidad_id"]');
  const amountInput = form?.querySelector('[name="monto_total"]');

  const showMessage = (text, isError = false) => {
    if (!message) return;
    message.textContent = text;
    message.className = `rounded-md border px-4 py-3 text-sm ${isError
      ? 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300'
      : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'}`;
    message.classList.remove('hidden');
  };

  if (unitSelect && amountInput) {
    unitSelect.addEventListener('change', () => {
      const selected = [...unitSelect.options].find((option) => option.selected);
      const price = Number(selected?.dataset?.price || 0);
      if (price > 0) {
        amountInput.value = String(price.toFixed(2));
      }
    });
  }

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(form));

    try {
      const response = await fetch('?api=ventas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': saleCsrfToken },
        body: JSON.stringify({ action: 'create', ...formData })
      });
      const result = await response.json();
      if (!response.ok) throw new Error(result.message || 'No se pudo registrar la venta.');
      showMessage('Venta registrada correctamente.');
      form.reset();
      const today = new Date();
      form.querySelector('[name="fecha"]').value = today.toISOString().slice(0, 10);
      form.querySelector('[name="cuotas"]').value = '12';
      setTimeout(() => window.location.reload(), 700);
    } catch (error) {
      showMessage(error.message, true);
    }
  });
}
