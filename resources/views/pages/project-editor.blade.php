@extends('layouts.hsxxx-layout')

@section('page-title', 'Project Editor')

@section('content')
<?php
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'create';
$projectId = isset($_GET['project_id']) ? $_GET['project_id'] : '';

$mode = ($mode === 'edit' && $projectId !== '') ? 'edit' : 'create';
$title = ($mode === 'edit' ? 'Edit Project' : 'Create Project');

$nameVal = '';
$descVal = '';
$statusVal = 'inprogress';

if ($mode === 'edit' && isset($_SESSION['projects'][$projectId])) {
    $p = $_SESSION['projects'][$projectId];
    $nameVal = $p['name'] ?? '';
    $descVal = $p['description'] ?? '';
    $statusVal = ($p['status'] === 'done' || (isset($p['status']) && $p['status'] === 'done')) ? 'done' : 'inprogress';
}

$backHref = '?page=project' . ($projectId ? '&project_id=' . urlencode($projectId) : '');
?>

<div class="row g-4 justify-content-center">
    <!-- Left Sidebar: Create/Edit Form -->
    <div class="col-12 col-lg-5 col-xl-4">
        <div class="card border-0 shadow-sm h-100 rounded-4">
            <div class="card-body p-4 p-xl-5">
                <div class="mb-4">
                    <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($title); ?></h2>
                    <p class="text-muted small">Enter the details for your project workspace.</p>
                </div>

                <form method="POST" action="">
                    @csrf
                    <input type="hidden" name="action" value="save_project" />
                    <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>" />
                    <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($projectId); ?>" />

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="project-name">PROJECT NAME</label>
                        <input type="text" class="form-control form-control-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="project-name" name="name" value="<?php echo htmlspecialchars($nameVal); ?>" placeholder="Enter project name..." required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="project-desc">DESCRIPTION</label>
                        <textarea class="form-control border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="project-desc" name="description" rows="5" placeholder="Enter description..."><?php echo htmlspecialchars($descVal); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small" for="project-status">STATUS</label>
                        <select class="form-select form-select-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="project-status" name="status">
                            <option value="inprogress" <?php echo $statusVal === 'inprogress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="done" <?php echo $statusVal === 'done' ? 'selected' : ''; ?>>Done</option>
                        </select>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?php echo htmlspecialchars($backHref); ?>" class="btn btn-light btn-lg flex-grow-1 fw-bold rounded-3 py-3" style="font-size: 14px;">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold rounded-3 py-3 shadow-sm" style="font-size: 14px;">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Content: Active Project Preview -->
    <div class="col-12 col-lg-7 col-xl-6 d-none d-lg-block">
        <div class="card border-0 shadow-sm h-100 rounded-4 bg-white">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
                    <h4 class="fw-bold text-dark mb-0"><?php echo $mode === 'edit' ? htmlspecialchars($nameVal) : 'Project Preview'; ?></h4>
                    <div class="btn btn-light btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-three-dots text-secondary"></i>
                    </div>
                </div>

                <div class="opacity-25" style="pointer-events: none;">
                    <div class="d-flex align-items-center p-3 mb-3 bg-light rounded-3 border">
                        <div class="rounded-2 border-2 border-secondary-subtle bg-white me-3" style="width: 24px; height: 24px; border: 2px solid #dee2e6;"></div>
                        <div class="bg-secondary-subtle rounded-pill w-50" style="height: 12px;"></div>
                    </div>
                    <div class="d-flex align-items-center p-3 mb-3 bg-light rounded-3 border ps-5 border-start border-4 border-primary">
                        <div class="rounded-2 border-2 border-secondary-subtle bg-white me-3" style="width: 24px; height: 24px; border: 2px solid #dee2e6;"></div>
                        <div class="bg-secondary-subtle rounded-pill w-25" style="height: 12px;"></div>
                    </div>
                    <div class="d-flex align-items-center p-3 mb-4 bg-light rounded-3 border">
                        <div class="rounded-2 border-2 border-secondary-subtle bg-white me-3" style="width: 24px; height: 24px; border: 2px solid #dee2e6;"></div>
                        <div class="bg-secondary-subtle rounded-pill w-40" style="height: 12px;"></div>
                    </div>
                    
                    <div class="btn btn-outline-secondary w-100 py-3 border-dashed d-flex align-items-center justify-content-center bg-transparent" style="border-style: dashed;">
                        <div class="bg-secondary-subtle text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <span class="fw-bold text-secondary-subtle">Create Task</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-5 text-center">
                    <p class="text-muted small">The preview shows how your project structure will appear in the workspace.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
