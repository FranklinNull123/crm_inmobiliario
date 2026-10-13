const initializeAgenda = () => {
  const agendaView = document.getElementById('agendaView');
  if (!agendaView) {
    return;
  }

  const agendaCards = agendaView.querySelectorAll('article');
  agendaCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });

  const calendarElement = document.getElementById('agendaCalendar');
  if (calendarElement && typeof FullCalendar !== 'undefined') {
    const priorityColors = {
      alta: '#e11d48',
      media: '#d97706',
      bajo: '#0284c7',
      baja: '#0284c7'
    };
    const events = (Array.isArray(window.agenda) ? window.agenda : []).map((item) => {
      const priority = String(item.prioridad || 'media').toLowerCase();
      const normalizedPriority = priority === 'alto' ? 'alta' : (priority === 'medio' ? 'media' : priority);
      return {
      title: item.titulo || 'Evento',
      start: item.fecha || undefined,
      backgroundColor: priorityColors[normalizedPriority] || priorityColors.media,
      borderColor: priorityColors[normalizedPriority] || priorityColors.media,
      extendedProps: {
        tipo: item.tipo || 'Agenda',
        detalle: item.detalle || '',
        estado: item.estado || 'Pendiente'
      }
      };
    }).filter((event) => event.start);

    const calendar = new FullCalendar.Calendar(calendarElement, {
      locale: 'es',
      initialView: 'dayGridMonth',
      height: 'auto',
      firstDay: 1,
      dayMaxEvents: 3,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        list: 'Agenda'
      },
      events,
      eventClick: (info) => {
        const { tipo, detalle, estado } = info.event.extendedProps;
        const details = [tipo, estado, detalle].filter(Boolean).join(' · ');
        info.el.title = details;
      }
    });

    calendar.render();
  }

  const taskForm = document.getElementById('agendaTaskForm');
  const taskFeedback = document.getElementById('agendaTaskFeedback');
  if (taskForm && typeof agendaCsrfToken !== 'undefined') {
    taskForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const submitButton = taskForm.querySelector('button[type="submit"]');
      submitButton.disabled = true;
      taskFeedback.className = 'rounded-lg bg-slate-100 px-3 py-2 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300';
      taskFeedback.textContent = 'Guardando tarea...';
      taskFeedback.classList.remove('hidden');

      const formData = new FormData(taskForm);
      const payload = {
        action: 'create',
        titulo: String(formData.get('titulo') || '').trim(),
        descripcion: String(formData.get('descripcion') || '').trim(),
        tipo: 'Tarea',
        prioridad: String(formData.get('prioridad') || 'media'),
        fecha_programada: String(formData.get('fecha_programada') || ''),
        _csrf: agendaCsrfToken
      };

      try {
        const response = await fetch('?api=recordatorios', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': agendaCsrfToken
          },
          body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || !result.ok) {
          throw new Error(result.message || 'No se pudo crear la tarea.');
        }
        window.location.reload();
      } catch (error) {
        taskFeedback.className = 'rounded-lg bg-rose-100 px-3 py-2 text-xs text-rose-700 dark:bg-rose-500/10 dark:text-rose-300';
        taskFeedback.textContent = error.message || 'No se pudo crear la tarea.';
        submitButton.disabled = false;
      }
    });
  }
};

window.addEventListener('load', initializeAgenda);
