<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle='Quiz corsi assegnati';
$userId=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT q.*, c.titolo AS corso_titolo FROM quizzes q JOIN courses c ON c.id=q.course_id WHERE EXISTS (SELECT 1 FROM lessons l WHERE l.course_id=q.course_id AND l.docente_id=:uid) ORDER BY q.id DESC');
$stmt->execute(['uid'=>$userId]);
$rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corso</th><th>Quiz</th><th>Punteggio max</th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="3" class="text-center text-muted py-4">Nessun quiz disponibile.</td></tr><?php endif; ?><?php foreach($rows as $r): ?><tr><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['titolo']) ?></td><td><?= (int)$r['punteggio_massimo'] ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
