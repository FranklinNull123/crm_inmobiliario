if (document.getElementById('pipelineView')) {
  const pipelineCards = document.querySelectorAll('#pipelineView article');
  pipelineCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 60}ms`;
    card.classList.add('transition', 'duration-200', 'hover:shadow-md');
  });
}
