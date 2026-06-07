export default class Dashboard {
  static init() {
    const dashboard = new Dashboard();
    dashboard.mount();
  }

  mount() {
    this.bindStatusToggles();
    this.bindTaskActions();
  }

  bindStatusToggles() {
    document.querySelectorAll('.dashboard-task-card .progress-btn').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const newStatus = this.nextStatus(button);
        this.updateStatusButton(button, newStatus);
      });
    });
  }

  bindTaskActions() {
    document.querySelectorAll('.dashboard-task-card .more-actions-btn').forEach((button) => {
      button.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const targetUrl = button.dataset.url;
        if (targetUrl) {
          window.location.href = targetUrl;
        }
      });
    });
  }

  nextStatus(button) {
    const currentStatus = button.classList.contains('done')
      ? 'done'
      : button.classList.contains('in-progress')
      ? 'in-progress'
      : 'none';

    if (currentStatus === 'none') {
      return 'in-progress';
    }
    if (currentStatus === 'in-progress') {
      return 'done';
    }
    return 'none';
  }

  updateStatusButton(button, status) {
    const icon = button.querySelector('i');
    button.classList.remove('in-progress', 'done');

    if (status === 'in-progress') {
      button.classList.add('in-progress');
      if (icon) {
        icon.className = 'bi bi-dash-circle-fill fs-6';
      }
      button.title = 'Status: In Progress';
    } else if (status === 'done') {
      button.classList.add('done');
      if (icon) {
        icon.className = 'bi bi-check-circle-fill fs-6';
      }
      button.title = 'Status: Done';
    } else {
      if (icon) {
        icon.className = 'bi bi-circle fs-6';
      }
      button.title = 'Status: None';
    }

    const group = button.closest('.progress-group');
    if (group) {
      group.dataset.status = status;
    }
  }
}
