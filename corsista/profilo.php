<?php
require_once __DIR__ . '/../config/config.php';
$rolePath = explode('/', str_replace('\\', '/', __FILE__))[count(explode('/', str_replace('\\', '/', __FILE__))) - 2] ?? '';
if ($rolePath === 'admin') {
    requireRole(['admin']);
} elseif ($rolePath === 'docente') {
    requireRole(['docente']);
} else {
    requireRole(['corsista']);
}
$pageTitle = basename(__FILE__, '.php');
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php
if ($rolePath === 'admin') require __DIR__ . '/../includes/sidebar-admin.php';
elseif ($rolePath === 'docente') require __DIR__ . '/../includes/sidebar-docente.php';
else require __DIR__ . '/../includes/sidebar-corsista.php';
?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><div class="card"><div class="card-body">Pagina operativa in costruzione: <?= e($pageTitle) ?></div></div></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php';
