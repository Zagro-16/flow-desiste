<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Quiz';

$rows = $pdo->query('SELECT q.*, c.titolo AS corso_titolo,
                     (SELECT COUNT(*) FROM quiz_questions qq WHERE qq.quiz_id=q.id) AS domande,
                     (SELECT COUNT(*) FROM quiz_attempts qa WHERE qa.quiz_id=q.id) AS tentativi
                     FROM quizzes q JOIN courses c ON c.id=q.course_id ORDER BY q.id DESC')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="d-flex justify-content-end mb-3"><a class="btn btn-success" href="/admin/quiz-new.php">Nuovo Quiz</a></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corso</th><th>Titolo quiz</th><th>Punteggio max</th><th>Domande</th><th>Tentativi</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessun quiz creato.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['titolo']) ?></td><td><?= (int)$r['punteggio_massimo'] ?></td><td><?= (int)$r['domande'] ?></td><td><?= (int)$r['tentativi'] ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
