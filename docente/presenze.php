<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle='Presenze Lezione';
$userId=(int)$_SESSION['user_id'];
$lessonId=(int)($_GET['lesson_id'] ?? 0);

$stmt=$pdo->prepare('SELECT l.*, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id WHERE l.id=:id AND l.docente_id=:uid');
$stmt->execute(['id'=>$lessonId,'uid'=>$userId]);
$lesson=$stmt->fetch();

$lessonsStmt=$pdo->prepare('SELECT id, titolo, data_lezione FROM lessons WHERE docente_id=:uid ORDER BY data_lezione DESC LIMIT 50');
$lessonsStmt->execute(['uid'=>$userId]);
$lessonOptions=$lessonsStmt->fetchAll();

$students=[];
if($lesson){
  $st=$pdo->prepare('SELECT u.id AS student_id, sp.nome, sp.cognome, a.presente
                     FROM enrollments e
                     JOIN users u ON u.id=e.student_id
                     JOIN student_profiles sp ON sp.user_id=u.id
                     LEFT JOIN attendance a ON a.lesson_id=:lid AND a.student_id=u.id
                     WHERE e.course_id=:cid AND e.stato="attivo"
                     ORDER BY sp.cognome, sp.nome');
  $st->execute(['lid'=>$lessonId,'cid'=>$lesson['course_id']]);
  $students=$st->fetchAll();
}

require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body"><form class="row g-2" method="get"><div class="col-md-8"><select class="form-select" name="lesson_id" required><option value="">Seleziona lezione</option><?php foreach($lessonOptions as $o): ?><option value="<?= (int)$o['id'] ?>" <?= $lessonId===(int)$o['id']?'selected':'' ?>><?= e($o['data_lezione'].' - '.$o['titolo']) ?></option><?php endforeach; ?></select></div><div class="col-md-4"><button class="btn btn-primary">Carica corsisti</button></div></form></div></div>
<?php if($lesson): ?>
<form action="/actions/presenza-save.php" method="post" class="card shadow-sm"><input type="hidden" name="lesson_id" value="<?= (int)$lessonId ?>"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corsista</th><th>Presente</th></tr></thead><tbody><?php foreach($students as $s): ?><tr><td><?= e($s['cognome'].' '.$s['nome']) ?></td><td><input type="checkbox" name="presenti[]" value="<?= (int)$s['student_id'] ?>" <?= (int)$s['presente']===1?'checked':'' ?>></td></tr><?php endforeach; ?></tbody></table></div><div class="card-footer"><button class="btn btn-success">Salva presenze</button></div></form>
<?php endif; ?>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
