<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle='Calendario Lezioni';

$from = trim((string)($_GET['from'] ?? date('Y-m-01')));
$to = trim((string)($_GET['to'] ?? date('Y-m-t')));

$stmt = $pdo->prepare('SELECT l.*, c.titolo AS corso_titolo, CONCAT(u.cognome," ",u.nome) AS docente_nome
                       FROM lessons l
                       JOIN courses c ON c.id=l.course_id
                       JOIN users u ON u.id=l.docente_id
                       WHERE l.data_lezione BETWEEN :from AND :to
                       ORDER BY l.data_lezione, l.ora_inizio');
$stmt->execute(['from'=>$from,'to'=>$to]);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body"><form class="row g-2"><div class="col-md-4"><input type="date" class="form-control" name="from" value="<?= e($from) ?>"></div><div class="col-md-4"><input type="date" class="form-control" name="to" value="<?= e($to) ?>"></div><div class="col-md-4"><button class="btn btn-primary">Applica</button></div></form></div></div>

<div class="row g-3">
<?php if(!$rows): ?><div class="col-12"><div class="alert alert-info">Nessuna lezione nel range selezionato.</div></div><?php endif; ?>
<?php foreach($rows as $r):
$waText = urlencode('Ciao, ricordati che oggi hai lezione. Ecco il link della lezione: '.($r['google_meet_link']?:''));
?>
<div class="col-md-6 col-xl-4"><div class="card shadow-sm h-100"><div class="card-body">
<div class="small text-muted"><?= e($r['data_lezione']) ?> <?= e(substr($r['ora_inizio'],0,5)) ?> - <?= e(substr($r['ora_fine'],0,5)) ?></div>
<h6 class="mb-1"><?= e($r['titolo']) ?></h6>
<div class="small mb-2"><?= e($r['corso_titolo']) ?> · <?= e($r['docente_nome']) ?></div>
<div class="d-flex gap-2 flex-wrap">
<?php if($r['google_meet_link']): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e($r['google_meet_link']) ?>">Meet</a><?php endif; ?>
<a class="btn btn-sm btn-outline-success" target="_blank" href="https://wa.me/?text=<?= $waText ?>">Condividi WhatsApp</a>
</div>
</div></div></div>
<?php endforeach; ?>
</div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
