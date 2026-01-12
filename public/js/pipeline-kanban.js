console.log('[pipeline-kanban] loaded');

document.addEventListener('DOMContentLoaded', () => {
  let draggedCard = null;

  document.querySelectorAll('.application-card').forEach(card => {
    card.draggable = true;

    card.addEventListener('dragstart', () => {
      draggedCard = card;
      card.classList.add('dragging');
      console.log('[dragstart] application_id=', card.dataset.applicationId);
    });

    card.addEventListener('dragend', () => {
      card.classList.remove('dragging');
      draggedCard = null;
      console.log('[dragend]');
    });
  });

  document.querySelectorAll('.pipeline-column').forEach(column => {
    column.addEventListener('dragover', (e) => {
      e.preventDefault();
    });

    column.addEventListener('drop', async () => {
      if (!draggedCard) return;

      const stepId = column.dataset.stepId;
      const appId  = draggedCard.dataset.applicationId;

      try {
        const res = await fetch(`/applications/${appId}/step`, {
          method: 'PATCH',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            selection_step_id: stepId
          })
        });

        if (res.status === 401 || res.status === 403) {
          console.warn('[PATCH auth expired] reload');
          location.reload();
          return;
        }

        const data = await res.json();
        console.log('[PATCH success]', data);

      } catch (e) {
        console.error('[PATCH error]', e);
      }
    });
  });
});
