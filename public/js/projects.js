(() => {
  const root = document.getElementById('projectsView');
  if (!root) return;

  const modal = root.querySelector('#projectModal');
  const projectForm = root.querySelector('#projectForm');
  const stageForm = root.querySelector('#stageForm');
  const message = root.querySelector('#projectMessage');

  const showMessage = (text, isError = false) => {
    message.textContent = text;
    message.className = `rounded-md border px-4 py-3 text-sm ${isError
      ? 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300'
      : 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-300'}`;
  };

  const post = async (action, data) => {
    const response = await fetch('?api=proyectos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': leadCsrfToken },
      body: JSON.stringify({ action, ...data }),
    });
    const result = await response.json();
    if (!response.ok) throw new Error(result.message || 'No se pudo guardar.');
    return result;
  };

  const openProjectForm = (project = null) => {
    projectForm.reset();
    projectForm.elements.id.value = project?.id ?? '';
    projectForm.elements.name.value = project?.name ?? '';
    projectForm.elements.location.value = project?.location ?? '';
    projectForm.elements.description.value = project?.description ?? '';
    projectForm.elements.start_date.value = project?.startDate ?? '';
    projectForm.elements.delivery_date.value = project?.deliveryDate ?? '';
    projectForm.elements.status.value = project?.status ?? projectStatusOptions[0]?.label ?? '';
    root.querySelector('#projectModalTitle').textContent = project ? 'Editar proyecto' : 'Nuevo proyecto';
    modal.classList.remove('hidden');
    projectForm.elements.name.focus();
  };

  root.querySelector('#newProjectBtn').addEventListener('click', () => openProjectForm());
  root.querySelectorAll('[data-project-close]').forEach((button) => button.addEventListener('click', () => modal.classList.add('hidden')));
  root.querySelectorAll('[data-edit-project]').forEach((button) => button.addEventListener('click', () => {
    try { openProjectForm(JSON.parse(button.dataset.editProject)); }
    catch { showMessage('No se pudieron cargar los datos del proyecto.', true); }
  }));

  projectForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
      await post('project_save', Object.fromEntries(new FormData(projectForm)));
      location.reload();
    } catch (error) { showMessage(error.message, true); }
  });

  const resetStageForm = () => {
    stageForm.reset();
    stageForm.elements.id.value = '';
    stageForm.elements.position.value = '1';
    root.querySelector('#stageSubmit').textContent = 'Añadir etapa';
    root.querySelector('#stageCancel').classList.add('hidden');
  };
  root.querySelector('#stageCancel').addEventListener('click', resetStageForm);
  stageForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    try {
      await post('stage_save', Object.fromEntries(new FormData(stageForm)));
      location.reload();
    } catch (error) { showMessage(error.message, true); }
  });

  root.querySelectorAll('[data-edit-stage]').forEach((button) => button.addEventListener('click', () => {
    const row = button.closest('[data-stage-row]');
    stageForm.elements.id.value = row.dataset.stageId;
    stageForm.elements.project_id.value = row.dataset.projectId;
    stageForm.elements.name.value = row.dataset.name;
    stageForm.elements.position.value = row.dataset.position;
    root.querySelector('#stageSubmit').textContent = 'Guardar cambios';
    root.querySelector('#stageCancel').classList.remove('hidden');
    stageForm.elements.name.focus();
  }));

  root.querySelectorAll('[data-toggle-stage]').forEach((button) => button.addEventListener('click', async () => {
    const activate = button.dataset.toggleStage === '1';
    try {
      await post('stage_status', { id: button.closest('[data-stage-row]').dataset.stageId, active: activate });
      location.reload();
    } catch (error) { showMessage(error.message, true); }
  }));

  root.querySelector('#projectSearch').addEventListener('input', (event) => {
    const query = event.currentTarget.value.trim().toLocaleLowerCase();
    let visible = 0;
    root.querySelectorAll('[data-project-row]').forEach((row) => {
      const matches = row.dataset.search.includes(query);
      row.classList.toggle('hidden', !matches);
      if (matches) visible += 1;
    });
    root.querySelector('#projectSearchEmpty').classList.toggle('hidden', visible > 0);
  });

  modal.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') modal.classList.add('hidden');
  });
})();