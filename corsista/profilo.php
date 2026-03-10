<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['corsista']);
$pageTitle='Profilo Anagrafico'; $uid=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT sp.*, u.email AS account_email FROM student_profiles sp JOIN users u ON u.id=sp.user_id WHERE sp.user_id=:uid LIMIT 1');
$stmt->execute(['uid'=>$uid]); $p=$stmt->fetch();
$stmt=$pdo->prepare('SELECT * FROM student_documents WHERE student_profile_id=:sid ORDER BY data_upload DESC');
$docs=[];
if($p){ $stmt->execute(['sid'=>$p['id']]); $docs=$stmt->fetchAll(); }
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-corsista.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<?php if(!$p): ?><div class="alert alert-warning">Profilo non trovato.</div><?php else: ?>
<div class="card shadow-sm mb-3"><div class="card-body"><div class="row g-3">
<?php foreach(['nome','cognome','data_nascita','nazione','citta_nascita','sesso','codice_fiscale','residenza','indirizzo','cap','provincia','comune','cellulare','mail','titolo_studio','iban','data_inserimento_corsista','data_eventuale_rinuncia','stato_corsista'] as $f): ?>
<div class="col-md-4"><label class="form-label text-capitalize"><?= e(str_replace('_',' ',$f)) ?></label><input class="form-control" value="<?= e((string)$p[$f]) ?>" disabled></div>
<?php endforeach; ?>
</div></div></div>
<div class="card shadow-sm"><div class="card-header">Documenti associati</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Tipologia</th><th>Nome</th><th>Data upload</th><th>File</th></tr></thead><tbody><?php if(!$docs): ?><tr><td colspan="4" class="text-center text-muted py-4">Nessun documento caricato.</td></tr><?php endif; ?><?php foreach($docs as $d): ?><tr><td><?= e($d['tipologia']) ?></td><td><?= e($d['nome_originale']) ?></td><td><?= e($d['data_upload']) ?></td><td><a target="_blank" href="/<?= e($d['percorso']) ?>">Apri</a></td></tr><?php endforeach; ?></tbody></table></div></div>
<?php endif; ?>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
