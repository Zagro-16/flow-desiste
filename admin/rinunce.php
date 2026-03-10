<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Rinunce / Desistenze';
$students = $pdo->query('SELECT id, cognome, nome, stato_corsista FROM student_profiles ORDER BY cognome,nome')->fetchAll();
$rows = $pdo->query('SELECT wr.*, sp.nome, sp.cognome, u.nome AS admin_nome, u.cognome AS admin_cognome FROM withdrawals_or_renunciations wr JOIN student_profiles sp ON sp.id=wr.student_profile_id JOIN users u ON u.id=wr.registrato_da ORDER BY wr.creato_il DESC')->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-header">Registra rinuncia/desistenza</div><div class="card-body">
<form action="/actions/rinuncia-save.php" method="post" class="row g-3">
<div class="col-md-4"><label class="form-label">Corsista</label><select class="form-select" name="student_profile_id" required><?php foreach($students as $s): ?><option value="<?= (int)$s['id'] ?>"><?= e($s['cognome'].' '.$s['nome']) ?> (<?= e($s['stato_corsista']) ?>)</option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label">Tipo</label><select class="form-select" name="tipo" required><option value="rinuncia">rinuncia</option><option value="desistenza">desistenza</option></select></div>
<div class="col-md-2"><label class="form-label">Data evento</label><input type="date" class="form-control" name="data_evento" value="<?= e(date('Y-m-d')) ?>" required></div>
<div class="col-md-4"><label class="form-label">Motivazione</label><input class="form-control" name="motivazione" required></div>
<div class="col-12"><button class="btn btn-primary">Registra evento</button></div>
</form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Corsista</th><th>Tipo</th><th>Data evento</th><th>Motivazione</th><th>Registrato da</th><th>Creato il</th></tr></thead><tbody><?php if(!$rows): ?><tr><td colspan="6" class="text-center text-muted py-4">Nessun evento registrato.</td></tr><?php endif; ?><?php foreach($rows as $r): ?><tr><td><?= e($r['cognome'].' '.$r['nome']) ?></td><td><?= e($r['tipo']) ?></td><td><?= e($r['data_evento']) ?></td><td><?= e($r['motivazione']) ?></td><td><?= e($r['admin_cognome'].' '.$r['admin_nome']) ?></td><td><?= e($r['creato_il']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
