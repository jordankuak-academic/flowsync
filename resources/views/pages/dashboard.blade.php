@extends('layouts.hsxxx-layout')

@section('page-title', 'Dashboard')

@section('content')
<?php
// Helper to render compact task card optimized for dashboard columns
if (!function_exists('renderDashboardTaskCard')) {
    function renderDashboardTaskCard($id, $name, $status, $column) {
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

        return sprintf(
            '<div class="dashboard-task-card d-flex align-items-center p-2 mb-2 bg-white rounded-2 shadow-sm" id="%s" data-type="dashboard-task" data-column="%s">
                <div class="d-flex align-items-center flex-grow-1 overflow-hidden">
                    <div class="progress-group d-flex align-items-center me-2" data-status="%s">
                        <button class="progress-btn btn btn-link p-0 border-0 %s" 
                                data-id="%s" 
                                data-type="dashboard-task"
                                data-tooltip="true" 
                                data-tooltip-placement="top" 
                                title="%s">
                            <i class="bi %s fs-6"></i>
                        </button>
                    </div>
                    <span class="text-truncate fw-medium text-dark small">%s</span>
                </div>
                <button class="btn btn-link btn-sm p-0 more-actions-btn" data-id="%s" data-type="dashboard-task" data-column="%s" data-name="%s">
                    <i class="bi bi-three-dots-vertical text-secondary"></i>
                </button>
             </div>',
            htmlspecialchars($id),
            htmlspecialchars($column),
            htmlspecialchars($status),
            $statusClass,
            htmlspecialchars($id),
            $statusTitle,
            $statusIcon,
            htmlspecialchars($name),
            htmlspecialchars($id),
            htmlspecialchars($column),
            htmlspecialchars($name)
        );
    }
}
?>

<div class="mb-4">
    <h1 class="h2 fw-bold text-dark mb-1">Task List</h1>
    <p class="text-muted mb-0">Review and manage status tracking categories across your workspace</p>
</div>

<div class="row g-4">
    <?php
    // Fake data — replace with API calls when backend is ready
    $dashboardTasks = [
        'today' => [
            ['id' => 'dt_1', 'name' => 'Design component library', 'status' => 'in-progress'],
            ['id' => 'dt_2', 'name' => 'Define color tokens',       'status' => 'done'],
        ],
        'upcoming' => [
            ['id' => 'dt_3', 'name' => 'Implement dashboard page',  'status' => 'none'],
            ['id' => 'dt_4', 'name' => 'Define API endpoints',       'status' => 'in-progress'],
        ],
        'overdue' => [
            ['id' => 'dt_5', 'name' => 'Set up project scaffolding','status' => 'done'],
        ],
        'unscheduled' => [
            ['id' => 'dt_6', 'name' => 'Build button variants',     'status' => 'none'],
            ['id' => 'dt_7', 'name' => 'Write unit tests',          'status' => 'none'],
        ],
    ];

    $columns = [
        'today'       => ['title' => 'Today',       'icon' => 'bi-calendar-event'],
        'upcoming'    => ['title' => 'Upcoming',    'icon' => 'bi-calendar-week'],
        'overdue'     => ['title' => 'Overdue',     'icon' => 'bi-exclamation-circle', 'text' => 'text-danger'],
        'unscheduled' => ['title' => 'Unscheduled', 'icon' => 'bi-question-circle'],
    ];

    foreach ($columns as $key => $info):
        $tasks = $dashboardTasks[$key] ?? [];
        $count = count($tasks);
    ?>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm bg-light-blue" style="min-height: 400px; background-color: #f0f7ff;">
            <div class="card-header border-0 bg-transparent d-flex align-items-center justify-content-between pt-3">
                <div class="d-flex align-items-center">
                    <i class="bi <?php echo $info['icon']; ?> me-2 <?php echo $info['text'] ?? 'text-primary'; ?>"></i>
                    <span class="fw-bold text-dark"><?php echo $info['title']; ?></span>
                </div>
                <span class="badge bg-white text-dark border rounded-pill px-2"><?php echo $count; ?></span>
            </div>
            <div class="card-body pt-2">
                <?php if (empty($tasks)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check2-circle fs-1 text-muted opacity-25"></i>
                        <p class="text-muted small mt-2">All caught up!</p>
                    </div>
                <?php else: ?>
                    <?php
                    foreach ($tasks as $task) {
                        echo renderDashboardTaskCard($task['id'], $task['name'], $task['status'] ?? 'none', $key);
                    }
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
@endsection


