<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Protocollo';

$filters = [
    'numero' => trim((string)($_GET['numero'] ?? '')),
    'stato' => trim((string)($_GET['stato'] ?? '')),
    'tipologia' => trim((string)($_GET['tipologia'] ?? '')),
    'destinatario' => trim((string)($_GET['destinatario'] ?? '')),
    'data_da' => trim((string)($_GET['data_da'] ?? '')),
    'data_a' => trim((string)($_GET['data_a'] ?? '')),
];

$sql = 'SELECT p.*, u.nome, u.cognome,
        (SELECT GROUP_CONCAT(pd.destinatario_tipo SEPARATOR ", ") FROM protocolli_destinatari pd WHERE pd.protocollo_id = p.id) AS destinatari
        FROM protocollo p
        JOIN users u ON u.id = p.autore_id WHERE 1=1';
$params = [];
if ($filters['numero'] !== '') { $sql .= ' AND p.numero_protocollo LIKE :numero'; $params['numero'] = '%' . $filters['numero'] . '%'; }
if ($filters['stato'] !== '') { $sql .= ' AND p.stato = :stato'; $params['stato'] = $filters['stato']; }
if ($filters['tipologia'] !== '') { $sql .= ' AND p.tipologia LIKE :tipologia'; $params['tipologia'] = '%' . $filters['tipologia'] . '%'; }
if ($filters['data_da'] !== '') { $sql .= ' AND p.data_protocollo >= :data_da'; $params['data_da'] = $filters['data_da']; }
if ($filters['data_a'] !== '') { $sql .= ' AND p.data_protocollo <= :data_a'; $params['data_a'] = $filters['data_a']; }
if ($filters['destinatario'] !== '') {
    $sql .= ' AND EXISTS (SELECT 1 FROM protocolli_destinatari x WHERE x.protocollo_id = p.id AND x.destinatario_tipo = :destinatario)';
    $params['destinatario'] = $filters['destinatario'];
}
$sql .= ' ORDER BY p.data_protocollo DESC, p.id DESC LIMIT 500';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4">
<?php require __DIR__ . '/../includes/page-header.php'; ?>
<?php require __DIR__ . '/../includes/alerts.php'; ?>

<div class="card shadow-sm mb-3"><div class="card-body">
    <form class="row g-2" method="get">
        <div class="col-md-2"><input class="form-control" name="numero" placeholder="N. protocollo" value="<?= e($filters['numero']) ?>"></div>
        <div class="col-md-2"><input class="form-control" name="tipologia" placeholder="Tipologia" value="<?= e($filters['tipologia']) ?>"></div>
        <div class="col-md-2"><select name="stato" class="form-select"><option value="">Stato</option><?php foreach(['bozza','inviato','archiviato'] as $s): ?><option value="<?= e($s) ?>" <?= $filters['stato']===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="destinatario" class="form-select"><option value="">Destinatario</option><?php foreach(['Admin','Segreteria','Docenti','Corsisti','Corso specifico','Utente specifico'] as $d): ?><option value="<?= e($d) ?>" <?= $filters['destinatario']===$d?'selected':'' ?>><?= e($d) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><input type="date" class="form-control" name="data_da" value="<?= e($filters['data_da']) ?>"></div>
        <div class="col-md-2"><input type="date" class="form-control" name="data_a" value="<?= e($filters['data_a']) ?>"></div>
        <div class="col-md-6 d-flex gap-2"><button class="btn btn-primary" type="submit">Filtra</button><a class="btn btn-outline-secondary" href="/admin/protocollo.php">Reset</a><a class="btn btn-success" href="/admin/protocollo-new.php">Nuovo protocollo</a></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>Numero</th><th>Data</th><th>Oggetto</th><th>Mittente</th><th>Destinatari</th><th>Stato</th><th>Autore</th><th></th></tr></thead>
<tbody>
<?php if (!$rows): ?><tr><td colspan="8" class="text-center text-muted py-4">Nessun protocollo trovato.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?>
<tr>
<td><?= e($r['numero_protocollo']) ?></td><td><?= e($r['data_protocollo']) ?></td><td><?= e($r['oggetto']) ?></td><td><?= e($r['mittente_tipo']) ?></td>
<td><small><?= e((string)$r['destinatari']) ?></small></td><td><span class="badge text-bg-secondary"><?= e($r['stato']) ?></span></td>
<td><?= e($r['cognome'].' '.$r['nome']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="/admin/protocollo-view.php?id=<?= (int)$r['id'] ?>">Apri</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
