<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['corsista']);
$pageTitle='I miei corsi'; $uid=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT c.*, e.stato AS stato_iscrizione, e.creato_il AS data_iscrizione FROM enrollments e JOIN courses c ON c.id=e.course_id WHERE e.student_id=:uid ORDER BY e.id DESC');
$stmt->execute(['uid'=>$uid]); $rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-corsista.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Codice</th><th>Titolo</th><th>Periodo</th><th>Stato corso</th><th>Iscrizione</th><th>Data iscrizione</th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="6" class="text-center text-muted py-4">Nessun corso assegnato.</td></tr><?php endif; ?><?php foreach($rows as $r): ?><tr><td><?= e($r['codice']) ?></td><td><?= e($r['titolo']) ?></td><td><?= e($r['data_inizio']) ?> → <?= e($r['data_fine']) ?></td><td><?= e($r['stato']) ?></td><td><?= e($r['stato_iscrizione']) ?></td><td><?= e($r['data_iscrizione']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
