<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$pageTitle='Gestione Docenti';
$q=trim((string)($_GET['q'] ?? ''));
$sql='SELECT u.id,u.nome,u.cognome,u.email,u.stato,tp.codice_fiscale,tp.telefono,tp.ore_assegnate,tp.ore_svolte FROM users u LEFT JOIN teacher_profiles tp ON tp.user_id=u.id WHERE u.role="docente"';
$params=[];
if($q!==''){ $sql.=' AND (u.nome LIKE :q OR u.cognome LIKE :q OR u.email LIKE :q OR tp.codice_fiscale LIKE :q)'; $params['q']='%'.$q.'%'; }
$sql.=' ORDER BY u.cognome,u.nome';
$stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm mb-3"><div class="card-body"><form method="get" class="row g-2"><div class="col-md-8"><input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Cerca docente per nome, email, CF"></div><div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">Cerca</button><a href="/admin/docente-new.php" class="btn btn-success">Nuovo docente</a></div></form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Docente</th><th>Email</th><th>CF</th><th>Telefono</th><th>Ore assegnate</th><th>Ore svolte</th><th>Stato</th><th></th></tr></thead><tbody>
<?php if(!$rows): ?><tr><td colspan="8" class="text-center text-muted py-4">Nessun docente.</td></tr><?php endif; ?>
<?php foreach($rows as $r): ?><tr><td><?= e($r['cognome'].' '.$r['nome']) ?></td><td><?= e($r['email']) ?></td><td><?= e((string)$r['codice_fiscale']) ?></td><td><?= e((string)$r['telefono']) ?></td><td><?= e((string)$r['ore_assegnate']) ?></td><td><?= e((string)$r['ore_svolte']) ?></td><td><?= e($r['stato']) ?></td><td><a class="btn btn-sm btn-outline-primary" href="/admin/docente-edit.php?id=<?= (int)$r['id'] ?>">Modifica</a></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
