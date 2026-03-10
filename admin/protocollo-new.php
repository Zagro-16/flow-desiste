<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Nuovo Protocollo';
$numero = nextProtocolNumber($pdo);
$types = ['Comunicazione', 'Avviso', 'Atto interno', 'Rinuncia', 'Desistenza'];
$dest = ['Admin','Segreteria','Docenti','Corsisti','Corso specifico','Utente specifico'];
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4">
<?php require __DIR__ . '/../includes/page-header.php'; ?>
<?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm">
<form method="post" action="/actions/protocollo-save.php" enctype="multipart/form-data">
<div class="card-body row g-3">
<div class="col-md-3"><label class="form-label">Numero protocollo</label><input class="form-control" name="numero_protocollo" value="<?= e($numero) ?>" readonly></div>
<div class="col-md-3"><label class="form-label">Data protocollo</label><input type="date" class="form-control" name="data_protocollo" value="<?= e(date('Y-m-d')) ?>" required></div>
<div class="col-md-3"><label class="form-label">Tipologia</label><select class="form-select" name="tipologia" required><?php foreach($types as $t): ?><option value="<?= e($t) ?>"><?= e($t) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Stato</label><select class="form-select" name="stato" required><option value="bozza">bozza</option><option value="inviato">inviato</option><option value="archiviato">archiviato</option></select></div>
<div class="col-md-4"><label class="form-label">Mittente interno</label><select class="form-select" name="mittente_tipo" required><?php foreach($dest as $d): ?><option value="<?= e($d) ?>"><?= e($d) ?></option><?php endforeach; ?></select></div>
<div class="col-md-8"><label class="form-label">Oggetto</label><input class="form-control" name="oggetto" required></div>
<div class="col-12"><label class="form-label">Contenuto documento</label><textarea class="form-control" name="contenuto" rows="8" required></textarea></div>
<div class="col-md-6"><label class="form-label">Destinatari</label><select class="form-select" name="destinatari[]" multiple size="6" required><?php foreach($dest as $d): ?><option value="<?= e($d) ?>"><?= e($d) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label">Allegato PDF</label><input type="file" name="allegato_pdf" class="form-control" accept="application/pdf"></div>
</div>
<div class="card-footer d-flex justify-content-between"><a class="btn btn-outline-secondary" href="/admin/protocollo.php">Annulla</a><button class="btn btn-primary" type="submit">Salva protocollo</button></div>
</form></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
