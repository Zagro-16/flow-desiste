<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Gestione Corsi';
$stato = trim((string)($_GET['stato'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.course_id=c.id) AS iscritti FROM courses c WHERE 1=1';
$params = [];
if ($stato !== '') { $sql .= ' AND c.stato=:stato'; $params['stato'] = $stato; }
if ($q !== '') { $sql .= ' AND (c.codice LIKE :q OR c.titolo LIKE :q)'; $params['q'] = '%'.$q.'%'; }
$sql .= ' ORDER BY c.data_inizio DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $rows = $stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body"><form class="row g-2" method="get">
<div class="col-md-5"><input class="form-control" name="q" placeholder="Codice o titolo" value="<?= e($q) ?>"></div>
<div class="col-md-3"><select class="form-select" name="stato"><option value="">Tutti gli stati</option><?php foreach(['bozza','attivo','sospeso','chiuso'] as $s): ?><option value="<?= e($s) ?>" <?= $stato===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">Filtra</button><a href="/admin/corso-new.php" class="btn btn-success">Nuovo corso</a></div>
</form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Codice</th><th>Titolo</th><th>Periodo</th><th>Stato</th><th>Monte ore</th><th>Iscritti</th><th></th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="7" class="text-center text-muted py-4">Nessun corso trovato</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['codice']) ?></td><td><?= e($r['titolo']) ?></td><td><?= e($r['data_inizio']) ?> → <?= e($r['data_fine']) ?></td><td><span class="badge text-bg-secondary"><?= e($r['stato']) ?></span></td><td><?= e((string)$r['monte_ore_totali']) ?></td><td><?= (int)$r['iscritti'] ?></td><td><a href="/admin/corso-edit.php?id=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
