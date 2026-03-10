<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Comunicazioni';

$targetType = trim((string)($_GET['target_type'] ?? ''));
$sql = 'SELECT c.*, u.nome AS autore_nome, u.cognome AS autore_cognome FROM communications c JOIN users u ON u.id=c.created_by WHERE 1=1';
$params = [];
if ($targetType !== '') {
    $sql .= ' AND c.target_type = :target_type';
    $params['target_type'] = $targetType;
}
$sql .= ' ORDER BY c.created_at DESC LIMIT 300';

$rows = [];
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
} catch (Throwable $e) {
    flash('warning', 'Tabella comunicazioni non disponibile. Esegui aggiornamento DB.');
}

$courses = $pdo->query('SELECT id, titolo FROM courses ORDER BY titolo')->fetchAll();
$users = $pdo->query('SELECT id, nome, cognome, role FROM users WHERE stato="attivo" ORDER BY cognome,nome LIMIT 200')->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<div class="card shadow-sm mb-3"><div class="card-header">Nuova comunicazione</div><div class="card-body">
<form action="/actions/invia-reminder-manuale.php" method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Destinazione</label><select class="form-select" name="target_type" id="targetType" required><option value="Docenti">Docenti</option><option value="Corsisti">Corsisti</option><option value="Corso">Corso</option><option value="Utente">Utente</option><option value="Tutti">Tutti</option></select></div>
<div class="col-md-3"><label class="form-label">Corso (se Corso)</label><select class="form-select" name="course_id"><option value="">-</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Utente (se Utente)</label><select class="form-select" name="user_id"><option value="">-</option><?php foreach($users as $u): ?><option value="<?= (int)$u['id'] ?>"><?= e($u['cognome'].' '.$u['nome'].' ['.$u['role'].']') ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Oggetto</label><input class="form-control" name="subject" required></div>
<div class="col-12"><label class="form-label">Messaggio</label><textarea class="form-control" rows="5" name="message" required></textarea></div>
<div class="col-12 d-flex gap-2"><button class="btn btn-primary">Invia comunicazione</button><small class="text-muted align-self-center">Viene registrata nello storico e nei log email.</small></div>
</form>
</div></div>

<div class="card shadow-sm"><div class="card-header">Storico comunicazioni</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Data</th><th>Target</th><th>Oggetto</th><th>Autore</th><th>Destinatari</th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessuna comunicazione disponibile.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['target_type']) ?></td><td><?= e($r['subject']) ?></td><td><?= e($r['autore_cognome'].' '.$r['autore_nome']) ?></td><td><?= (int)$r['total_recipients'] ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
