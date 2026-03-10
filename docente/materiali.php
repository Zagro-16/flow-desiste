<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle='Materiali Didattici';
$userId=(int)$_SESSION['user_id'];
$coursesStmt=$pdo->prepare('SELECT DISTINCT c.id,c.titolo FROM courses c JOIN lessons l ON l.course_id=c.id WHERE l.docente_id=:uid ORDER BY c.titolo');
$coursesStmt->execute(['uid'=>$userId]);
$courses=$coursesStmt->fetchAll();

$stmt=$pdo->prepare('SELECT m.*, c.titolo AS corso_titolo FROM materials m JOIN courses c ON c.id=m.course_id WHERE m.uploaded_by=:uid ORDER BY m.creato_il DESC');
$stmt->execute(['uid'=>$userId]);
$materials=$stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-header">Carica materiale</div><div class="card-body"><form action="/actions/materiale-upload.php" method="post" enctype="multipart/form-data" class="row g-3"><div class="col-md-4"><label class="form-label">Corso</label><select class="form-select" name="course_id" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div><div class="col-md-4"><label class="form-label">Titolo</label><input class="form-control" name="titolo" required></div><div class="col-md-4"><label class="form-label">File</label><input type="file" class="form-control" name="material_file" required></div><div class="col-12"><button class="btn btn-primary">Carica</button></div></form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corso</th><th>Titolo</th><th>Data</th><th>File</th></tr></thead><tbody><?php if(!$materials): ?><tr><td colspan="4" class="text-center text-muted py-4">Nessun materiale caricato.</td></tr><?php endif; ?><?php foreach($materials as $m): ?><tr><td><?= e($m['corso_titolo']) ?></td><td><?= e($m['titolo']) ?></td><td><?= e($m['creato_il']) ?></td><td><a target="_blank" href="/<?= e($m['file_path']) ?>">Scarica</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
