<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM courses WHERE id=:id'); $stmt->execute(['id'=>$id]); $corso = $stmt->fetch();
if(!$corso){ flash('danger','Corso non trovato'); header('Location:/admin/corsi.php'); exit; }
$pageTitle = 'Modifica Corso';
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/corso-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
