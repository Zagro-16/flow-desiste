<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Gestione Lezioni';
$courseId = (int)($_GET['course_id'] ?? 0);
$sql = 'SELECT l.*, c.titolo AS corso_titolo, CONCAT(u.cognome," ",u.nome) AS docente_nome
        FROM lessons l JOIN courses c ON c.id=l.course_id JOIN users u ON u.id=l.docente_id WHERE 1=1';
$params=[];
if($courseId>0){$sql.=' AND l.course_id=:course_id';$params['course_id']=$courseId;}
$sql.=' ORDER BY l.data_lezione DESC, l.ora_inizio DESC LIMIT 500';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
$corsi = $pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body"><form method="get" class="row g-2"><div class="col-md-6"><select name="course_id" class="form-select"><option value="0">Tutti i corsi</option><?php foreach($corsi as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $courseId===(int)$c['id']?'selected':'' ?>><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div><div class="col-md-6 d-flex gap-2"><button class="btn btn-primary">Filtra</button><a href="/admin/lezione-new.php" class="btn btn-success">Nuova lezione</a></div></form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Data</th><th>Orario</th><th>Titolo</th><th>Corso</th><th>Docente</th><th>Meet</th><th>Stato</th><th></th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="8" class="text-center text-muted py-4">Nessuna lezione presente.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['data_lezione']) ?></td><td><?= e(substr($r['ora_inizio'],0,5).' - '.substr($r['ora_fine'],0,5)) ?></td><td><?= e($r['titolo']) ?></td><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['docente_nome']) ?></td><td><?php if($r['google_meet_link']): ?><a target="_blank" href="<?= e($r['google_meet_link']) ?>">Apri</a><?php else: ?>-<?php endif; ?></td><td><?= e($r['stato']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="/admin/lezione-edit.php?id=<?= (int)$r['id'] ?>">Modifica</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
