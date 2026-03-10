<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['corsista']);
$pageTitle='Quiz'; $uid=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT q.*, c.titolo AS corso_titolo,
(SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.id AND qa.student_id=:uid) AS tentativi
FROM quizzes q JOIN courses c ON c.id=q.course_id JOIN enrollments e ON e.course_id=q.course_id
WHERE e.student_id=:uid AND e.stato="attivo" ORDER BY q.id DESC');
$stmt->execute(['uid'=>$uid]); $rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-corsista.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corso</th><th>Quiz</th><th>Punteggio max</th><th>Tentativi</th><th></th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessun quiz disponibile.</td></tr><?php endif; ?><?php foreach($rows as $r): ?><tr><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['titolo']) ?></td><td><?= (int)$r['punteggio_massimo'] ?></td><td><?= (int)$r['tentativi'] ?></td><td><form method="post" action="/actions/quiz-submit.php"><input type="hidden" name="quiz_id" value="<?= (int)$r['id'] ?>"><button class="btn btn-sm btn-outline-primary">Registra tentativo demo</button></form></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
