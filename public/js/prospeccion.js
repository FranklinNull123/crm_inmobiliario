if (document.getElementById('prospeccionView')) {
  const prospeccionCards = document.querySelectorAll('#prospeccionView article');
  prospeccionCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });
}
