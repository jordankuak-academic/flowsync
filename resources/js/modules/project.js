// resources/js/modules/project.js
export default class ProjectPage {
    mount() {
        this.loadProjects();
        this.bindEvents();
    }

    async loadProjects() {
        const response = await fetch('/api/projects');
        const data = await response.json();

        this.renderProjects(data.projects);
    }

    bindEvents() {
        document.body.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-action]');
            if (!button) return;

            if (button.dataset.action === 'view-project') {
                await this.loadProject(button.dataset.projectId);
            }

            if (button.dataset.action === 'create-task') {
                await this.createTask();
            }
        });
    }

    renderProjects(projects) {
        // 用 JS 把 project list render 进 Blade 的 container
    }

    async loadProject(projectId) {
        const response = await fetch(`/api/projects/${projectId}`);
        const data = await response.json();

        // render selected project + tasks
    }
}