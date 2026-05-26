<?php

/**
 * FlowSync front controller.
 * Keeps app startup, routing, and layout composition in one predictable place.
 */
require_once __DIR__ . '/includes/app-data.php';
require_once __DIR__ . '/includes/app-actions.php';
require_once __DIR__ . '/includes/app-routing.php';
require_once __DIR__ . '/includes/app-assets.php';

require_once __DIR__ . '/components/button.php';
require_once __DIR__ . '/components/input.php';
require_once __DIR__ . '/components/task-row.php';

flowSyncStartSession();
flowSyncInitializeData();
flowSyncHandlePost();

[$page, $pageFile] = flowSyncResolvePage(__DIR__);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlowSync</title>
<?php flowSyncRenderStylesheets(); ?>
</head>

<body class="<?php echo $page === 'login' ? 'page-login bg-light' : 'bg-light'; ?>">
    <?php if ($page === 'login'): ?>
        <main class="login-split-container">
            <?php include $pageFile; ?>
        </main>
    <?php else: ?>
        <div class="app-container d-flex flex-column min-vh-100">
            <?php include __DIR__ . '/includes/header.php'; ?>
            <div class="main-layout d-flex flex-grow-1">
                <?php include __DIR__ . '/includes/sidebar.php'; ?>
                <main class="content-viewport flex-grow-1 p-4 fade-in">
                    <?php include $pageFile; ?>
                </main>
            </div>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/includes/delete-modal.php'; ?>

    <script src="assets/js/app.js"></script>
</body>

</html>
