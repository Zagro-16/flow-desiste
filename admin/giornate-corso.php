<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Giornate Corso';

$stmt = $pdo->query('SELECT cd.*, c.titolo AS corso_titolo, cm.nome AS modulo_nome, cl.nome AS sede_nome
                     FROM course_days cd
                     JOIN courses c ON c.id = cd.idcorso
                     JOIN course_modules cm ON cm.id = cd.idmodulo
                     JOIN course_locations cl ON cl.id = cd.idsede
                     ORDER BY cd.anno DESC, cd.mese DESC, cd.giorno DESC, cd.ora DESC, cd.minuto DESC LIMIT 500');
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="d-flex gap-2 mb-3">
    <a href="/admin/giornata-new.php" class="btn btn-success">Nuova giornata</a>
    <a href="/actions/export-giornate-corso-csv.php" class="btn btn-outline-primary">Export CSV</a>
    <a href="/actions/export-giornate-corso-excel.php" class="btn btn-outline-success">Export Excel</a>
</div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>Data/Ora</th><th>Durata</th><th>Corso</th><th>Stage</th><th>Docente</th><th>Modulo</th><th>Sede</th><th>Codocente</th><th>Tutor</th><th></th></tr></thead>
<tbody>
<?php if(!$rows): ?><tr><td colspan="10" class="text-center text-muted py-4">Nessuna giornata registrata.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?>
<tr>
<td><?= e(sprintf('%02d/%02d/%04d %02d:%02d', (int)$r['giorno'], (int)$r['mese'], (int)$r['anno'], (int)$r['ora'], (int)$r['minuto'])) ?></td>
<td><?= (int)$r['durata'] ?> min</td><td><?= e($r['corso_titolo']) ?></td><td><?= e($r['stage']) ?></td><td><?= e($r['cf_docente']) ?></td>
<td><?= e($r['modulo_nome']) ?></td><td><?= e($r['sede_nome']) ?></td><td><?= e((string)($r['cf_codocente'] ?: '-')) ?></td><td><?= e((string)($r['cf_tutor'] ?: '-')) ?></td>
<td><a class="btn btn-sm btn-outline-primary" href="/admin/giornata-edit.php?id=<?= (int)$r['id'] ?>">Modifica</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
