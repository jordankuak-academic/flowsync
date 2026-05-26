@extends('layouts.hsxxx-layout')

@section('page-title', 'Team Management')

@section('content')
<?php

/**
 * FlowSync Team Page using project CSS utilities.
 */

// 1. Logic & Data Handling
$selectedProjId = isset($_GET['project_id']) ? $_GET['project_id'] : 'proj_1';
$activeProject = null;

if (!empty($_SESSION['projects'])) {
    if (isset($_SESSION['projects'][$selectedProjId])) {
        $activeProject = $_SESSION['projects'][$selectedProjId];
    } else {
        $activeProject = reset($_SESSION['projects']);
        $selectedProjId = $activeProject['id'];
    }
}

$inprogressProjects = [];
$doneProjects = [];
foreach ($_SESSION['projects'] ?? [] as $project) {
    if (($project['status'] ?? 'inprogress') === 'done') {
        $doneProjects[] = $project;
    } else {
        $inprogressProjects[] = $project;
    }
}

/**
 * Helper to render a task row for the team overview panel.
 */
function renderTeamProjectTask($task, $isSubtask = false)
{
    $status = $task['status'] ?? 'none';
    $id = htmlspecialchars($task['id']);
    $name = htmlspecialchars($task['name']);
    $progress = $task['progress'] ?? '0/0';
    $type = $isSubtask ? 'subtask' : 'task';

    $statusClass = '';
    $statusIcon = 'bi-circle';
    $statusTitle = 'Status: None';

    if ($status === 'in-progress') {
        $statusClass = 'in-progress';
        $statusIcon = 'bi-dash-circle-fill text-primary';
        $statusTitle = 'Status: In Progress';
    } elseif ($status === 'done') {
        $statusClass = 'done';
        $statusIcon = 'bi-check-circle-fill text-success';
        $statusTitle = 'Status: Done';
    }

    $rowClass = $isSubtask ? 'subtask-row' : '';

    return sprintf(
        '<div class="team-task-row %s">
            <div class="team-task-main">
                <div class="progress-group d-flex align-items-center" data-status="%s">
                <button class="progress-btn btn btn-link p-0 border-0 %s" 
                        data-id="%s" 
                        data-type="%s"
                        data-tooltip="true" 
                        data-tooltip-placement="top" 
                        title="%s">
                    <i class="bi %s fs-6"></i>
                </button>
            </div>
                <span class="team-task-name">%s</span>
            </div>
            <div class="team-task-meta">
                <span class="badge bg-light text-muted border rounded-pill px-2">%s</span>
            <button class="btn btn-link btn-sm p-0 more-actions-btn" data-id="%s" data-type="%s" data-name="%s">
                <i class="bi bi-three-dots-vertical text-secondary"></i>
            </button>
            </div>
        </div>',
        $rowClass,
        $status,
        $statusClass,
        $id,
        $type,
        $statusTitle,
        $statusIcon,
        $name,
        htmlspecialchars($progress),
        $id,
        $type,
        $name
    );
}

function renderTeamProjectList($projects, $selectedProjId, $statusClass = '')
{
    if (empty($projects)) {
        return '<p class="text-muted text-center small py-2 mb-0">No projects</p>';
    }

    $html = '';
    foreach ($projects as $project) {
        $isActive = $selectedProjId === $project['id'];
        $itemClass = trim('project-list-item team-project-list-item ' . ($isActive ? 'active ' . $statusClass : ''));
        $linkClass = 'text-decoration-none flex-grow-1 text-truncate fw-medium ' . ($isActive ? 'text-white' : 'text-dark');
        $taskCount = count($project['tasks'] ?? []);
        $activeBadgeColor = $statusClass === 'done' ? 'text-success' : 'text-primary';

        $html .= sprintf(
            '<div class="%s">
                <a href="?page=team&project_id=%s" class="%s">%s</a>
                <span class="badge %s rounded-pill px-2">%d</span>
            </div>',
            $itemClass,
            urlencode($project['id']),
            $linkClass,
            htmlspecialchars($project['name']),
            $isActive ? 'bg-white ' . $activeBadgeColor : 'bg-light text-muted border',
            $taskCount
        );
    }

    return $html;
}
?>

<div class="row g-4">
    <!-- Left Panel: Project Overview -->
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm h-100 rounded-4 p-3 team-project-overview">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-2 px-3 pb-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Project Overview</h5>
                    <p class="text-muted small mb-0"><?php echo $activeProject ? htmlspecialchars($activeProject['name']) : 'No project selected'; ?></p>
                </div>
                <div class="dropdown d-flex align-items-center">
                    <button class="btn btn-light btn-sm border-light dropdown-toggle d-flex align-items-center justify-content-center"
                        type="button"
                        data-dropdown-toggle="true"
                        aria-expanded="false"
                        style="width: 38px; border-radius: 8px;">
                        <i class="bi bi-funnel text-secondary"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($_SESSION['projects'] as $p): ?>
                            <li>
                                <a class="dropdown-item <?php echo $p['id'] === $selectedProjId ? 'active' : ''; ?>"
                                    href="?page=team&project_id=<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="card-body px-3 pt-2 overflow-auto" style="max-height: 680px;">
                <div class="team-project-list-panel mb-3">
                    <div class="team-project-section">
                        <div class="team-project-section-title">
                            <span class="status-dot bg-primary"></span>
                            <span>In Progress</span>
                            <span class="badge bg-light text-primary border rounded-pill px-2"><?php echo count($inprogressProjects); ?></span>
                        </div>
                        <?php echo renderTeamProjectList($inprogressProjects, $selectedProjId); ?>
                    </div>

                    <div class="team-project-section">
                        <div class="team-project-section-title done">
                            <span class="status-dot bg-success"></span>
                            <span>Completed</span>
                            <span class="badge bg-light text-success border rounded-pill px-2"><?php echo count($doneProjects); ?></span>
                        </div>
                        <?php echo renderTeamProjectList($doneProjects, $selectedProjId, 'done'); ?>
                    </div>
                </div>

                <div class="team-task-overview-title">
                    <span class="fw-bold text-dark">Tasks</span>
                    <?php if ($activeProject): ?>
                        <span class="badge bg-light text-muted border rounded-pill px-2"><?php echo count($activeProject['tasks'] ?? []); ?></span>
                    <?php endif; ?>
                </div>

                <?php
                if ($activeProject && !empty($activeProject['tasks'])) {
                    foreach ($activeProject['tasks'] as $task) {
                        echo renderTeamProjectTask($task);
                        if (!empty($task['subtasks'])) {
                            foreach ($task['subtasks'] as $sub) {
                                echo renderTeamProjectTask($sub, true);
                            }
                        }
                    }
                } else {
                    echo '<div class="text-center py-5"><i class="bi bi-journal-x fs-1 text-muted opacity-25"></i><p class="text-muted small mt-2">No tasks available for this project.</p></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Right Panel: Department Member -->
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm h-100 rounded-4 p-3 team-members-panel">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-2 px-3">
                <h5 class="fw-bold text-dark mb-0">Department Members</h5>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm border-light bg-light fw-bold" style="font-size: 12px; border-radius: 8px;">
                        <option>All Roles</option>
                        <option>Project Manager</option>
                        <option>Web Developer</option>
                        <option>UI/UX Designer</option>
                    </select>
                    <a href="?page=member-editor&mode=create" class="btn btn-primary btn-sm rounded-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;" title="Add User" aria-label="Add User"><i class="bi bi-person-plus-fill"></i></a>
                </div>
            </div>

            <div class="card-body px-3 pt-3">
                <?php
                $members = $_SESSION['staff'] ?? [];
                $avatarColors = [
                    'linear-gradient(135deg, #2563EB 0%, #3B82F6 100%)',
                    'linear-gradient(135deg, #10B981 0%, #34D399 100%)',
                    'linear-gradient(135deg, #EF4444 0%, #F87171 100%)',
                    'linear-gradient(135deg, #8B5CF6 0%, #A78BFA 100%)',
                    'linear-gradient(135deg, #06B6D4 0%, #22D3EE 100%)',
                    'linear-gradient(135deg, #6366F1 0%, #818CF8 100%)',
                    'linear-gradient(135deg, #14B8A6 0%, #2DD4BF 100%)'
                ];

                foreach ($members as $member):
                    $initials = strtoupper(substr($member['name'], 0, 1) . substr(strrchr($member['name'], ' '), 1, 1));
                    if (strlen($initials) < 2) $initials = strtoupper(substr($member['name'], 0, 2));

                    $colorIndex = abs(crc32($member['name'])) % count($avatarColors);
                    $selectedColor = $avatarColors[$colorIndex];
                ?>
                    <div class="member-row bg-white border rounded-3 shadow-sm hover-shadow transition">
                        <div class="member-avatar">
                            <div class="member-avatar-circle rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="background: <?php echo $selectedColor; ?>;">
                                <?php echo $initials; ?>
                            </div>
                        </div>
                        <div class="member-info">
                            <h6 class="member-name"><?php echo htmlspecialchars($member['name']); ?></h6>
                            <span class="member-role badge bg-light text-primary border rounded-pill px-2 py-1">
                                #<?php echo htmlspecialchars($member['role']); ?>
                            </span>
                        </div>
                        <div class="member-actions dropdown">
                            <button class="btn btn-link btn-sm p-0 more-actions-btn" data-id="<?php echo $member['id'] ?? ''; ?>" data-type="member" data-name="<?php echo htmlspecialchars($member['name']); ?>">
                                <i class="bi bi-three-dots-vertical text-secondary fs-5"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
@endsection

