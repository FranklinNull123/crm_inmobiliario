if (document.getElementById('agendaView')) {
  const agendaCards = document.querySelectorAll('#agendaView article');
  agendaCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });
}
