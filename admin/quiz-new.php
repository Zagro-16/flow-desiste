<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Nuovo Quiz';
$courses = $pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<form action="/actions/quiz-save.php" method="post" class="card shadow-sm">
<div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Corso</label><select class="form-select" name="course_id" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Titolo quiz</label><input class="form-control" name="titolo" required></div>
<div class="col-md-4"><label class="form-label">Punteggio massimo</label><input type="number" min="1" class="form-control" name="punteggio_massimo" value="100" required></div>
<div class="col-12"><label class="form-label">Domande (una per riga: domanda|opzione1;opzione2;opzione3|risposta_corretta)</label><textarea class="form-control" rows="8" name="questions_blob" placeholder="Esempio: Quale linguaggio usiamo?|PHP;Java;Ruby|PHP" required></textarea></div>
</div></div>
<div class="card-footer d-flex justify-content-between"><a href="/admin/quiz.php" class="btn btn-outline-secondary">Annulla</a><button class="btn btn-primary">Salva quiz</button></div>
</form>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
