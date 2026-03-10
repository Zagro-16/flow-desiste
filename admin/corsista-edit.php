<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash('danger', 'ID corsista non valido.');
    header('Location: /admin/corsisti.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM student_profiles WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$corsista = $stmt->fetch();
if (!$corsista) {
    flash('danger', 'Corsista non trovato.');
    header('Location: /admin/corsisti.php');
    exit;
}

$pageTitle = 'Modifica Corsista';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4">
<?php require __DIR__ . '/../includes/page-header.php'; ?>
<?php require __DIR__ . '/../includes/alerts.php'; ?>
<?php require __DIR__ . '/partials/corsista-form.php'; ?>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
