<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle = 'Lezioni Assegnate';
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT l.*, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id WHERE l.docente_id=:uid ORDER BY l.data_lezione DESC, l.ora_inizio DESC');
$stmt->execute(['uid' => $userId]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>Data</th><th>Orario</th><th>Corso</th><th>Titolo</th><th>Stato</th><th>Meet</th></tr></thead>
<tbody><?php if(!$rows): ?><tr><td colspan="6" class="text-center text-muted py-4">Nessuna lezione assegnata.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['data_lezione']) ?></td><td><?= e(substr($r['ora_inizio'],0,5).' - '.substr($r['ora_fine'],0,5)) ?></td><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['titolo']) ?></td><td><?= e($r['stato']) ?></td><td><?php if($r['google_meet_link']): ?><a target="_blank" href="<?= e($r['google_meet_link']) ?>">Apri</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody>
</table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
