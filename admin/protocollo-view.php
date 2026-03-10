<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, u.nome, u.cognome FROM protocollo p JOIN users u ON u.id=p.autore_id WHERE p.id=:id');
$stmt->execute(['id' => $id]);
$p = $stmt->fetch();
if (!$p) { flash('danger','Protocollo non trovato'); header('Location:/admin/protocollo.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM protocolli_destinatari WHERE protocollo_id = :id');
$stmt->execute(['id' => $id]);
$dest = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT * FROM protocol_attachments WHERE protocollo_id = :id');
$stmt->execute(['id' => $id]);
$att = $stmt->fetchAll();

$pageTitle = 'Dettaglio Protocollo';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body">
<h5><?= e($p['numero_protocollo']) ?> - <?= e($p['oggetto']) ?></h5>
<p class="mb-1"><strong>Data:</strong> <?= e($p['data_protocollo']) ?> | <strong>Stato:</strong> <?= e($p['stato']) ?> | <strong>Tipologia:</strong> <?= e($p['tipologia']) ?></p>
<p class="mb-1"><strong>Mittente:</strong> <?= e($p['mittente_tipo']) ?></p>
<p class="mb-1"><strong>Autore:</strong> <?= e($p['cognome'].' '.$p['nome']) ?></p>
<hr><pre class="bg-light p-3" style="white-space:pre-wrap;"><?= e($p['contenuto']) ?></pre>
</div></div>
<div class="row g-3">
<div class="col-md-6"><div class="card shadow-sm"><div class="card-header">Destinatari</div><ul class="list-group list-group-flush"><?php foreach($dest as $d): ?><li class="list-group-item"><?= e($d['destinatario_tipo']) ?> <?= $d['destinatario_label'] ? '- '.e($d['destinatario_label']) : '' ?></li><?php endforeach; ?></ul></div></div>
<div class="col-md-6"><div class="card shadow-sm"><div class="card-header">Allegati</div><ul class="list-group list-group-flush"><?php if(!$att): ?><li class="list-group-item text-muted">Nessun allegato</li><?php endif; ?><?php foreach($att as $a): ?><li class="list-group-item d-flex justify-content-between"><span><?= e($a['nome_originale']) ?></span><a href="/<?= e($a['percorso']) ?>" target="_blank">Scarica</a></li><?php endforeach; ?></ul></div></div>
</div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
