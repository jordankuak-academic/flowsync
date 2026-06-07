    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultProjects = [
    'proj_1' => [
        'id'          => 'proj_1',
        'name'        => 'FlowSync Redesign',
        'description' => 'Redesign the FlowSync UI from scratch using full CSS.',
        'status'      => 'inprogress',
        'tasks'       => [
            'task_1' => [
                'id'        => 'task_1',
                'name'      => 'Set up project scaffolding',
                'status'    => 'done',
                'priority'  => 'High',
                'due'       => '2026-05-20',
                'assignees' => ['staff_1'],
                'subtasks'  => [],
            ],
            'task_2' => [
                'id'        => 'task_2',
                'name'      => 'Design component library',
                'status'    => 'in-progress',
                'priority'  => 'High',
                'due'       => '2026-06-01',
                'assignees' => ['staff_2'],
                'subtasks'  => [
                    'sub_1' => [
                        'id'        => 'sub_1',
                        'name'      => 'Define color tokens',
                        'status'    => 'done',
                        'priority'  => 'Normal',
                        'due'       => '2026-05-25',
                        'assignees' => ['staff_2'],
                    ],
                    'sub_2' => [
                        'id'        => 'sub_2',
                        'name'      => 'Build button variants',
                        'status'    => 'none',
                        'priority'  => 'Normal',
                        'due'       => 'Unscheduled',
                        'assignees' => [],
                    ],
                ],
            ],
            'task_3' => [
                'id'        => 'task_3',
                'name'      => 'Implement dashboard page',
                'status'    => 'none',
                'priority'  => 'Normal',
                'due'       => 'Unscheduled',
                'assignees' => [],
                'subtasks'  => [],
            ],
        ],
    ],
    'proj_2' => [
        'id'          => 'proj_2',
        'name'        => 'API Integration',
        'description' => 'Connect frontend to Laravel backend via REST API.',
        'status'      => 'inprogress',
        'tasks'       => [
            'task_4' => [
                'id'        => 'task_4',
                'name'      => 'Define API endpoints',
                'status'    => 'in-progress',
                'priority'  => 'High',
                'due'       => '2026-06-10',
                'assignees' => ['staff_3'],
                'subtasks'  => [],
            ],
        ],
    ],
    'proj_3' => [
        'id'          => 'proj_3',
        'name'        => 'User Auth Module',
        'description' => 'Login, registration, and role-based access control.',
        'status'      => 'done',
        'tasks'       => [
            'task_5' => [
                'id'        => 'task_5',
                'name'      => 'Implement login page',
                'status'    => 'done',
                'priority'  => 'High',
                'due'       => '2026-05-10',
                'assignees' => ['staff_1', 'staff_2'],
                'subtasks'  => [],
            ],
        ],
    ],
];

$defaultStaff = [
    ['id' => 'staff_1', 'name' => 'Alice Tan',   'role' => 'Project Manager'],
    ['id' => 'staff_2', 'name' => 'Bob Lee',      'role' => 'UI/UX Designer'],
    ['id' => 'staff_3', 'name' => 'Carol Ng',     'role' => 'Web Developer'],
    ['id' => 'staff_4', 'name' => 'David Lim',    'role' => 'QA Engineer'],
];

if (!isset($_SESSION['projects'])) {
    $_SESSION['projects'] = $defaultProjects;
}
if (!isset($_SESSION['staff'])) {
    $_SESSION['staff'] = $defaultStaff;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'create';
$type = isset($_GET['type']) ? $_GET['type'] : 'task';
$projectId = isset($_GET['project_id']) ? $_GET['project_id'] : 'proj_1';
$taskId = isset($_GET['task_id']) ? $_GET['task_id'] : '';
$parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : '';

$mode = ($mode === 'edit') ? 'edit' : 'create';
$type = ($type === 'subtask') ? 'subtask' : 'task';

$title = ($mode === 'edit' ? 'Edit ' : 'Create ') . ($type === 'subtask' ? 'Subtask' : 'Task');

$nameVal = '';
$priorityVal = 'Normal';
$dueVal = '';
$dueMode = 'unscheduled';
$assigneeIds = [];

if ($mode === 'edit' && $taskId !== '' && isset($_SESSION['projects'][$projectId])) {
    if ($type === 'subtask' && $parentId !== '') {
        $sub = $_SESSION['projects'][$projectId]['tasks'][$parentId]['subtasks'][$taskId] ?? null;
        if ($sub) {
            $nameVal = $sub['name'] ?? '';
            $priorityVal = $sub['priority'] ?? 'Normal';
            $assigneeIds = $sub['assignees'] ?? [];
            $storedDue = $sub['due'] ?? '';
            if ($storedDue === 'Unscheduled' || $storedDue === '') {
                $dueMode = 'unscheduled';
                $dueVal = '';
            } else {
                $dueMode = 'date';
                $dueVal = $storedDue;
            }
        }
    } else {
        $task = $_SESSION['projects'][$projectId]['tasks'][$taskId] ?? null;
        if ($task) {
            $nameVal = $task['name'] ?? '';
            $priorityVal = $task['priority'] ?? 'Normal';
            $assigneeIds = $task['assignees'] ?? [];
            $storedDue = $task['due'] ?? '';
            if ($storedDue === 'Unscheduled' || $storedDue === '') {
                $dueMode = 'unscheduled';
                $dueVal = '';
            } else {
                $dueMode = 'date';
                $dueVal = $storedDue;
            }
        }
    }
}

$backHref = '?page=project&project_id=' . urlencode($projectId);
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-xl-5">
                <div class="mb-4">
                    <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($title); ?></h2>
                    <p class="text-muted small">Fill in the task details to organize your workflow.</p>
                </div>

                <form method="POST" action="?page=project">
                    <input type="hidden" name="action" value="save_task" />
                    <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>" />
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>" />
                    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>" />
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>" />
                    <input type="hidden" name="parent_id" value="<?php echo htmlspecialchars($parentId); ?>" />

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="task-name">TASK NAME</label>
                        <input type="text" class="form-control form-control-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="task-name" name="name" value="<?php echo htmlspecialchars($nameVal); ?>" placeholder="Enter task name..." required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="task-priority">PRIORITY</label>
                        <select class="form-select form-select-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="task-priority" name="priority">
                            <option value="High" <?php echo $priorityVal === 'High' ? 'selected' : ''; ?>>High</option>
                            <option value="Normal" <?php echo ($priorityVal === 'Normal' || $priorityVal === 'Medium') ? 'selected' : ''; ?>>Normal</option>
                            <option value="Low" <?php echo $priorityVal === 'Low' ? 'selected' : ''; ?>>Low</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="task-assignee">ASSIGNEE</label>
                        <select class="form-select form-select-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="task-assignee" name="assignee">
                            <option value="">Unassigned</option>
                            <?php foreach ($_SESSION['staff'] as $staff): ?>
                                <option value="<?php echo $staff['id']; ?>" <?php echo in_array($staff['id'], $assigneeIds) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($staff['name']); ?> (<?php echo htmlspecialchars($staff['role']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-5">
                            <label class="form-label fw-bold text-dark small" for="task-due-mode">DUE TYPE</label>
                            <select class="form-select form-select-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="task-due-mode" name="due_mode">
                                <option value="unscheduled" <?php echo $dueMode === 'unscheduled' ? 'selected' : ''; ?>>Unscheduled</option>
                                <option value="date" <?php echo $dueMode === 'date' ? 'selected' : ''; ?>>Date</option>
                            </select>
                        </div>
                        <div class="col-7">
                            <label class="form-label fw-bold text-dark small" for="task-due">SELECT DATE</label>
                            <input type="date" class="form-control form-control-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="task-due" name="due" value="<?php echo htmlspecialchars($dueVal); ?>" <?php echo $dueMode === 'unscheduled' ? 'disabled' : ''; ?> />
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?php echo htmlspecialchars($backHref); ?>" class="btn btn-light btn-lg flex-grow-1 fw-bold rounded-3 py-3" style="font-size: 14px;">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold rounded-3 py-3 shadow-sm" style="font-size: 14px;">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modeEl = document.getElementById('task-due-mode');
    const dueEl = document.getElementById('task-due');
    if (modeEl && dueEl) {
        modeEl.addEventListener('change', () => {
            const isUnscheduled = modeEl.value === 'unscheduled';
            dueEl.disabled = isUnscheduled;
            if (isUnscheduled) {
                dueEl.value = '';
            } else {
                if (!dueEl.value) {
                    dueEl.value = new Date().toISOString().split('T')[0];
                }
            }
        });
    }
});
</script>
