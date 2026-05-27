    <?php
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
