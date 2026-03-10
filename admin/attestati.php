<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Attestati';

$enrolled = $pdo->query('SELECT e.course_id, e.student_id, c.titolo AS corso_titolo, sp.nome, sp.cognome
                         FROM enrollments e
                         JOIN courses c ON c.id=e.course_id
                         JOIN users u ON u.id=e.student_id
                         JOIN student_profiles sp ON sp.user_id=u.id
                         WHERE e.stato IN ("attivo","completato")
                         ORDER BY c.titolo, sp.cognome')->fetchAll();

$rows = $pdo->query('SELECT ct.*, c.titolo AS corso_titolo, sp.nome, sp.cognome
                     FROM certificates ct
                     JOIN courses c ON c.id=ct.course_id
                     JOIN users u ON u.id=ct.student_id
                     JOIN student_profiles sp ON sp.user_id=u.id
                     ORDER BY ct.generato_il DESC LIMIT 300')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<div class="card shadow-sm mb-3"><div class="card-header">Genera attestato</div><div class="card-body">
<form action="/actions/attestato-genera.php" method="post" class="row g-3">
<div class="col-md-8"><label class="form-label">Corsista / Corso</label><select class="form-select" name="enrollment_pair" required><?php foreach($enrolled as $e): ?><option value="<?= (int)$e['student_id'] ?>:<?= (int)$e['course_id'] ?>"><?= e($e['corso_titolo'].' - '.$e['cognome'].' '.$e['nome']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Timbro/firma</label><select class="form-select" name="stamp_type"><option value="timbro_firma">Timbro con firma</option><option value="timbro">Timbro standard</option></select></div>
<div class="col-12"><button class="btn btn-primary">Genera attestato PDF</button></div>
</form>
</div></div>

<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Data</th><th>Codice</th><th>Corsista</th><th>Corso</th><th>File</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessun attestato generato.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['generato_il']) ?></td><td><?= e($r['codice_univoco']) ?></td><td><?= e($r['cognome'].' '.$r['nome']) ?></td><td><?= e($r['corso_titolo']) ?></td><td><a target="_blank" href="/<?= e($r['file_path']) ?>">Scarica</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
