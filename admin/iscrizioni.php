<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Iscrizioni';

$courses = $pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();
$students = $pdo->query('SELECT sp.id AS student_profile_id, u.id AS user_id, sp.cognome, sp.nome FROM student_profiles sp JOIN users u ON u.id=sp.user_id ORDER BY sp.cognome,sp.nome')->fetchAll();

$stmt = $pdo->query('SELECT e.*, c.titolo AS corso_titolo, sp.nome, sp.cognome
                     FROM enrollments e
                     JOIN courses c ON c.id=e.course_id
                     JOIN users u ON u.id=e.student_id
                     JOIN student_profiles sp ON sp.user_id=u.id
                     ORDER BY e.creato_il DESC LIMIT 300');
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<div class="card shadow-sm mb-3"><div class="card-header">Nuova iscrizione</div><div class="card-body">
<form action="/actions/iscrizione-save.php" method="post" class="row g-3">
<div class="col-md-4"><label class="form-label">Corsista</label><select class="form-select" name="student_id" required><?php foreach($students as $s): ?><option value="<?= (int)$s['user_id'] ?>"><?= e($s['cognome'].' '.$s['nome']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">Corso</label><select class="form-select" name="course_id" required><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-2"><label class="form-label">Stato</label><select class="form-select" name="stato" required><option value="attivo">attivo</option><option value="ritirato">ritirato</option><option value="completato">completato</option></select></div>
<div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Salva</button></div>
</form>
</div></div>

<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Data</th><th>Corsista</th><th>Corso</th><th>Stato</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="4" class="text-center text-muted py-4">Nessuna iscrizione registrata.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['creato_il']) ?></td><td><?= e($r['cognome'].' '.$r['nome']) ?></td><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['stato']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
