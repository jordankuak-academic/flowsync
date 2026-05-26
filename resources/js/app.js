/**
 * FlowSync — Client-Side Interactivity
 *
 * Sections:
 *  0. Project Tooltips
 *  1. Inline Project Create Toggle
 *  2. Inline Project Edit (cancel button)
 *  3. Inline Task Name Editing (click name)
 *  4. Inline Assignee Editing (click avatar cell)
 *  5. Inline Due Date Editing (click due cell)
 *  6. Inline Priority Editing (click priority cell)
 *  7. Inline Add Task
 *  8. Inline Add Subtask
 *  9. More Actions Dropdown (Delete only — all fields editable inline)
 * 10. Progress Status Cycle (None → In Progress → Done)
 * 11. Chevron Rotation for Subtask Collapse
 */

document.addEventListener('DOMContentLoaded', () => {

    // ============================================================
    // 0. Project Tooltips
    // Initialise all [data-tooltip="true"] elements.
    // Status tooltips on progress buttons show "Status: None/In Progress/Done".
    // ============================================================
    const tooltipEl = document.createElement('div');
    tooltipEl.className = 'tooltip';
    tooltipEl.style.display = 'none';
    document.body.appendChild(tooltipEl);

    function showTooltip(el) {
        const text = el.getAttribute('title') || el.getAttribute('data-tooltip-title');
        if (!text) return;
        el.setAttribute('data-tooltip-title', text);
        el.removeAttribute('title');
        tooltipEl.textContent = text;
        tooltipEl.style.display = 'block';
        const rect = el.getBoundingClientRect();
        const tipRect = tooltipEl.getBoundingClientRect();
        const placement = el.getAttribute('data-tooltip-placement') || 'top';
        const top = placement === 'right'
            ? rect.top + rect.height / 2 - tipRect.height / 2
            : rect.top - tipRect.height - 8;
        const left = placement === 'right'
            ? rect.right + 8
            : rect.left + rect.width / 2 - tipRect.width / 2;
        tooltipEl.style.top = Math.max(8, top) + 'px';
        tooltipEl.style.left = Math.max(8, left) + 'px';
    }

    function hideTooltip() {
        tooltipEl.style.display = 'none';
    }

    document.querySelectorAll('[data-tooltip="true"]').forEach(el => {
        el.addEventListener('mouseenter', () => showTooltip(el));
        el.addEventListener('mouseleave', hideTooltip);
        el.addEventListener('focus', () => showTooltip(el));
        el.addEventListener('blur', hideTooltip);
    });


    // ============================================================
    // 1. INLINE PROJECT CREATE TOGGLE
    // The + button in each project section header shows/hides
    // the inline create form below it.
    // ============================================================
    document.querySelectorAll('.toggle-inline-create-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const formId = btn.getAttribute('data-target');
            const form   = document.getElementById(formId);
            if (!form) return;

            const isVisible = form.style.display !== 'none';
            // Close all other inline create forms first
            document.querySelectorAll('.inline-project-form').forEach(f => f.style.display = 'none');
            form.style.display = isVisible ? 'none' : 'block';

            if (!isVisible) {
                const input = form.querySelector('input[type="text"]');
                if (input) input.focus();
            }
        });
    });

    document.querySelectorAll('.cancel-inline-create-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById(btn.getAttribute('data-target'));
            if (form) form.style.display = 'none';
        });
    });


    // ============================================================
    // 2. INLINE PROJECT EDIT
    // Triggered from the more-actions dropdown for projects.
    // Swaps the project list item between view and edit form.
    // ============================================================
    function showProjectInlineEdit(projId) {
        const viewEl = document.getElementById('view-' + projId);
        const editEl = document.getElementById('edit-' + projId);
        if (viewEl) viewEl.style.display = 'none';
        if (editEl) {
            editEl.style.display = 'block';
            const input = editEl.querySelector('input[type="text"]');
            if (input) input.focus();
        }
    }

    document.querySelectorAll('.cancel-inline-edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const projId = btn.getAttribute('data-proj-id');
            const viewEl = document.getElementById('view-' + projId);
            const editEl = document.getElementById('edit-' + projId);
            if (editEl) editEl.style.display = 'none';
            if (viewEl) viewEl.style.display  = '';
        });
    });


    // ============================================================
    // SHARED HELPER: submit the hidden save-task-field form.
    // Used by all inline field editors (name, assignee, due, priority).
    // ============================================================
    function submitTaskField({ id, parentId, type, projId, name, priority, due, assignee }) {
        const form = document.getElementById('save-task-field-form');
        if (!form) return;

        // Update the project_id hidden field to match the current project
        const projInput = form.querySelector('[name="project_id"]');
        if (projInput && projId) projInput.value = projId;

        document.getElementById('stf-id').value       = id       || '';
        document.getElementById('stf-parent').value   = parentId || '';
        document.getElementById('stf-type').value     = type     || 'task';
        document.getElementById('stf-name').value     = name     || '';
        document.getElementById('stf-priority').value = priority || '';
        document.getElementById('stf-due').value      = due      || 'Unscheduled';
        document.getElementById('stf-assignee').value = assignee || '';
        form.submit();
    }

    // Helper: read current field values from a task row by id
    function getRowData(rowEl) {
        return {
            id:       rowEl.getAttribute('data-id'),
            type:     rowEl.getAttribute('data-type'),
            projId:   rowEl.getAttribute('data-proj-id'),
            parentId: rowEl.getAttribute('data-parent-id') || '',
        };
    }


    // ============================================================
    // 3. INLINE TASK NAME EDITING
    // Click the task name text to replace it with an input.
    // Saves on blur or Enter; cancels on Escape.
    // ============================================================
    document.body.addEventListener('click', e => {
        const nameDisplay = e.target.closest('.task-name-display');
        if (!nameDisplay) return;

        const cell  = nameDisplay.closest('.task-name-cell');
        const input = cell ? cell.querySelector('.task-name-input') : null;
        if (!input) return;

        nameDisplay.style.display = 'none';
        input.style.display = '';
        input.focus();
        input.select();

        function saveName() {
            const newName = input.value.trim();
            if (!newName || newName === nameDisplay.textContent.trim()) {
                input.style.display  = 'none';
                nameDisplay.style.display = '';
                return;
            }
            const row = input.closest('.task-item-row');
            if (!row) return;
            const d = getRowData(row);
            submitTaskField({ ...d, name: newName, priority: 'Normal', due: 'Unscheduled' });
        }

        input.addEventListener('blur', saveName, { once: true });
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  { e.preventDefault(); saveName(); }
            if (e.key === 'Escape') {
                input.value = nameDisplay.textContent.trim();
                input.style.display  = 'none';
                nameDisplay.style.display = '';
            }
        });
    });


    // ============================================================
    // 4. INLINE ASSIGNEE EDITING
    // Click the assignee avatar cluster to reveal a <select>.
    // Selecting an option immediately saves and reloads.
    // ============================================================
    document.body.addEventListener('click', e => {
        const cell = e.target.closest('.task-assignee-cell');
        if (!cell) return;
        // Ignore clicks on the select itself (let change handler deal with it)
        if (e.target.closest('.assignee-inline-select')) return;

        const display = cell.querySelector('.assignee-display');
        const select  = cell.querySelector('.assignee-inline-select');
        if (!select) return;

        // Hide all other open inline selects
        document.querySelectorAll('.assignee-inline-select').forEach(s => {
            if (s !== select) { s.style.display = 'none'; s.closest('.task-assignee-cell').querySelector('.assignee-display').style.display = ''; }
        });

        display.style.display = 'none';
        select.style.display  = '';
        select.focus();
    });

    document.body.addEventListener('change', e => {
        const select = e.target.closest('.assignee-inline-select');
        if (!select) return;

        const row = select.closest('.task-item-row');
        if (!row) return;
        const d = getRowData(row);

        // Read current name/priority/due from the row so we don't overwrite them
        const nameEl     = row.querySelector('.task-name-display');
        const priorityEl = row.querySelector('.priority-display');
        const dueEl      = row.querySelector('.due-inline-input');

        submitTaskField({
            ...d,
            name:     nameEl     ? nameEl.textContent.trim()    : '',
            priority: priorityEl ? priorityEl.textContent.trim() : 'Normal',
            due:      dueEl      ? (dueEl.value || 'Unscheduled') : 'Unscheduled',
            assignee: select.value,
        });
    });

    // Close assignee selects when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('.task-assignee-cell')) {
            document.querySelectorAll('.assignee-inline-select').forEach(s => {
                s.style.display = 'none';
                const display = s.closest('.task-assignee-cell')?.querySelector('.assignee-display');
                if (display) display.style.display = '';
            });
        }
    });


    // ============================================================
    // 5. INLINE DUE DATE EDITING
    // Click the due date display to reveal a date <input>.
    // Saves on blur or Enter.
    // ============================================================
    document.body.addEventListener('click', e => {
        const cell = e.target.closest('.task-due-cell');
        if (!cell) return;
        if (e.target.closest('.due-inline-input')) return;

        const display = cell.querySelector('.due-display');
        const input   = cell.querySelector('.due-inline-input');
        if (!input) return;

        display.style.display = 'none';
        input.style.display   = '';
        input.focus();
    });

    document.body.addEventListener('blur', e => {
        const input = e.target.closest('.due-inline-input');
        if (!input) return;

        const row = input.closest('.task-item-row');
        if (!row) return;
        const d = getRowData(row);

        const nameEl     = row.querySelector('.task-name-display');
        const priorityEl = row.querySelector('.priority-display');
        const assigneeEl = row.querySelector('.assignee-inline-select');

        submitTaskField({
            ...d,
            name:     nameEl     ? nameEl.textContent.trim()     : '',
            priority: priorityEl ? priorityEl.textContent.trim() : 'Normal',
            due:      input.value || 'Unscheduled',
            assignee: assigneeEl ? assigneeEl.value : '',
        });
    }, true); // use capture so blur fires on non-focusable elements

    document.body.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        const input = e.target.closest('.due-inline-input');
        if (!input) return;
        e.preventDefault();
        input.blur(); // triggers the blur save above
    });


    // ============================================================
    // 6. INLINE PRIORITY EDITING
    // Click the priority badge to reveal a <select>.
    // Selecting immediately saves and reloads.
    // ============================================================
    document.body.addEventListener('click', e => {
        const cell = e.target.closest('.task-priority-cell');
        if (!cell) return;
        if (e.target.closest('.priority-inline-select')) return;

        const display = cell.querySelector('.priority-display');
        const select  = cell.querySelector('.priority-inline-select');
        if (!select) return;

        document.querySelectorAll('.priority-inline-select').forEach(s => {
            if (s !== select) { s.style.display = 'none'; s.closest('.task-priority-cell').querySelector('.priority-display').style.display = ''; }
        });

        display.style.display = 'none';
        select.style.display  = '';
        select.focus();
    });

    document.body.addEventListener('change', e => {
        const select = e.target.closest('.priority-inline-select');
        if (!select) return;

        const row = select.closest('.task-item-row');
        if (!row) return;
        const d = getRowData(row);

        const nameEl     = row.querySelector('.task-name-display');
        const dueEl      = row.querySelector('.due-inline-input');
        const assigneeEl = row.querySelector('.assignee-inline-select');

        submitTaskField({
            ...d,
            name:     nameEl     ? nameEl.textContent.trim()       : '',
            priority: select.value,
            due:      dueEl      ? (dueEl.value || 'Unscheduled')  : 'Unscheduled',
            assignee: assigneeEl ? assigneeEl.value                : '',
        });
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.task-priority-cell')) {
            document.querySelectorAll('.priority-inline-select').forEach(s => {
                s.style.display = 'none';
                const display = s.closest('.task-priority-cell')?.querySelector('.priority-display');
                if (display) display.style.display = '';
            });
        }
    });


    // ============================================================
    // 7. INLINE ADD TASK
    // The "Create New Task" dashed button shows the inline form.
    // ============================================================
    const showAddTaskBtn   = document.getElementById('show-add-task-btn');
    const inlineAddTaskRow = document.getElementById('inline-add-task-row');
    const cancelAddTask    = document.getElementById('cancel-add-task');

    if (showAddTaskBtn && inlineAddTaskRow) {
        showAddTaskBtn.addEventListener('click', () => {
            inlineAddTaskRow.style.display = 'block';
            showAddTaskBtn.style.display   = 'none';
            const input = inlineAddTaskRow.querySelector('input[name="name"]');
            if (input) input.focus();
        });
    }
    if (cancelAddTask) {
        cancelAddTask.addEventListener('click', () => {
            inlineAddTaskRow.style.display = 'none';
            if (showAddTaskBtn) showAddTaskBtn.style.display = '';
        });
    }


    // ============================================================
    // 8. INLINE ADD SUBTASK
    // Each task with subtasks has an "Add Subtask" link.
    // Clicking it clones the hidden template form below that row.
    // ============================================================
    const subtaskTemplate = document.getElementById('inline-add-subtask-template');

    document.body.addEventListener('click', e => {
        const addBtn = e.target.closest('.add-subtask-inline-btn');
        if (!addBtn) return;

        const parentId = addBtn.getAttribute('data-parent-id');

        // Remove any already-open inline subtask forms
        document.querySelectorAll('.active-subtask-inline-form').forEach(f => f.remove());

        if (!subtaskTemplate) return;
        const clone = subtaskTemplate.cloneNode(true);
        clone.id = 'active-subtask-inline-form-' + parentId;
        clone.classList.add('active-subtask-inline-form');
        clone.style.display = 'block';

        // Set the correct parent_id
        const parentInput = clone.querySelector('input[name="parent_id"]');
        if (parentInput) parentInput.value = parentId;

        // Insert after the add-subtask row
        const addRow = addBtn.closest('.inline-add-subtask-row');
        if (addRow) addRow.insertAdjacentElement('afterend', clone);

        const nameInput = clone.querySelector('input[name="name"]');
        if (nameInput) nameInput.focus();

        clone.querySelector('.cancel-subtask-inline').addEventListener('click', () => clone.remove());
    });


    // ============================================================
    // 9. MORE ACTIONS DROPDOWN — Delete only
    //
    // For tasks/subtasks all fields are editable inline, so the
    // dropdown only shows Delete.
    //
    // For projects, Edit opens the inline project form.
    // ============================================================
    let activeDropdown = null;

    // Close dropdown when clicking outside
    document.addEventListener('click', e => {
        if (activeDropdown && !activeDropdown.contains(e.target) && !e.target.closest('.more-actions-btn')) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    });

    function openModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.add('show');
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.removeAttribute('aria-hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalEl) {
        if (!modalEl) return;
        modalEl.classList.remove('show');
        modalEl.removeAttribute('aria-modal');
        modalEl.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        modalEl.dispatchEvent(new CustomEvent('modal:hidden'));
    }

    document.body.addEventListener('click', e => {
        const closeBtn = e.target.closest('[data-modal-dismiss="true"]');
        if (closeBtn) closeModal(closeBtn.closest('.modal'));
        if (e.target.classList.contains('modal')) closeModal(e.target);
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal(document.querySelector('.modal.show'));
    });

    // Show delete confirmation modal, then call onConfirm
    function confirmAndDelete(onConfirm) {
        const modalEl    = document.getElementById('deleteConfirmModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const header     = modalEl.querySelector('.modal-header');
        if (header) header.classList.add('modal-header-danger');

        function cleanup() {
            confirmBtn.removeEventListener('click', handleConfirm);
            if (header) header.classList.remove('modal-header-danger');
        }

        function handleConfirm() {
            onConfirm();
            closeModal(modalEl);
            cleanup();
        }

        confirmBtn.addEventListener('click', handleConfirm);
        modalEl.addEventListener('modal:hidden', cleanup, { once: true });
        openModal(modalEl);
    }

    // POST a delete_item action via fetch
    function deleteItem(type, id, projId, parentId) {
        const formData = new FormData();
        formData.append('action',     'delete_item');
        formData.append('type',       type);
        formData.append('id',         id);
        formData.append('project_id', projId);
        formData.append('parent_id',  parentId || '');
        fetch('index.php', { method: 'POST', body: formData })
            .then(res => { if (res.redirected) window.location.href = res.url; });
    }

    document.body.addEventListener('click', e => {
        const actionBtn = e.target.closest('.more-actions-btn');
        if (!actionBtn) return;
        e.stopPropagation();
        e.preventDefault();

        // Close any existing dropdown
        if (activeDropdown) { activeDropdown.remove(); activeDropdown = null; }

        const id   = actionBtn.getAttribute('data-id');
        const type = actionBtn.getAttribute('data-type');

        const dropdown = document.createElement('div');
        dropdown.className = 'actions-dropdown';

        // Projects get an "Edit" option that opens the inline form.
        // Tasks/subtasks do NOT get Edit — they use inline field editing.
        if (type === 'project') {
            dropdown.innerHTML = `
                <button class="actions-dropdown-item edit-trigger" type="button">
                    <i class="bi bi-pencil me-2" style="font-size:11px;"></i>Edit Project
                </button>
                <button class="actions-dropdown-item delete-trigger" type="button">
                    <i class="bi bi-trash me-2" style="font-size:11px;"></i>Delete
                </button>`;
        } else {
            dropdown.innerHTML = `
                <button class="actions-dropdown-item delete-trigger" type="button">
                    <i class="bi bi-trash me-2" style="font-size:11px;"></i>Delete
                </button>`;
        }

        document.body.appendChild(dropdown);
        activeDropdown = dropdown;

        // Position below the button
        const rect    = actionBtn.getBoundingClientRect();
        const scrollY = window.scrollY || document.documentElement.scrollTop;
        const scrollX = window.scrollX || document.documentElement.scrollLeft;
        dropdown.style.top  = `${rect.bottom + scrollY + 4}px`;
        dropdown.style.left = `${rect.right  + scrollX - dropdown.offsetWidth}px`;

        // Edit trigger (projects only)
        const editTrigger = dropdown.querySelector('.edit-trigger');
        if (editTrigger) {
            editTrigger.onclick = () => {
                dropdown.remove(); activeDropdown = null;
                showProjectInlineEdit(id);
            };
        }

        // Delete trigger
        dropdown.querySelector('.delete-trigger').onclick = () => {
            dropdown.remove(); activeDropdown = null;
            confirmAndDelete(() => {
                const projId = new URLSearchParams(window.location.search).get('project_id') || 'proj_1';
                const row    = document.getElementById('row_' + id);
                const parentId = row ? (row.getAttribute('data-parent-id') || '') : '';
                deleteItem(type, id, projId, parentId);
            });
        };
    });


    // ============================================================
    // 10. PROGRESS STATUS CYCLE
    // Clicking the circle button cycles the task status:
    // None → In Progress → Done → None
    // Tooltip updates to always show current state.
    // ============================================================
    document.body.addEventListener('click', e => {
        const progressBtn = e.target.closest('.progress-btn');
        if (!progressBtn) return;

        let newStatus, newTitle, newIcon;
        if (progressBtn.classList.contains('done')) {
            progressBtn.classList.remove('done');
            newStatus = 'none';
            newTitle  = 'None';
            newIcon   = 'bi-circle';
        } else if (progressBtn.classList.contains('in-progress')) {
            progressBtn.classList.replace('in-progress', 'done');
            newStatus = 'done';
            newTitle  = 'Done';
            newIcon   = 'bi-check-circle-fill text-success';
        } else {
            progressBtn.classList.add('in-progress');
            newStatus = 'in-progress';
            newTitle  = 'In Progress';
            newIcon   = 'bi-dash-circle-fill text-primary';
        }

        // Update status data attribute on the parent group
        const group = progressBtn.closest('[data-status]');
        if (group) group.setAttribute('data-status', newStatus);

        // Swap the icon
        const icon = progressBtn.querySelector('i');
        if (icon) icon.className = `bi ${newIcon}`;

        const label = 'Status: ' + newTitle;
        progressBtn.setAttribute('data-tooltip-title', label);
        progressBtn.setAttribute('title', label);
    });


    // ============================================================
    // 11. CHEVRON ROTATION FOR SUBTASK COLLAPSE
    // Rotate the expand chevron when project collapse opens/closes.
    // ============================================================
    document.body.addEventListener('click', e => {
        const btn = e.target.closest('.expand-btn');
        if (!btn) return;
        const targetSelector = btn.getAttribute('data-target');
        const target = targetSelector ? document.querySelector(targetSelector) : null;
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        if (target) target.classList.toggle('show', !expanded);
    });

    document.body.addEventListener('click', e => {
        const toggle = e.target.closest('[data-dropdown-toggle="true"]');
        document.querySelectorAll('.dropdown.open').forEach(dropdown => {
            if (!toggle || dropdown !== toggle.closest('.dropdown')) dropdown.classList.remove('open');
        });
        if (toggle) {
            e.preventDefault();
            toggle.closest('.dropdown')?.classList.toggle('open');
        }
    });

});
