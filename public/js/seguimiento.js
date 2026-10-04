if (document.getElementById('seguimientoView')) {
  const activityCards = document.querySelectorAll('#seguimientoView article');
  activityCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });
}
