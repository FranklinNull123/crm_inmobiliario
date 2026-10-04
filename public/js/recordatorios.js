(function () {
  const form = document.getElementById('recordatorioForm');
  if (!form) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const payload = {
      action: 'create',
      titulo: form.querySelector('[name="titulo"]').value.trim(),
      descripcion: form.querySelector('[name="descripcion"]').value.trim(),
      tipo: form.querySelector('[name="tipo"]').value,
      prioridad: form.querySelector('[name="prioridad"]').value,
      fecha_programada: form.querySelector('[name="fecha_programada"]').value,
      _csrf: recordatoriosCsrfToken,
    };

    try {
      const response = await fetch('?api=recordatorios', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': recordatoriosCsrfToken,
        },
        body: JSON.stringify(payload),
      });

      const result = await response.json();
      if (!response.ok || !result.ok) {
        throw new Error(result.message || 'No se pudo guardar el recordatorio.');
      }

      form.reset();
      window.location.reload();
    } catch (error) {
      alert(error.message || 'No se pudo guardar el recordatorio.');
    }
  });
})();
