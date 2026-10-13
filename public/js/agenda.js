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
};

window.addEventListener('load', initializeAgenda);
