<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Presenze';
$lessonId = (int)($_GET['lesson_id'] ?? 0);

$lessonOptions = $pdo->query('SELECT l.id, l.data_lezione, l.titolo, c.titolo AS corso_titolo
                              FROM lessons l JOIN courses c ON c.id=l.course_id
                              ORDER BY l.data_lezione DESC LIMIT 200')->fetchAll();

$lesson = null;
$students = [];
if ($lessonId > 0) {
    $stmt = $pdo->prepare('SELECT l.*, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id WHERE l.id=:id');
    $stmt->execute(['id' => $lessonId]);
    $lesson = $stmt->fetch();

    if ($lesson) {
        $stmt = $pdo->prepare('SELECT u.id AS student_id, sp.nome, sp.cognome, a.presente
                               FROM enrollments e
                               JOIN users u ON u.id=e.student_id
                               JOIN student_profiles sp ON sp.user_id=u.id
                               LEFT JOIN attendance a ON a.lesson_id=:lesson_id AND a.student_id=u.id
                               WHERE e.course_id=:course_id AND e.stato="attivo"
                               ORDER BY sp.cognome, sp.nome');
        $stmt->execute(['lesson_id' => $lessonId, 'course_id' => $lesson['course_id']]);
        $students = $stmt->fetchAll();
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<div class="card shadow-sm mb-3"><div class="card-body">
<form method="get" class="row g-2">
<div class="col-md-9"><select class="form-select" name="lesson_id" required><option value="">Seleziona lezione</option><?php foreach($lessonOptions as $o): ?><option value="<?= (int)$o['id'] ?>" <?= $lessonId===(int)$o['id']?'selected':'' ?>><?= e($o['data_lezione'].' - '.$o['corso_titolo'].' - '.$o['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><button class="btn btn-primary w-100">Carica presenze</button></div>
</form>
</div></div>

<?php if($lesson): ?>
<form action="/actions/presenza-save.php" method="post" class="card shadow-sm">
<input type="hidden" name="lesson_id" value="<?= (int)$lessonId ?>">
<div class="card-header">Presenze lezione: <?= e($lesson['corso_titolo'].' - '.$lesson['titolo']) ?></div>
<div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corsista</th><th>Presente</th></tr></thead><tbody>
<?php foreach($students as $s): ?><tr><td><?= e($s['cognome'].' '.$s['nome']) ?></td><td><input type="checkbox" name="presenti[]" value="<?= (int)$s['student_id'] ?>" <?= (int)$s['presente']===1?'checked':'' ?>></td></tr><?php endforeach; ?>
</tbody></table></div>
<div class="card-footer"><button class="btn btn-success">Salva presenze</button></div>
</form>
<?php endif; ?>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
