<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$id=(int)($_GET['id'] ?? 0);
$stmt=$pdo->prepare('SELECT * FROM lessons WHERE id=:id'); $stmt->execute(['id'=>$id]); $lezione=$stmt->fetch();
if(!$lezione){ flash('danger','Lezione non trovata'); header('Location:/admin/lezioni.php'); exit; }
$pageTitle='Modifica Lezione';
$corsi=$pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();
$docenti=$pdo->query("SELECT id,nome,cognome FROM users WHERE role='docente' AND stato='attivo' ORDER BY cognome,nome")->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/lezione-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
