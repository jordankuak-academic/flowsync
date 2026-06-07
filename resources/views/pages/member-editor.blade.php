@extends('layouts.hsxxx-layout')

@section('page-title', 'Member Editor')

@section('content')
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['staff'])) {
    $_SESSION['staff'] = [
        ['id' => 'staff_1', 'name' => 'Alice Tan',   'role' => 'Project Manager'],
        ['id' => 'staff_2', 'name' => 'Bob Lee',      'role' => 'UI/UX Designer'],
        ['id' => 'staff_3', 'name' => 'Carol Ng',     'role' => 'Web Developer'],
        ['id' => 'staff_4', 'name' => 'David Lim',    'role' => 'QA Engineer'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_member') {
    $mode = $_POST['mode'] ?? 'create';
    $memberId = $_POST['member_id'] ?? '';
    $name = $_POST['name'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($mode === 'edit' && $memberId !== '') {
        foreach ($_SESSION['staff'] as &$m) {
            if ($m['id'] === $memberId) {
                $m['name'] = $name;
                $m['role'] = $role;
                break;
            }
        }
    } else {
        $newId = 'staff_' . uniqid();
        $_SESSION['staff'][] = [
            'id' => $newId,
            'name' => $name,
            'role' => $role
        ];
    }

    header('Location: /team');
    exit;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'create';
$memberId = isset($_GET['member_id']) ? $_GET['member_id'] : '';

$mode = ($mode === 'edit' && $memberId !== '') ? 'edit' : 'create';
$title = ($mode === 'edit' ? 'Edit Member' : 'Add New Member');

$nameVal = '';
$roleVal = '';

if ($mode === 'edit') {
    $members = $_SESSION['staff'];
    foreach ($members as $m) {
        if ($m['id'] === $memberId) {
            $nameVal = $m['name'] ?? '';
            $roleVal = $m['role'] ?? '';
            break;
        }
    }
}

$backHref = '?page=team';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-xl-5">
                <div class="mb-4 text-center">
                    <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($title); ?></h2>
                    <p class="text-muted small">Update the information for your team member.</p>
                </div>

                <form method="POST" action="">
                    @csrf
                    <input type="hidden" name="action" value="save_member" />
                    <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>" />
                    <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($memberId); ?>" />

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small" for="member-name">FULL NAME</label>
                        <input type="text" class="form-control form-control-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="member-name" name="name" value="<?php echo htmlspecialchars($nameVal); ?>" placeholder="e.g. Robert Johnson" required />
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small" for="member-role">ROLE</label>
                        <select class="form-select form-select-lg border-light bg-light rounded-3 px-3 py-2 fs-6 fw-medium" id="member-role" name="role">
                            <option value="Project Manager" <?php echo $roleVal === 'Project Manager' ? 'selected' : ''; ?>>Project Manager</option>
                            <option value="Web Developer" <?php echo $roleVal === 'Web Developer' ? 'selected' : ''; ?>>Web Developer</option>
                            <option value="UI/UX Designer" <?php echo $roleVal === 'UI/UX Designer' ? 'selected' : ''; ?>>UI/UX Designer</option>
                            <option value="QA Engineer" <?php echo $roleVal === 'QA Engineer' ? 'selected' : ''; ?>>QA Engineer</option>
                        </select>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?php echo htmlspecialchars($backHref); ?>" class="btn btn-light btn-lg flex-grow-1 fw-bold rounded-3 py-3" style="font-size: 14px;">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1 fw-bold rounded-3 py-3 shadow-sm" style="font-size: 14px;">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
