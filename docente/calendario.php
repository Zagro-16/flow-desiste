<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle = 'Calendario Lezioni';
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT l.*, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id WHERE l.docente_id=:uid AND l.data_lezione>=CURDATE() ORDER BY l.data_lezione, l.ora_inizio');
$stmt->execute(['uid'=>$userId]);
$lessons = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="row g-3">
<?php if(!$lessons): ?><div class="col-12"><div class="alert alert-info">Nessuna lezione in calendario.</div></div><?php endif; ?>
<?php foreach($lessons as $l): $msg=urlencode('Ciao, ricordati che oggi hai lezione. Ecco il link della lezione: '.($l['google_meet_link']?:'')); ?>
<div class="col-md-6 col-xl-4"><div class="card shadow-sm h-100"><div class="card-body">
<h6 class="mb-1"><?= e($l['corso_titolo']) ?></h6><div class="text-muted small mb-2"><?= e($l['titolo']) ?></div>
<div><strong><?= e($l['data_lezione']) ?></strong> <?= e(substr($l['ora_inizio'],0,5).' - '.substr($l['ora_fine'],0,5)) ?></div>
<div class="mt-2 d-flex gap-2 flex-wrap">
<?php if($l['google_meet_link']): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e($l['google_meet_link']) ?>">Meet</a><?php endif; ?>
<a class="btn btn-sm btn-outline-success" target="_blank" href="https://wa.me/?text=<?= $msg ?>">WhatsApp</a>
</div>
</div></div></div>
<?php endforeach; ?>
</div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
