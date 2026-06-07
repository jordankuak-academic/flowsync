@extends('layouts.hsxxx-layout')

@section('page-title', 'Projects')

@section('content')
<?php

/**
 * FlowSync — Project Page
 *
 * Left sidebar: In-progress and completed project lists with inline create/edit forms.
 * Right panel:  Active project workspace with fully inline-editable task rows.
 *
 * Inline editable fields per task: Name, Assignee, Due Date, Priority, Status (click-to-cycle).
 * The three-dots menu only shows Delete (no separate Edit button needed).
 *
 * Components available via index.php: renderButton(), renderInput(), renderTaskRow()
 */

// -------------------------------------------------------
// Session initialization & CRUD Handling
// -------------------------------------------------------
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'save_project') {
        $mode = $_POST['mode'] ?? 'create';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'inprogress';

        if ($mode === 'create') {
            $projId = 'proj_' . uniqid();
            $_SESSION['projects'][$projId] = [
                'id'          => $projId,
                'name'        => $name,
                'description' => $description,
                'status'      => $status,
                'tasks'       => []
            ];
            header("Location: ?page=project&project_id=" . urlencode($projId));
            exit;
        } elseif ($mode === 'edit') {
            $projId = $_POST['project_id'] ?? '';
            if (isset($_SESSION['projects'][$projId])) {
                $_SESSION['projects'][$projId]['name'] = $name;
                $_SESSION['projects'][$projId]['description'] = $description;
                $_SESSION['projects'][$projId]['status'] = $status;
            }
            header("Location: ?page=project&project_id=" . urlencode($projId));
            exit;
        }
    } elseif ($action === 'delete_project') {
        $projId = $_POST['project_id'] ?? '';
        if (isset($_SESSION['projects'][$projId])) {
            unset($_SESSION['projects'][$projId]);
        }
        header("Location: ?page=project");
        exit;
    } elseif ($action === 'save_task') {
        $mode = $_POST['mode'] ?? 'create';
        $type = $_POST['type'] ?? 'task';
        $projId = $_POST['project_id'] ?? '';
        $taskId = $_POST['task_id'] ?? '';
        $parentId = $_POST['parent_id'] ?? '';

        if (isset($_SESSION['projects'][$projId])) {
            if ($mode === 'create') {
                $name = $_POST['name'] ?? '';
                $priority = $_POST['priority'] ?? 'Normal';
                $assignee = $_POST['assignee'] ?? '';
                $due = $_POST['due'] ?? '';
                if ($due === '') {
                    $due = 'Unscheduled';
                }

                $newTaskId = ($type === 'subtask' ? 'sub_' : 'task_') . uniqid();
                $newTask = [
                    'id'        => $newTaskId,
                    'name'      => $name,
                    'status'    => 'none',
                    'priority'  => $priority,
                    'due'       => $due,
                    'assignees' => $assignee ? [$assignee] : [],
                ];
                if ($type === 'task') {
                    $newTask['subtasks'] = [];
                    $_SESSION['projects'][$projId]['tasks'][$newTaskId] = $newTask;
                } else {
                    if (isset($_SESSION['projects'][$projId]['tasks'][$parentId])) {
                        $_SESSION['projects'][$projId]['tasks'][$parentId]['subtasks'][$newTaskId] = $newTask;
                    }
                }
            } elseif ($mode === 'edit') {
                if ($type === 'subtask' && $parentId !== '') {
                    $taskRef = &$_SESSION['projects'][$projId]['tasks'][$parentId]['subtasks'][$taskId];
                } else {
                    $taskRef = &$_SESSION['projects'][$projId]['tasks'][$taskId];
                }

                if (isset($taskRef)) {
                    if (isset($_POST['name'])) {
                        $taskRef['name'] = $_POST['name'];
                    }
                    if (isset($_POST['priority'])) {
                        $taskRef['priority'] = $_POST['priority'];
                    }
                    if (isset($_POST['assignee'])) {
                        $assignee = $_POST['assignee'];
                        $taskRef['assignees'] = $assignee ? [$assignee] : [];
                    }
                    if (isset($_POST['due_mode'])) {
                        $taskRef['due'] = $_POST['due_mode'] === 'unscheduled' ? 'Unscheduled' : ($_POST['due'] ?: 'Unscheduled');
                    } elseif (isset($_POST['due'])) {
                        $taskRef['due'] = $_POST['due'] ?: 'Unscheduled';
                    }
                    if (isset($_POST['status'])) {
                        $taskRef['status'] = $_POST['status'];
                    }
                }
            }
        }
        header("Location: ?page=project&project_id=" . urlencode($projId));
        exit;
    } elseif ($action === 'delete_task') {
        $projId = $_POST['project_id'] ?? '';
        $taskId = $_POST['task_id'] ?? '';
        $parentId = $_POST['parent_id'] ?? '';

        if (isset($_SESSION['projects'][$projId])) {
            if ($parentId !== '') {
                if (isset($_SESSION['projects'][$projId]['tasks'][$parentId]['subtasks'][$taskId])) {
                    unset($_SESSION['projects'][$projId]['tasks'][$parentId]['subtasks'][$taskId]);
                }
            } else {
                if (isset($_SESSION['projects'][$projId]['tasks'][$taskId])) {
                    unset($_SESSION['projects'][$projId]['tasks'][$taskId]);
                }
            }
        }
        header("Location: ?page=project&project_id=" . urlencode($projId));
        exit;
    }
}

$projects = $_SESSION['projects'];
$staff = $_SESSION['staff'];

// -------------------------------------------------------
// Group projects by status
// -------------------------------------------------------
$inprogressProjects = [];
$doneProjects       = [];
foreach ($projects as $p) {
    if (($p['status'] ?? 'inprogress') === 'done') {
        $doneProjects[] = $p;
    } else {
        $inprogressProjects[] = $p;
    }
}

// -------------------------------------------------------
// Determine the active project
// -------------------------------------------------------
$selectedProjId = $_GET['project_id'] ?? '';
if (empty($selectedProjId) || !array_key_exists($selectedProjId, $projects)) {
    $keys           = array_keys($projects);
    $selectedProjId = !empty($keys) ? $keys[0] : '';
}
$activeProject = $selectedProjId ? $projects[$selectedProjId] : null;

// -------------------------------------------------------
// Staff lookup map for avatar rendering
// -------------------------------------------------------
$staffMap = [];
foreach ($staff as $s) {
    $staffMap[$s['id']] = $s;
}

$avatarColors = ['#2563EB', '#10B981', '#EF4444', '#8B5CF6', '#06B6D4', '#F59E0B', '#EC4899'];

function getAvatarColor($name, $colors)
{
    return $colors[abs(crc32($name)) % count($colors)];
}

function getInitials($name)
{
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
    }
    return strtoupper(substr($name, 0, 2));
}

// -------------------------------------------------------
// Render assignee avatar cluster
// -------------------------------------------------------
function renderAssigneeAvatars($assigneeIds, $staffMap, $avatarColors, $max = 3)
{
    if (empty($assigneeIds)) {
        return '<span class="text-muted" style="font-size:11px; opacity:0.5;" title="No assignee">—</span>';
    }
    $html  = '<div class="d-flex align-items-center" style="gap:-4px;">';
    $count = 0;
    foreach ((array)$assigneeIds as $sid) {
        if ($count >= $max) break;
        if (!isset($staffMap[$sid])) continue;
        $m = $staffMap[$sid];
        $html .= sprintf(
            '<div class="avatar-chip" style="background:%s;" title="%s">%s</div>',
            getAvatarColor($m['name'], $avatarColors),
            htmlspecialchars($m['name'] . ' · ' . $m['role']),
            htmlspecialchars(getInitials($m['name']))
        );
        $count++;
    }
    $extra = count((array)$assigneeIds) - $count;
    if ($extra > 0) {
        $html .= sprintf('<div class="avatar-chip avatar-chip-more">+%d</div>', $extra);
    }
    $html .= '</div>';
    return $html;
}

// -------------------------------------------------------
// Render a single task or subtask row
// All fields are inline-editable: click name, assignee, due, or priority to edit.
// Status cycles on button click (None → In Progress → Done).
// -------------------------------------------------------
function renderTaskRowInline($task, $variant, $projId, $staffMap, $avatarColors, $parentId = '')
{
    $id         = htmlspecialchars($task['id']);
    $name       = htmlspecialchars($task['name']);
    $status     = $task['status']   ?? 'none';
    $due        = $task['due']      ?? 'Unscheduled';
    $priority   = $task['priority'] ?? 'Normal';
    $progress   = $task['progress'] ?? '';
    $assigneeIds = (array)($task['assignees'] ?? []);
    $isSubtask  = ($variant === 'subtask');

    // Status icon & tooltip
    $statusIcon  = 'bi-circle';
    $statusClass = '';
    $statusTitle = 'None';
    if ($status === 'in-progress') {
        $statusIcon  = 'bi-dash-circle-fill text-primary';
        $statusClass = 'in-progress';
        $statusTitle = 'In Progress';
    } elseif ($status === 'done') {
        $statusIcon  = 'bi-check-circle-fill text-success';
        $statusClass = 'done';
        $statusTitle = 'Done';
    }

    // Priority badge colour
    $priClass = 'bg-secondary';
    if ($priority === 'High')                         $priClass = 'bg-danger';
    elseif ($priority === 'Medium' || $priority === 'Normal') $priClass = 'bg-warning text-dark';
    elseif ($priority === 'Low')                      $priClass = 'bg-info text-dark';

    // Expand button (tasks with subtasks only)
    $expandBtn  = '';
    $hasSubtasks = !empty($task['subtasks']);
    if ($hasSubtasks && !$isSubtask) {
        $expandBtn = sprintf(
            '<button class="btn btn-link btn-sm p-0 me-1 expand-btn" type="button"
                     data-collapse-toggle="true" data-target="#subtasks_%s"
                     aria-expanded="true" aria-controls="subtasks_%s">
                <i class="bi bi-chevron-right text-muted" style="font-size:12px;"></i>
            </button>',
            $id,
            $id
        );
    }

    $avatarHtml = renderAssigneeAvatars($assigneeIds, $staffMap, $avatarColors);
    $parentAttr = $isSubtask ? 'data-parent-id="' . htmlspecialchars($parentId) . '"' : '';
    $rowPad     = $isSubtask ? 'ps-4 subtask-row' : '';

    // Due date display
    $dueBadge = ($due === 'Unscheduled' || $due === '')
        ? '<span class="text-muted" style="font-size:11px;">Unscheduled</span>'
        : '<span class="fw-medium" style="font-size:12px;">' . htmlspecialchars($due) . '</span>';

    // Build staff options for inline assignee select

    $staffOptionsHtml = '<option value="">Unassigned</option>';
    foreach ($staffMap as $s) {
        $sel = in_array($s['id'], $assigneeIds) ? 'selected' : '';
        $staffOptionsHtml .= sprintf(
            '<option value="%s" %s>%s</option>',
            htmlspecialchars($s['id']),
            $sel,
            htmlspecialchars($s['name'])
        );
    }

    // Priority options
    $priOptions = '';
    foreach (['High', 'Normal', 'Low'] as $p) {
        $sel = ($priority === $p || ($priority === 'Medium' && $p === 'Normal')) ? 'selected' : '';
        $priOptions .= "<option value=\"{$p}\" {$sel}>{$p}</option>";
    }

    // Progress badge
    $progressBadge = $progress
        ? sprintf('<div class="task-attr"><span class="task-attr-label">Progress</span><span class="badge bg-light text-dark border" style="font-size:10px;">%s</span></div>', htmlspecialchars($progress))
        : '';

    return sprintf(
        // Row wrapper
        '<div class="task-item-row %s" id="row_%s" data-id="%s" data-type="%s" data-proj-id="%s" %s>
            <div class="task-row-left">
                %s

                <!-- STATUS: click to cycle None → In Progress → Done (tooltip shows current state) -->
                <div class="status-wrapper">                
                    <button class="progress-btn btn btn-link p-0 border-0 %s"
                            data-id="%s" data-type="%s"
                            title="Status: %s"
                            data-tooltip="true" data-tooltip-placement="top">
                        <i class="bi %s" style="font-size:18px;"></i>
                    </button>
                </div>

                <!-- TASK NAME: click to edit inline -->
                <div class="task-name-cell" style="flex:1;">
                    <span class="task-name-display fw-semibold text-dark"
                          data-id="%s" data-type="%s"
                          title="Click to edit task name">%s</span>
                    <input type="text" class="task-name-input form-control form-control-sm"
                           style="display:none; max-width:280px;" value="%s" />
                </div>
            </div>

            <div class="task-row-right">

                <!-- ASSIGNEE: click avatar to open select -->
                <div class="task-attr task-assignee-cell" style="min-width:80px; cursor:pointer;"
                     data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s"
                     title="Click to change assignee">
                    <span class="task-attr-label">Assignee</span>
                    <div class="assignee-display">%s</div>
                    <select class="form-select form-select-sm rounded-2 border-primary assignee-inline-select"
                            style="display:none; min-width:120px; font-size:11px;"
                            data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s">
                        %s
                    </select>
                </div>

                <!-- DUE DATE: click to open date picker -->
                <div class="task-attr task-due-cell" style="min-width:90px; cursor:pointer;"
                     data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s"
                     title="Click to change due date">
                    <span class="task-attr-label">Due</span>
                    <span class="d-flex align-items-center gap-1 due-display">
                        <i class="bi bi-calendar3 text-muted" style="font-size:11px;"></i>
                        %s
                    </span>
                    <input type="date" class="form-control form-control-sm rounded-2 border-primary due-inline-input"
                           style="display:none; min-width:120px; font-size:11px;"
                           value="%s"
                           data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s" />
                </div>

                <!-- PRIORITY: click to open select -->
                <div class="task-attr task-priority-cell" style="min-width:70px; cursor:pointer;"
                     data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s"
                     title="Click to change priority">
                    <span class="task-attr-label">Priority</span>
                    <span class="badge %s rounded-pill px-2 py-1 priority-display" style="font-size:10px;">%s</span>
                    <select class="form-select form-select-sm rounded-2 border-primary priority-inline-select"
                            style="display:none; min-width:80px; font-size:11px;"
                            data-id="%s" data-type="%s" data-proj-id="%s" data-parent-id="%s">
                        %s
                    </select>
                </div>

                <!-- PROGRESS -->
                %s

                <!-- DELETE ONLY — no edit button needed (all fields editable inline) -->
                <div class="task-attr">
                    <button class="btn btn-link btn-sm p-0 more-actions-btn text-muted"
                            data-id="%s" data-type="%s" data-name="%s" title="Delete">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                </div>
            </div>
        </div>',
        // Row wrapper
        $rowPad,
        $id,
        $id,
        $variant,
        htmlspecialchars($projId),
        $parentAttr,
        // Expand button
        $expandBtn,
        // Status button
        $statusClass,
        $id,
        $variant,
        $statusTitle,
        $statusIcon,
        // Task name
        $id,
        $variant,
        $name,
        htmlspecialchars($task['name']),
        // Assignee cell
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        $avatarHtml,
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        $staffOptionsHtml,
        // Due date cell
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        $dueBadge,
        ($due === 'Unscheduled' ? '' : htmlspecialchars($due)),
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        // Priority cell
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        $priClass,
        htmlspecialchars($priority),
        $id,
        $variant,
        htmlspecialchars($projId),
        htmlspecialchars($parentId),
        $priOptions,
        // Progress + actions
        $progressBadge,
        $id,
        $variant,
        htmlspecialchars($task['name'])
    );
}

// -------------------------------------------------------
// Render all tasks (with nested subtasks and add-subtask button)
// -------------------------------------------------------
function renderAllTasks($tasks, $projId, $staffMap, $avatarColors)
{
    $html = '';
    foreach ($tasks as $task) {
        $html .= renderTaskRowInline($task, 'task', $projId, $staffMap, $avatarColors);

        if (!empty($task['subtasks'])) {
            $tid  = htmlspecialchars($task['id']);
            $html .= sprintf('<div class="collapse show subtask-group" id="subtasks_%s">', $tid);
            foreach ($task['subtasks'] as $sub) {
                $html .= renderTaskRowInline($sub, 'subtask', $projId, $staffMap, $avatarColors, $task['id']);
            }
            // Inline add-subtask trigger
            $html .= sprintf(
                '<div class="inline-add-subtask-row ps-4" data-parent-id="%s" data-proj-id="%s">
                    <button class="btn btn-link btn-sm text-primary fw-semibold p-0 add-subtask-inline-btn"
                            data-parent-id="%s" data-proj-id="%s">
                        <i class="bi bi-plus-lg me-1"></i><span>Add Subtask</span>
                    </button>
                </div>',
                $tid,
                htmlspecialchars($projId),
                $tid,
                htmlspecialchars($projId)
            );
            $html .= '</div>';
        }
    }
    return $html;
}
?>

<div class="row g-4">

    <!-- ============================================================
         LEFT SIDEBAR — Project Lists
         ============================================================ -->
    <div class="col-12 col-lg-4 col-xl-3">
        <div class="d-flex flex-column gap-3">

            <!-- IN PROGRESS PROJECTS -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-3 px-3 pb-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-dot bg-primary"></span>
                        <span class="fw-bold text-uppercase" style="font-size:11px; letter-spacing:0.8px; color:#2563EB;">In Progress</span>
                        <span class="badge bg-light text-primary border rounded-pill px-2"><?php echo count($inprogressProjects); ?></span>
                    </div>
                    <!-- Add new in-progress project -->
                    <button class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 toggle-inline-create-btn"
                        data-target="inline-create-inprogress"
                        style="width:24px;height:24px;" title="Add new project">
                        <i class="bi bi-plus-lg" style="font-size:11px;"></i>
                    </button>
                </div>

                <!-- Inline create form (In Progress) -->
                <div id="inline-create-inprogress" class="inline-project-form px-3 pb-2" style="display:none;">
                    <form method="POST" action="" class="d-flex flex-column gap-2">
                        @csrf
                        <input type="hidden" name="action" value="save_project" />
                        <input type="hidden" name="mode" value="create" />
                        <input type="hidden" name="status" value="inprogress" />
                        <div>
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px;">Project Name</label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Enter project name..." required />
                        </div>
                        <div>
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px;">Description</label>
                            <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Short description..." />
                        </div>
                        <div class="d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:12px;">Create</button>
                            <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-create-btn" data-target="inline-create-inprogress" style="font-size:12px;">Cancel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body px-2 pt-0 pb-2">
                    <?php if (empty($inprogressProjects)): ?>
                        <p class="text-muted text-center small py-3 mb-0">No active projects</p>
                    <?php else: ?>
                        <?php foreach ($inprogressProjects as $p):
                            $isActive = ($selectedProjId === $p['id']);
                        ?>
                            <div class="project-list-item <?php echo $isActive ? 'active' : ''; ?>" data-proj-id="<?php echo $p['id']; ?>">

                                <!-- View mode -->
                                <div class="project-list-view d-flex align-items-center gap-2 <?php echo $isActive ? : ''; ?>" id="view-<?php echo $p['id']; ?>">
                                    <a href="?page=project&project_id=<?php echo $p['id']; ?>"
                                        class="text-decoration-none flex-grow-1 text-truncate fw-medium <?php echo 'text-dark'; ?>">
                                        <?php echo htmlspecialchars($p['name']); ?>
                                    </a>
                                    <button class="btn btn-link btn-sm p-0 more-actions-btn <?php echo 'text-muted'; ?>"
                                        data-id="<?php echo $p['id']; ?>"
                                        data-type="project"
                                        data-name="<?php echo htmlspecialchars($p['name']); ?>"
                                        title="Project options">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </div>

                                <!-- Inline edit mode (triggered from more-actions dropdown) -->
                                <div class="project-inline-edit" id="edit-<?php echo $p['id']; ?>" style="display:none;">
                                    <form method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="action" value="save_project" />
                                        <input type="hidden" name="mode" value="edit" />
                                        <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>" />
                                        <div class="mb-1">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Name</label>
                                            <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" value="<?php echo htmlspecialchars($p['name']); ?>" required />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Description</label>
                                            <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light" value="<?php echo htmlspecialchars($p['description'] ?? ''); ?>" />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Status</label>
                                            <select name="status" class="form-select form-select-sm rounded-3 border-light">
                                                <option value="inprogress" <?php echo ($p['status'] !== 'done') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="done" <?php echo ($p['status'] === 'done') ? 'selected' : ''; ?>>Done</option>
                                            </select>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:11px;">Save</button>
                                            <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-edit-btn" data-proj-id="<?php echo $p['id']; ?>" style="font-size:11px;">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- COMPLETED PROJECTS -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-3 px-3 pb-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-dot bg-success"></span>
                        <span class="fw-bold text-uppercase" style="font-size:11px; letter-spacing:0.8px; color:#10B981;">Completed</span>
                        <span class="badge bg-light text-success border rounded-pill px-2"><?php echo count($doneProjects); ?></span>
                    </div>
                    
                </div>

                <!-- Inline create form (Done) -->
                <div id="inline-create-done" class="inline-project-form px-3 pb-2" style="display:none;">
                    <form method="POST" action="" class="d-flex flex-column gap-2">
                        @csrf
                        <input type="hidden" name="action" value="save_project" />
                        <input type="hidden" name="mode" value="create" />
                        <input type="hidden" name="status" value="done" />
                        <div>
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px;">Project Name</label>
                            <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Enter project name..." required />
                        </div>
                        <div>
                            <label class="form-label fw-bold text-dark mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px;">Description</label>
                            <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Short description..." />
                        </div>
                        <div class="d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-success btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:12px;">Create</button>
                            <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-create-btn" data-target="inline-create-done" style="font-size:12px;">Cancel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body px-2 pt-0 pb-2">
                    <?php if (empty($doneProjects)): ?>
                        <p class="text-muted text-center small py-3 mb-0">No completed projects</p>
                    <?php else: ?>
                        <?php foreach ($doneProjects as $p):
                            $isActive = ($selectedProjId === $p['id']);
                        ?>
                            <div class="project-list-item <?php echo $isActive ? 'active done' : ''; ?>" data-proj-id="<?php echo $p['id']; ?>">

                                <!-- View mode -->
                                <div class="project-list-view d-flex align-items-center gap-2" id="view-<?php echo $p['id']; ?>">
                                    <a href="?page=project&project_id=<?php echo $p['id']; ?>"
                                        class="text-decoration-none flex-grow-1 text-truncate fw-medium <?php echo $isActive ? 'text-white' : 'text-dark'; ?>">
                                        <?php echo htmlspecialchars($p['name']); ?>
                                    </a>
                                    <button class="btn btn-link btn-sm p-0 more-actions-btn <?php echo 'text-muted'; ?>"
                                        data-id="<?php echo $p['id']; ?>"
                                        data-type="project"
                                        data-name="<?php echo htmlspecialchars($p['name']); ?>"
                                        title="Project options">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                </div>

                                <!-- Inline edit mode -->
                                <div class="project-inline-edit" id="edit-<?php echo $p['id']; ?>" style="display:none;">
                                    <form method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="action" value="save_project" />
                                        <input type="hidden" name="mode" value="edit" />
                                        <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>" />
                                        <div class="mb-1">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Name</label>
                                            <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" value="<?php echo htmlspecialchars($p['name']); ?>" required />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Description</label>
                                            <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light" value="<?php echo htmlspecialchars($p['description'] ?? ''); ?>" />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Status</label>
                                            <select name="status" class="form-select form-select-sm rounded-3 border-light">
                                                <option value="inprogress" <?php echo ($p['status'] !== 'done') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="done" <?php echo ($p['status'] === 'done') ? 'selected' : ''; ?>>Done</option>
                                            </select>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:11px;">Save</button>
                                            <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-edit-btn" data-proj-id="<?php echo $p['id']; ?>" style="font-size:11px;">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================================
         RIGHT PANEL — Active Project Workspace
         ============================================================ -->
    <div class="col-12 col-lg-8 col-xl-9">
        <div class="card border-0 shadow-sm h-100 rounded-4">

            <?php if (!$activeProject): ?>
                <!-- Empty state -->
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                    <i class="bi bi-kanban fs-1 text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">Select a project to view tasks</h5>
                    <p class="text-muted small">Choose from the left sidebar to start managing your workflow.</p>
                </div>

            <?php else: ?>

                <!-- Project Header -->
                <div class="card-header bg-white border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-start">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($activeProject['name']); ?></h4>                                                        
                        </div>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($activeProject['description'] ?? 'No description provided.'); ?></p>
                    </div>
                    <!-- Project-level more actions (edit name/desc/status or delete) -->
                    <button class="btn btn-light btn-sm rounded-3 more-actions-btn"
                        data-id="<?php echo $activeProject['id']; ?>"
                        data-type="project"
                        data-name="<?php echo htmlspecialchars($activeProject['name']); ?>"
                        title="Project actions">
                        <i class="bi bi-trash text-danger"></i>
                    </button>
                </div>

                <!-- Task Table Column Headers -->
                <div class="task-table-header px-4 py-2">
                    <div class="task-row-left">
                        <span class="task-col-label" style="min-width:70px;">Status</span>
                        <span class="task-col-label">Task Name</span>
                    </div>  
                    <div class="task-row-right">
                        <span class="task-col-label" style="min-width:80px;">Assignee</span>
                        <span class="task-col-label" style="min-width:90px;">Due Date</span>
                        <span class="task-col-label" style="min-width:70px;">Priority</span>
                        <span class="task-col-label" style="min-width:60px;">Progress</span>
                        <span class="task-col-label" style="min-width:30px;"></span>
                    </div>
                </div>

                <!-- Task List -->
                <div class="card-body px-4 pt-2 pb-4">
                    <div class="task-list">
                        <?php
                        if (empty($activeProject['tasks'])) {
                            echo '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 opacity-25 d-block mb-2"></i>No tasks yet. Create your first task below.</div>';
                        } else {
                            echo renderAllTasks($activeProject['tasks'], $selectedProjId, $staffMap, $avatarColors);
                        }
                        ?>

                        <!-- Inline Add Task Row (shown on "Create New Task" click) -->
                        <div id="inline-add-task-row" style="display:none;" class="inline-task-form mt-2">
                            <form method="POST" action=""
                                class="d-flex flex-wrap gap-2 align-items-end p-3 rounded-3 bg-white border shadow-sm">
                                @csrf
                                <input type="hidden" name="action" value="save_task" />
                                <input type="hidden" name="mode" value="create" />
                                <input type="hidden" name="type" value="task" />
                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjId); ?>" />
                                <div style="flex:1; min-width:200px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Task Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" placeholder="Enter task name..." required autofocus />
                                </div>
                                <div style="min-width:120px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Priority</label>
                                    <select name="priority" class="form-select form-select-sm rounded-3 border-light">
                                        <option value="High">High</option>
                                        <option value="Normal" selected>Normal</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                                <div style="min-width:140px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Assignee</label>
                                    <select name="assignee" class="form-select form-select-sm rounded-3 border-light">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($staff as $s): ?>
                                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="min-width:130px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Due Date</label>
                                    <input type="date" name="due" class="form-control form-control-sm rounded-3 border-light" />
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-3 px-3" style="font-size:12px; height:32px;">Add Task</button>
                                    <button type="button" class="btn btn-light btn-sm rounded-3 px-3" id="cancel-add-task" style="font-size:12px; height:32px;">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Inline Add Subtask Form Template (cloned by JS per task) -->
                        <div id="inline-add-subtask-template" style="display:none;">
                            <form method="POST" action=""
                                class="d-flex flex-wrap gap-2 align-items-end p-2 rounded-3 bg-white border shadow-sm ms-4 mt-1 mb-2">
                                @csrf
                                <input type="hidden" name="action" value="save_task" />
                                <input type="hidden" name="mode" value="create" />
                                <input type="hidden" name="type" value="subtask" />
                                <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjId); ?>" />
                                <input type="hidden" name="parent_id" value="" />
                                <div style="flex:1; min-width:180px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Subtask Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" placeholder="Enter subtask name..." required />
                                </div>
                                
                                <div style="min-width:140px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:10px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Assignee</label>
                                    <select name="assignee" class="form-select form-select-sm rounded-3 border-light">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($staff as $s): ?>
                                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div style="min-width:120px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Due Date</label>
                                    <input type="date" name="due" class="form-control form-control-sm rounded-3 border-light" />
                                </div>
                                <div style="min-width:110px;">
                                    <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Priority</label>
                                    <select name="priority" class="form-select form-select-sm rounded-3 border-light">
                                        <option value="High">High</option>
                                        <option value="Normal" selected>Normal</option>
                                        <option value="Low">Low</option>
                                    </select>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm fw-bold rounded-3 px-3" style="font-size:11px; height:30px;">Add</button>
                                    <button type="button" class="btn btn-light btn-sm rounded-3 px-2 cancel-subtask-inline" style="font-size:11px; height:30px;">Cancel</button>
                                </div>
                            </form>
                        </div>

                        <!-- Create Task Button -->
                        <div class="mt-3">
                            <button id="show-add-task-btn"
                                class="btn btn-outline-secondary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center fw-bold"
                                style="border-style:dashed; border-width:2px; background:#f8fafc; font-size:14px;">
                                <i class="bi bi-plus-lg me-2"></i>
                                <span>Create New Task</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Hidden form for inline task field saves (name, assignee, due, priority, status) -->
<form id="save-task-field-form" method="POST" action="" style="display:none;">
    @csrf
    <input type="hidden" name="action" value="save_task" />
    <input type="hidden" name="mode" value="edit" />
    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjId); ?>" />
    <input type="hidden" name="task_id" id="stf-id" />
    <input type="hidden" name="parent_id" id="stf-parent" />
    <input type="hidden" name="type" id="stf-type" />
    <input type="hidden" name="name" id="stf-name" />
    <input type="hidden" name="priority" id="stf-priority" />
    <input type="hidden" name="due" id="stf-due" />
    <input type="hidden" name="assignee" id="stf-assignee" />
    <input type="hidden" name="status" id="stf-status" />
</form>

<!-- Hidden form for deleting items (projects, tasks, subtasks) -->
<form id="delete-item-form" method="POST" action="" style="display:none;">
    @csrf
    <input type="hidden" name="action" id="delete-action" />
    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($selectedProjId); ?>" />
    <input type="hidden" name="task_id" id="delete-task-id" />
    <input type="hidden" name="parent_id" id="delete-parent-id" />
</form>

<script>
// Expose the initial PHP state of projects and staff to Javascript
const projectsState = @json($projects);
const staffMap = @json($staffMap);
const avatarColors = @json($avatarColors);
let currentProjId = '<?php echo htmlspecialchars($selectedProjId); ?>';

function absCrc32(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    return Math.abs(hash);
}

function getAvatarColor(name) {
    return avatarColors[absCrc32(name) % avatarColors.length];
}

function getInitials(name) {
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function renderAssigneeAvatars(assigneeIds) {
    if (!assigneeIds || assigneeIds.length === 0) {
        return '<span class="text-muted" style="font-size:11px; opacity:0.5;" title="No assignee">—</span>';
    }
    let html = '<div class="d-flex align-items-center" style="gap:-4px;">';
    let count = 0;
    const max = 3;
    assigneeIds.forEach(sid => {
        if (count >= max) return;
        if (!staffMap[sid]) return;
        const m = staffMap[sid];
        html += `<div class="avatar-chip" style="background:${getAvatarColor(m.name)};" title="${escapeHtml(m.name)} · ${escapeHtml(m.role)}">${escapeHtml(getInitials(m.name))}</div>`;
        count++;
    });
    const extra = assigneeIds.length - count;
    if (extra > 0) {
        html += `<div class="avatar-chip avatar-chip-more">+${extra}</div>`;
    }
    html += '</div>';
    return html;
}

function renderTaskRowInlineJS(task, variant, projId, parentId = '') {
    const id = task.id;
    const name = task.name;
    const status = task.status || 'none';
    const due = task.due || 'Unscheduled';
    const priority = task.priority || 'Normal';
    const progress = task.progress || '';
    const assigneeIds = task.assignees || [];
    const isSubtask = (variant === 'subtask');

    let statusIcon = 'bi-circle';
    let statusClass = '';
    let statusTitle = 'None';
    if (status === 'in-progress') {
        statusIcon = 'bi-dash-circle-fill text-primary';
        statusClass = 'in-progress';
        statusTitle = 'In Progress';
    } else if (status === 'done') {
        statusIcon = 'bi-check-circle-fill text-success';
        statusClass = 'done';
        statusTitle = 'Done';
    }

    let priClass = 'bg-secondary';
    if (priority === 'High') priClass = 'bg-danger';
    else if (priority === 'Medium' || priority === 'Normal') priClass = 'bg-warning text-dark';
    else if (priority === 'Low') priClass = 'bg-info text-dark';

    const hasSubtasks = task.subtasks && Object.keys(task.subtasks).length > 0;
    let expandBtn = '';
    if (!isSubtask) {
        // Always render expand btn; hidden (opacity:0) when task has no subtasks
        const hiddenStyle = hasSubtasks ? '' : 'opacity:0; pointer-events:none;';
        expandBtn = `
            <button class="btn btn-sm p-0 me-1 expand-btn" type="button"
                     data-target="subtasks_${id}"
                     aria-expanded="true"
                     style="background:none; border:none; line-height:1; ${hiddenStyle}">
                <i class="bi bi-chevron-down" style="font-size:13px; color:#94a3b8;"></i>
            </button>`;
    }

    const avatarHtml = renderAssigneeAvatars(assigneeIds);
    const parentAttr = isSubtask ? `data-parent-id="${parentId}"` : '';
    const rowPad = isSubtask ? 'ps-4 subtask-row' : '';

    const dueBadge = (due === 'Unscheduled' || due === '')
        ? '<span class="text-muted" style="font-size:11px;">Unscheduled</span>'
        : `<span class="fw-medium" style="font-size:12px;">${escapeHtml(due)}</span>`;

    let staffOptionsHtml = '<option value="">Unassigned</option>';
    for (const sid in staffMap) {
        const s = staffMap[sid];
        const sel = assigneeIds.includes(sid) ? 'selected' : '';
        staffOptionsHtml += `<option value="${s.id}" ${sel}>${escapeHtml(s.name)}</option>`;
    }

    let priOptions = '';
    ['High', 'Normal', 'Low'].forEach(p => {
        const sel = (priority === p) ? 'selected' : '';
        priOptions += `<option value="${p}" ${sel}>${p}</option>`;
    });

    const progressBadge = progress
        ? `<div class="task-attr"><span class="task-attr-label">Progress</span><span class="badge bg-light text-dark border" style="font-size:10px;">${escapeHtml(progress)}</span></div>`
        : '';

    return `
        <div class="task-item-row ${rowPad}" id="row_${id}" data-id="${id}" data-type="${variant}" data-proj-id="${projId}" ${parentAttr}>
            <div class="task-row-left">
                ${expandBtn}
                <div class="status-wrapper">                
                    <button class="progress-btn btn btn-link p-0 border-0 ${statusClass}"
                            data-id="${id}" data-type="${variant}"
                            title="Status: ${statusTitle}"
                            data-tooltip="true" data-tooltip-placement="top">
                        <i class="bi ${statusIcon}" style="font-size:18px;"></i>
                    </button>
                </div>
                <div class="task-name-cell" style="flex:1;">
                    <span class="task-name-display fw-semibold text-dark"
                          data-id="${id}" data-type="${variant}"
                          title="Click to edit task name">${escapeHtml(name)}</span>
                    <input type="text" class="task-name-input form-control form-control-sm"
                           style="display:none; max-width:280px;" value="${escapeHtml(name)}" />
                </div>
            </div>
            <div class="task-row-right">
                <div class="task-attr task-assignee-cell" style="min-width:80px; cursor:pointer;"
                     data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}"
                     title="Click to change assignee">
                    <span class="task-attr-label">Assignee</span>
                    <div class="assignee-display">${avatarHtml}</div>
                    <select class="form-select form-select-sm rounded-2 border-primary assignee-inline-select"
                            style="display:none; min-width:120px; font-size:11px;"
                            data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}">
                        ${staffOptionsHtml}
                    </select>
                </div>
                <div class="task-attr task-due-cell" style="min-width:90px; cursor:pointer;"
                     data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}"
                     title="Click to change due date">
                    <span class="task-attr-label">Due</span>
                    <span class="d-flex align-items-center gap-1 due-display">
                        <i class="bi bi-calendar3 text-muted" style="font-size:11px;"></i>
                        ${dueBadge}
                    </span>
                    <input type="date" class="form-control form-control-sm rounded-2 border-primary due-inline-input"
                           style="display:none; min-width:120px; font-size:11px;"
                           value="${due === 'Unscheduled' ? '' : due}"
                           data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}" />
                </div>
                <div class="task-attr task-priority-cell" style="min-width:70px; cursor:pointer;"
                     data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}"
                     title="Click to change priority">
                    <span class="task-attr-label">Priority</span>
                    <span class="badge ${priClass} rounded-pill px-2 py-1 priority-display" style="font-size:10px;">${priority}</span>
                    <select class="form-select form-select-sm rounded-2 border-primary priority-inline-select"
                            style="display:none; min-width:80px; font-size:11px;"
                            data-id="${id}" data-type="${variant}" data-proj-id="${projId}" data-parent-id="${parentId}">
                        ${priOptions}
                    </select>
                </div>
                ${progressBadge}
                <div class="task-attr">
                    <button class="btn btn-link btn-sm p-0 more-actions-btn text-danger"
                            data-id="${id}" data-type="${variant}" data-name="${escapeHtml(name)}" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

function renderActiveProject(projId) {
    currentProjId = projId;
    const proj = projectsState[projId];
    if (!proj) {
        const rightPanel = document.querySelector('.col-lg-8.col-xl-9');
        if (rightPanel) {
            rightPanel.innerHTML = `
                <div class="card border-0 shadow-sm h-100 rounded-4">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-5">
                        <i class="bi bi-kanban fs-1 text-muted opacity-25 mb-3"></i>
                        <h5 class="text-muted">Select a project to view tasks</h5>
                        <p class="text-muted small">Choose from the left sidebar to start managing your workflow.</p>
                    </div>
                </div>`;
        }
        return;
    }

    // Highlight active project in sidebar
    document.querySelectorAll('.project-list-item').forEach(item => {
        item.classList.remove('active', 'done');
        const id = item.getAttribute('data-proj-id');
        if (id === projId) {
            item.classList.add('active');
            if (proj.status === 'done') {
                item.classList.add('done');
            }
        }
    });

    const headerTitle = document.querySelector('.card-header h4');
    if (headerTitle) headerTitle.textContent = proj.name;

    const headerDesc = document.querySelector('.card-header p');
    if (headerDesc) headerDesc.textContent = proj.description || 'No description provided.';

    const headerMoreBtn = document.querySelector('.card-header .more-actions-btn');
    if (headerMoreBtn) {
        headerMoreBtn.setAttribute('data-id', proj.id);
        headerMoreBtn.setAttribute('data-name', proj.name);
    }

    const taskListContainer = document.querySelector('.task-list');
    if (taskListContainer) {
        const inlineAddTaskRow = document.getElementById('inline-add-task-row');
        const inlineAddSubtaskTemplate = document.getElementById('inline-add-subtask-template');
        
        taskListContainer.innerHTML = '';
        const tasks = proj.tasks || {};
        const taskKeys = Object.keys(tasks);
        
        if (taskKeys.length === 0) {
            const emptyEl = document.createElement('div');
            emptyEl.className = 'text-center py-5 text-muted empty-state-placeholder';
            emptyEl.innerHTML = '<i class="bi bi-inbox fs-2 opacity-25 d-block mb-2"></i>No tasks yet. Create your first task below.';
            taskListContainer.appendChild(emptyEl);
        } else {
            let html = '';
            for (const tid in tasks) {
                const task = tasks[tid];
                html += renderTaskRowInlineJS(task, 'task', projId);

                const subtasks = task.subtasks || {};
                const subtaskKeys = Object.keys(subtasks);

                // Always render subtask group container (even if empty) so Add Subtask always visible
                html += `<div class="subtask-group show" id="subtasks_${task.id}">`;  
                subtaskKeys.forEach(sid => {
                    html += renderTaskRowInlineJS(subtasks[sid], 'subtask', projId, task.id);
                });

                html += `
                    <div class="inline-add-subtask-row ps-4" data-parent-id="${task.id}" data-proj-id="${projId}">
                        <button class="btn btn-link btn-sm text-primary fw-semibold p-0 add-subtask-inline-btn"
                                data-parent-id="${task.id}" data-proj-id="${projId}">
                            <i class="bi bi-plus-lg me-1"></i><span>Add Subtask</span>
                        </button>
                    </div>`;
                html += '</div>';
            }
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            while (tempDiv.firstChild) {
                taskListContainer.appendChild(tempDiv.firstChild);
            }
        }

        if (inlineAddTaskRow) {
            inlineAddTaskRow.style.display = 'none';
            inlineAddTaskRow.querySelector('input[name="project_id"]').value = projId;
            taskListContainer.appendChild(inlineAddTaskRow);
        }

        if (inlineAddSubtaskTemplate) {
            inlineAddSubtaskTemplate.querySelector('input[name="project_id"]').value = projId;
            taskListContainer.appendChild(inlineAddSubtaskTemplate);
        }

        const createBtnContainer = document.createElement('div');
        createBtnContainer.className = 'mt-3';
        createBtnContainer.innerHTML = `
            <button id="show-add-task-btn"
                class="btn btn-outline-secondary w-100 py-2 rounded-3 d-flex align-items-center justify-content-center fw-bold"
                style="border-style:dashed; border-width:2px; background:#f8fafc; font-size:14px;">
                <i class="bi bi-plus-lg me-2"></i>
                <span>Create New Task</span>
            </button>`;
        taskListContainer.appendChild(createBtnContainer);
    }

    attachTaskEventListeners();
}

function renderSidebarProjects() {
    const inprogressList = [];
    const doneList = [];

    for (const pid in projectsState) {
        const p = projectsState[pid];
        if (p.status === 'done') {
            doneList.push(p);
        } else {
            inprogressList.push(p);
        }
    }

    const inprogressCard = document.querySelector('[data-target="inline-create-inprogress"]').closest('.card');
    const doneCard = document.querySelector('[data-target="inline-create-done"]').closest('.card');

    const inprogressBody = inprogressCard.querySelector('.card-body');
    const doneBody = doneCard.querySelector('.card-body');

    inprogressCard.querySelector('.badge').textContent = inprogressList.length;
    doneCard.querySelector('.badge').textContent = doneList.length;

    if (inprogressList.length === 0) {
        inprogressBody.innerHTML = '<p class="text-muted text-center small py-3 mb-0">No active projects</p>';
    } else {
        let html = '';
        inprogressList.forEach(p => {
            const isActive = (currentProjId === p.id);
            html += `
                <div class="project-list-item ${isActive ? 'active' : ''}" data-proj-id="${p.id}">
                    <div class="project-list-view d-flex align-items-center gap-2" id="view-${p.id}">
                        <a href="?page=project&project_id=${p.id}"
                            class="text-decoration-none flex-grow-1 text-truncate fw-medium text-dark">
                            ${escapeHtml(p.name)}
                        </a>
                        <button class="btn btn-link btn-sm p-0 more-actions-btn text-muted"
                            data-id="${p.id}"
                            data-type="project"
                            data-name="${escapeHtml(p.name)}"
                            title="Project options">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div class="project-inline-edit" id="edit-${p.id}" style="display:none;">
                        <form class="project-edit-form">
                            <input type="hidden" name="project_id" value="${p.id}" />
                            <div class="mb-1">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Name</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" value="${escapeHtml(p.name)}" required />
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light" value="${escapeHtml(p.description || '')}" />
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-3 border-light">
                                    <option value="inprogress" ${p.status !== 'done' ? 'selected' : ''}>In Progress</option>
                                    <option value="done" ${p.status === 'done' ? 'selected' : ''}>Done</option>
                                </select>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:11px;">Save</button>
                                <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-edit-btn" data-proj-id="${p.id}" style="font-size:11px;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>`;
        });
        inprogressBody.innerHTML = html;
    }

    if (doneList.length === 0) {
        doneBody.innerHTML = '<p class="text-muted text-center small py-3 mb-0">No completed projects</p>';
    } else {
        let html = '';
        doneList.forEach(p => {
            const isActive = (currentProjId === p.id);
            html += `
                <div class="project-list-item ${isActive ? 'active done' : ''}" data-proj-id="${p.id}">
                    <div class="project-list-view d-flex align-items-center gap-2" id="view-${p.id}">
                        <a href="?page=project&project_id=${p.id}"
                            class="text-decoration-none flex-grow-1 text-truncate fw-medium ${isActive ? 'text-white' : 'text-dark'}">
                            ${escapeHtml(p.name)}
                        </a>
                        <button class="btn btn-link btn-sm p-0 more-actions-btn text-muted"
                            data-id="${p.id}"
                            data-type="project"
                            data-name="${escapeHtml(p.name)}"
                            title="Project options">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                    <div class="project-inline-edit" id="edit-${p.id}" style="display:none;">
                        <form class="project-edit-form">
                            <input type="hidden" name="project_id" value="${p.id}" />
                            <div class="mb-1">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Name</label>
                                <input type="text" name="name" class="form-control form-control-sm rounded-3 border-light" value="${escapeHtml(p.name)}" required />
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm rounded-3 border-light" value="${escapeHtml(p.description || '')}" />
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold mb-1" style="font-size:9px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-3 border-light">
                                    <option value="inprogress" ${p.status !== 'done' ? 'selected' : ''}>In Progress</option>
                                    <option value="done" ${p.status === 'done' ? 'selected' : ''}>Done</option>
                                </select>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-bold rounded-3" style="font-size:11px;">Save</button>
                                <button type="button" class="btn btn-light btn-sm rounded-3 cancel-inline-edit-btn" data-proj-id="${p.id}" style="font-size:11px;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>`;
        });
        doneBody.innerHTML = html;
    }

    attachSidebarEventListeners();
}

function attachSidebarEventListeners() {
    document.querySelectorAll('.project-list-view a').forEach(a => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            const url = new URL(a.href, window.location.origin);
            const projId = url.searchParams.get('project_id');
            if (projId) {
                history.pushState(null, '', `?page=project&project_id=${projId}`);
                renderActiveProject(projId);
            }
        });
    });

    document.querySelectorAll('.project-list-item').forEach(item => {
        const projId = item.getAttribute('data-proj-id');
        const viewEl = document.getElementById(`view-${projId}`);
        const editEl = document.getElementById(`edit-${projId}`);
        const moreBtn = viewEl ? viewEl.querySelector('.more-actions-btn') : null;
        const cancelBtn = editEl ? editEl.querySelector('.cancel-inline-edit-btn') : null;
        const form = editEl ? editEl.querySelector('form') : null;

        if (moreBtn && editEl && viewEl) {
            moreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                document.querySelectorAll('.project-inline-edit').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.project-list-view').forEach(el => el.style.display = 'flex');

                viewEl.style.display = 'none';
                editEl.style.display = 'block';
                const input = editEl.querySelector('input[name="name"]');
                if (input) input.focus();
            });
        }

        if (cancelBtn && editEl && viewEl) {
            cancelBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                editEl.style.display = 'none';
                viewEl.style.display = 'flex';
            });
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const name = form.querySelector('input[name="name"]').value.trim();
                const description = form.querySelector('input[name="description"]').value.trim();
                const status = form.querySelector('select[name="status"]').value;

                if (name === '') return;

                projectsState[projId].name = name;
                projectsState[projId].description = description;
                projectsState[projId].status = status;

                renderSidebarProjects();
                if (currentProjId === projId) {
                    renderActiveProject(projId);
                }
            });
        }
    });
}

function attachTaskEventListeners() {
    const showAddTaskBtn = document.getElementById('show-add-task-btn');
    const inlineAddTaskRow = document.getElementById('inline-add-task-row');
    const cancelAddTaskBtn = document.getElementById('cancel-add-task');

    if (showAddTaskBtn && inlineAddTaskRow) {
        showAddTaskBtn.addEventListener('click', () => {
            showAddTaskBtn.style.display = 'none';
            inlineAddTaskRow.style.display = 'block';
            const input = inlineAddTaskRow.querySelector('input[name="name"]');
            if (input) input.focus();
        });
    }

    if (cancelAddTaskBtn && showAddTaskBtn && inlineAddTaskRow) {
        cancelAddTaskBtn.addEventListener('click', () => {
            inlineAddTaskRow.style.display = 'none';
            showAddTaskBtn.style.display = 'flex';
        });
    }

    const addTaskForm = inlineAddTaskRow ? inlineAddTaskRow.querySelector('form') : null;
    if (addTaskForm) {
        const newForm = addTaskForm.cloneNode(true);
        addTaskForm.parentNode.replaceChild(newForm, addTaskForm);

        newForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nameInput = newForm.querySelector('input[name="name"]');
            const prioritySelect = newForm.querySelector('select[name="priority"]');
            const assigneeSelect = newForm.querySelector('select[name="assignee"]');
            const dueInput = newForm.querySelector('input[name="due"]');

            const name = nameInput.value.trim();
            if (name === '') return;

            const priority = prioritySelect.value;
            const assignee = assigneeSelect.value;
            const due = dueInput.value || 'Unscheduled';

            const newTaskId = 'task_' + Math.random().toString(36).substr(2, 9);
            const newTask = {
                id: newTaskId,
                name: name,
                status: 'none',
                priority: priority,
                due: due,
                assignees: assignee ? [assignee] : [],
                subtasks: {}
            };

            projectsState[currentProjId].tasks[newTaskId] = newTask;

            nameInput.value = '';
            prioritySelect.value = 'Normal';
            assigneeSelect.value = '';
            dueInput.value = '';
            inlineAddTaskRow.style.display = 'none';

            renderActiveProject(currentProjId);
        });
    }

    const addSubtaskInlineBtns = document.querySelectorAll('.add-subtask-inline-btn');
    addSubtaskInlineBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const parentId = btn.getAttribute('data-parent-id');
            const container = btn.closest('.inline-add-subtask-row');
            
            const existingForm = container.nextElementSibling;
            if (existingForm && existingForm.classList.contains('inline-subtask-injected-form')) {
                return;
            }

            const template = document.getElementById('inline-add-subtask-template').querySelector('form');
            const clone = template.cloneNode(true);
            clone.classList.add('inline-subtask-injected-form');
            clone.querySelector('input[name="parent_id"]').value = parentId;

            const cancelBtn = clone.querySelector('.cancel-subtask-inline');
            cancelBtn.addEventListener('click', () => {
                clone.remove();
            });

            clone.addEventListener('submit', (e) => {
                e.preventDefault();
                const nameInput = clone.querySelector('input[name="name"]');
                const prioritySelect = clone.querySelector('select[name="priority"]');
                const assigneeSelect = clone.querySelector('select[name="assignee"]');
                const dueInput = clone.querySelector('input[name="due"]');

                const name = nameInput.value.trim();
                if (name === '') return;

                const priority = prioritySelect.value;
                const assignee = assigneeSelect.value;
                const due = dueInput.value || 'Unscheduled';

                const newSubId = 'sub_' + Math.random().toString(36).substr(2, 9);
                const newSub = {
                    id: newSubId,
                    name: name,
                    status: 'none',
                    priority: priority,
                    due: due,
                    assignees: assignee ? [assignee] : []
                };

                if (!projectsState[currentProjId].tasks[parentId].subtasks) {
                    projectsState[currentProjId].tasks[parentId].subtasks = {};
                }
                projectsState[currentProjId].tasks[parentId].subtasks[newSubId] = newSub;

                clone.remove();
                renderActiveProject(currentProjId);
            });

            container.parentNode.insertBefore(clone, container.nextSibling);
            const input = clone.querySelector('input[name="name"]');
            if (input) input.focus();
        });
    });

    const nameDisplays = document.querySelectorAll('.task-name-display');
    nameDisplays.forEach(display => {
        display.addEventListener('click', () => {
            const input = display.nextElementSibling;
            display.style.display = 'none';
            input.style.display = 'block';
            input.focus();
            input.select();
        });
    });

    const nameInputs = document.querySelectorAll('.task-name-input');
    nameInputs.forEach(input => {
        const display = input.previousElementSibling;
        const taskId = display.getAttribute('data-id');
        const taskType = display.getAttribute('data-type');
        const row = input.closest('.task-item-row');
        const parentId = row ? row.getAttribute('data-parent-id') || '' : '';

        const submitName = () => {
            const newValue = input.value.trim();
            if (newValue === '') {
                input.value = display.textContent;
                input.style.display = 'none';
                display.style.display = 'inline';
                return;
            }
            if (newValue !== display.textContent) {
                if (taskType === 'subtask' && parentId !== '') {
                    projectsState[currentProjId].tasks[parentId].subtasks[taskId].name = newValue;
                } else {
                    projectsState[currentProjId].tasks[taskId].name = newValue;
                }
                display.textContent = newValue;
            }
            input.style.display = 'none';
            display.style.display = 'inline';
        };

        input.addEventListener('blur', submitName);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            } else if (e.key === 'Escape') {
                input.value = display.textContent;
                input.style.display = 'none';
                display.style.display = 'inline';
            }
        });
    });

    const assigneeCells = document.querySelectorAll('.task-assignee-cell');
    assigneeCells.forEach(cell => {
        const display = cell.querySelector('.assignee-display');
        const select = cell.querySelector('.assignee-inline-select');
        const taskId = cell.getAttribute('data-id');
        const taskType = cell.getAttribute('data-type');
        const parentId = cell.getAttribute('data-parent-id') || '';

        cell.addEventListener('click', (e) => {
            if (e.target.closest('.assignee-inline-select')) return;
            display.style.display = 'none';
            select.style.display = 'block';
            select.focus();
        });

        select.addEventListener('change', () => {
            const val = select.value;
            const assignees = val ? [val] : [];

            if (taskType === 'subtask' && parentId !== '') {
                projectsState[currentProjId].tasks[parentId].subtasks[taskId].assignees = assignees;
            } else {
                projectsState[currentProjId].tasks[taskId].assignees = assignees;
            }

            display.innerHTML = renderAssigneeAvatars(assignees);
            display.style.display = 'block';
            select.style.display = 'none';
        });

        select.addEventListener('blur', () => {
            display.style.display = 'block';
            select.style.display = 'none';
        });
    });

    const dueCells = document.querySelectorAll('.task-due-cell');
    dueCells.forEach(cell => {
        const display = cell.querySelector('.due-display');
        const input = cell.querySelector('.due-inline-input');
        const taskId = cell.getAttribute('data-id');
        const taskType = cell.getAttribute('data-type');
        const parentId = cell.getAttribute('data-parent-id') || '';

        cell.addEventListener('click', (e) => {
            if (e.target.closest('.due-inline-input')) return;
            display.style.display = 'none';
            input.style.display = 'block';
            input.focus();
        });

        input.addEventListener('change', () => {
            const val = input.value || 'Unscheduled';

            if (taskType === 'subtask' && parentId !== '') {
                projectsState[currentProjId].tasks[parentId].subtasks[taskId].due = val;
            } else {
                projectsState[currentProjId].tasks[taskId].due = val;
            }

            const dueBadge = (val === 'Unscheduled' || val === '')
                ? '<span class="text-muted" style="font-size:11px;">Unscheduled</span>'
                : `<span class="fw-medium" style="font-size:12px;">${val}</span>`;

            display.querySelector('.due-display span, span:last-child').outerHTML = dueBadge;
            display.style.display = 'flex';
            input.style.display = 'none';
        });

        input.addEventListener('blur', () => {
            display.style.display = 'flex';
            input.style.display = 'none';
        });
    });

    const priorityCells = document.querySelectorAll('.task-priority-cell');
    priorityCells.forEach(cell => {
        const display = cell.querySelector('.priority-display');
        const select = cell.querySelector('.priority-inline-select');
        const taskId = cell.getAttribute('data-id');
        const taskType = cell.getAttribute('data-type');
        const parentId = cell.getAttribute('data-parent-id') || '';

        cell.addEventListener('click', (e) => {
            if (e.target.closest('.priority-inline-select')) return;
            display.style.display = 'none';
            select.style.display = 'block';
            select.focus();
        });

        select.addEventListener('change', () => {
            const val = select.value;

            if (taskType === 'subtask' && parentId !== '') {
                projectsState[currentProjId].tasks[parentId].subtasks[taskId].priority = val;
            } else {
                projectsState[currentProjId].tasks[taskId].priority = val;
            }

            let priClass = 'bg-secondary';
            if (val === 'High') priClass = 'bg-danger';
            else if (val === 'Medium' || val === 'Normal') priClass = 'bg-warning text-dark';
            else if (val === 'Low') priClass = 'bg-info text-dark';

            display.textContent = val;
            display.className = `badge ${priClass} rounded-pill px-2 py-1 priority-display`;

            display.style.display = 'inline-block';
            select.style.display = 'none';
        });

        select.addEventListener('blur', () => {
            display.style.display = 'inline-block';
            select.style.display = 'none';
        });
    });

    const statusBtns = document.querySelectorAll('.progress-btn');
    statusBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const taskId = btn.getAttribute('data-id');
            const taskType = btn.getAttribute('data-type');
            const row = btn.closest('.task-item-row');
            const parentId = row ? row.getAttribute('data-parent-id') || '' : '';

            let currentStatus = 'none';
            if (btn.classList.contains('done')) {
                currentStatus = 'done';
            } else if (btn.classList.contains('in-progress')) {
                currentStatus = 'in-progress';
            }

            let newStatus = 'none';
            if (currentStatus === 'none') {
                newStatus = 'in-progress';
            } else if (currentStatus === 'in-progress') {
                newStatus = 'done';
            } else if (currentStatus === 'done') {
                newStatus = 'none';
            }

            if (taskType === 'subtask' && parentId !== '') {
                projectsState[currentProjId].tasks[parentId].subtasks[taskId].status = newStatus;
            } else {
                projectsState[currentProjId].tasks[taskId].status = newStatus;
            }

            btn.className = `progress-btn btn btn-link p-0 border-0 ${newStatus === 'none' ? '' : newStatus}`;
            const icon = btn.querySelector('i');
            if (newStatus === 'in-progress') {
                icon.className = 'bi bi-dash-circle-fill text-primary';
                btn.title = 'Status: In Progress';
            } else if (newStatus === 'done') {
                icon.className = 'bi bi-check-circle-fill text-success';
                btn.title = 'Status: Done';
            } else {
                icon.className = 'bi bi-circle';
                btn.title = 'Status: None';
            }
        });
    });

    const taskDeleteBtns = document.querySelectorAll('.task-item-row .more-actions-btn');
    taskDeleteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const taskId = btn.getAttribute('data-id');
            const taskType = btn.getAttribute('data-type');
            const taskName = btn.getAttribute('data-name');
            const row = btn.closest('.task-item-row');
            const parentId = row ? row.getAttribute('data-parent-id') || '' : '';

            if (confirm(`Are you sure you want to delete the ${taskType} "${taskName}"?`)) {
                if (taskType === 'subtask' && parentId !== '') {
                    delete projectsState[currentProjId].tasks[parentId].subtasks[taskId];
                } else {
                    delete projectsState[currentProjId].tasks[taskId];
                }
                renderActiveProject(currentProjId);
            }
        });
    });

    // Expand / collapse subtask groups
    const expandBtns = document.querySelectorAll('.expand-btn');
    expandBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const targetId = btn.getAttribute('data-target');
            const group = document.getElementById(targetId);
            if (!group) return;
            const isExpanded = btn.getAttribute('aria-expanded') === 'true';
            const icon = btn.querySelector('i');
            if (isExpanded) {
                group.style.display = 'none';
                btn.setAttribute('aria-expanded', 'false');
                if (icon) { icon.style.transform = 'rotate(-90deg)'; }
            } else {
                group.style.display = 'block';
                btn.setAttribute('aria-expanded', 'true');
                if (icon) { icon.style.transform = 'rotate(0deg)'; }
            }
        });
    });

    const activeProjHeaderBtn = document.querySelector('.card-header .more-actions-btn');
    if (activeProjHeaderBtn && activeProjHeaderBtn.getAttribute('data-type') === 'project') {
        const newBtn = activeProjHeaderBtn.cloneNode(true);
        activeProjHeaderBtn.parentNode.replaceChild(newBtn, activeProjHeaderBtn);

        newBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const projId = newBtn.getAttribute('data-id');
            const projName = newBtn.getAttribute('data-name');
            if (confirm(`Are you sure you want to delete the project "${projName}"?`)) {
                delete projectsState[projId];
                
                const remainingKeys = Object.keys(projectsState);
                const nextProjId = remainingKeys.length > 0 ? remainingKeys[0] : '';
                
                renderSidebarProjects();
                
                if (nextProjId) {
                    history.pushState(null, '', `?page=project&project_id=${nextProjId}`);
                    renderActiveProject(nextProjId);
                } else {
                    history.pushState(null, '', `?page=project`);
                    renderActiveProject('');
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const toggleBtns = document.querySelectorAll('.toggle-inline-create-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            if (targetForm) {
                const isHidden = targetForm.style.display === 'none';
                targetForm.style.display = isHidden ? 'block' : 'none';
                if (isHidden) {
                    const input = targetForm.querySelector('input[name="name"]');
                    if (input) input.focus();
                }
            }
        });
    });

    const cancelCreateBtns = document.querySelectorAll('.cancel-inline-create-btn');
    cancelCreateBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            if (targetForm) {
                targetForm.style.display = 'none';
            }
        });
    });

    const inprogressCreateForm = document.querySelector('#inline-create-inprogress form');
    if (inprogressCreateForm) {
        inprogressCreateForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nameInput = inprogressCreateForm.querySelector('input[name="name"]');
            const descInput = inprogressCreateForm.querySelector('input[name="description"]');
            const name = nameInput.value.trim();
            const desc = descInput.value.trim();

            if (name === '') return;

            const newProjId = 'proj_' + Math.random().toString(36).substr(2, 9);
            projectsState[newProjId] = {
                id: newProjId,
                name: name,
                description: desc,
                status: 'inprogress',
                tasks: {}
            };

            nameInput.value = '';
            descInput.value = '';
            document.getElementById('inline-create-inprogress').style.display = 'none';

            renderSidebarProjects();
            history.pushState(null, '', `?page=project&project_id=${newProjId}`);
            renderActiveProject(newProjId);
        });
    }

    const doneCreateForm = document.querySelector('#inline-create-done form');
    if (doneCreateForm) {
        doneCreateForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nameInput = doneCreateForm.querySelector('input[name="name"]');
            const descInput = doneCreateForm.querySelector('input[name="description"]');
            const name = nameInput.value.trim();
            const desc = descInput.value.trim();

            if (name === '') return;

            const newProjId = 'proj_' + Math.random().toString(36).substr(2, 9);
            projectsState[newProjId] = {
                id: newProjId,
                name: name,
                description: desc,
                status: 'done',
                tasks: {}
            };

            nameInput.value = '';
            descInput.value = '';
            document.getElementById('inline-create-done').style.display = 'none';

            renderSidebarProjects();
            history.pushState(null, '', `?page=project&project_id=${newProjId}`);
            renderActiveProject(newProjId);
        });
    }

    renderSidebarProjects();
    if (currentProjId) {
        renderActiveProject(currentProjId);
    }

    window.addEventListener('popstate', () => {
        const params = new URLSearchParams(window.location.search);
        const projId = params.get('project_id') || '';
        renderActiveProject(projId);
    });
});
</script>
@endsection
