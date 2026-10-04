if (document.getElementById('tareasView')) {
  const taskCards = document.querySelectorAll('#tareasView article');
  taskCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });
}
