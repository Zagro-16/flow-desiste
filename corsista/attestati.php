<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['corsista']);
$pageTitle='Attestati'; $uid=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT ct.*, c.titolo AS corso_titolo FROM certificates ct JOIN courses c ON c.id=ct.course_id WHERE ct.student_id=:uid ORDER BY ct.generato_il DESC');
$stmt->execute(['uid'=>$uid]); $rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-corsista.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Codice</th><th>Corso</th><th>Data</th><th>PDF</th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="4" class="text-center text-muted py-4">Nessun attestato disponibile.</td></tr><?php endif; ?><?php foreach($rows as $r): ?><tr><td><?= e($r['codice_univoco']) ?></td><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['generato_il']) ?></td><td><a target="_blank" href="/<?= e($r['file_path']) ?>">Scarica</a></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
