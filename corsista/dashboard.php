<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['corsista']);
$pageTitle='Dashboard Corsista';
$userId=(int)$_SESSION['user_id'];

$stmt=$pdo->prepare('SELECT c.id,c.titolo,c.codice,e.stato FROM enrollments e JOIN courses c ON c.id=e.course_id WHERE e.student_id=:uid ORDER BY e.id DESC LIMIT 1');
$stmt->execute(['uid'=>$userId]);
$course=$stmt->fetch();

$stmt=$pdo->prepare('SELECT COUNT(*) FROM lessons l JOIN enrollments e ON e.course_id=l.course_id WHERE e.student_id=:uid AND l.data_lezione>=CURDATE() AND e.stato="attivo"');
$stmt->execute(['uid'=>$userId]);
$futureLessons=(int)$stmt->fetchColumn();

$stmt=$pdo->prepare('SELECT COUNT(*) FROM materials m JOIN enrollments e ON e.course_id=m.course_id WHERE e.student_id=:uid AND e.stato="attivo"');
$stmt->execute(['uid'=>$userId]);
$materials=(int)$stmt->fetchColumn();

$stmt=$pdo->prepare('SELECT COUNT(*) FROM quizzes q JOIN enrollments e ON e.course_id=q.course_id WHERE e.student_id=:uid AND e.stato="attivo"');
$stmt->execute(['uid'=>$userId]);
$quizzes=(int)$stmt->fetchColumn();

$stmt=$pdo->prepare('SELECT COUNT(*) FROM certificates WHERE student_id=:uid');
$stmt->execute(['uid'=>$userId]);
$certs=(int)$stmt->fetchColumn();

$stmt=$pdo->prepare('SELECT l.*, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id JOIN enrollments e ON e.course_id=l.course_id WHERE e.student_id=:uid AND e.stato="attivo" AND l.data_lezione>=CURDATE() ORDER BY l.data_lezione, l.ora_inizio LIMIT 8');
$stmt->execute(['uid'=>$userId]);
$nextLessons=$stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-corsista.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Corso attivo</div><div class="h5 mb-0"><?= e($course['titolo'] ?? 'Nessuno') ?></div></div></div></div>
<div class="col-md-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Lezioni future</div><div class="h4 mb-0"><?= $futureLessons ?></div></div></div></div>
<div class="col-md-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Materiali</div><div class="h4 mb-0"><?= $materials ?></div></div></div></div>
<div class="col-md-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Quiz</div><div class="h4 mb-0"><?= $quizzes ?></div></div></div></div>
<div class="col-md-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Attestati</div><div class="h4 mb-0"><?= $certs ?></div></div></div></div></div>

<div class="card shadow-sm"><div class="card-header">Prossime lezioni</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Data</th><th>Orario</th><th>Corso</th><th>Titolo</th><th>Meet</th></tr></thead><tbody><?php if(!$nextLessons): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessuna lezione pianificata.</td></tr><?php endif; ?><?php foreach($nextLessons as $l): ?><tr><td><?= e($l['data_lezione']) ?></td><td><?= e(substr($l['ora_inizio'],0,5).' - '.substr($l['ora_fine'],0,5)) ?></td><td><?= e($l['corso_titolo']) ?></td><td><?= e($l['titolo']) ?></td><td><?php if($l['google_meet_link']): ?><a target="_blank" href="<?= e($l['google_meet_link']) ?>">Apri</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
