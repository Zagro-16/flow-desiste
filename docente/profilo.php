<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle='Profilo Docente';
$userId=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT u.nome,u.cognome,u.email,u.stato,tp.codice_fiscale,tp.telefono,tp.ore_assegnate,tp.ore_svolte FROM users u LEFT JOIN teacher_profiles tp ON tp.user_id=u.id WHERE u.id=:id');
$stmt->execute(['id'=>$userId]);
$p=$stmt->fetch();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Nome</label><input class="form-control" value="<?= e($p['nome'] ?? '') ?>" disabled></div>
<div class="col-md-4"><label class="form-label">Cognome</label><input class="form-control" value="<?= e($p['cognome'] ?? '') ?>" disabled></div>
<div class="col-md-4"><label class="form-label">Email</label><input class="form-control" value="<?= e($p['email'] ?? '') ?>" disabled></div>
<div class="col-md-4"><label class="form-label">Telefono</label><input class="form-control" value="<?= e($p['telefono'] ?? '') ?>" disabled></div>
<div class="col-md-4"><label class="form-label">Codice fiscale</label><input class="form-control" value="<?= e($p['codice_fiscale'] ?? '') ?>" disabled></div>
<div class="col-md-4"><label class="form-label">Stato</label><input class="form-control" value="<?= e($p['stato'] ?? '') ?>" disabled></div>
</div></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
