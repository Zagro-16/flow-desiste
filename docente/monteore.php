<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle='Monte Ore';
$userId=(int)$_SESSION['user_id'];
$stmt=$pdo->prepare('SELECT COALESCE(ore_assegnate,0) AS ore_assegnate, COALESCE(ore_svolte,0) AS ore_svolte FROM teacher_profiles WHERE user_id=:uid LIMIT 1');
$stmt->execute(['uid'=>$userId]);
$hours=$stmt->fetch() ?: ['ore_assegnate'=>0,'ore_svolte'=>0];
$calcStmt=$pdo->prepare('SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE,ora_inizio,ora_fine))/60,0) FROM lessons WHERE docente_id=:uid AND stato="svolta"');
$calcStmt->execute(['uid'=>$userId]);
$calc=(float)$calcStmt->fetchColumn();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="row g-3"><div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Ore assegnate</div><div class="h3"><?= e((string)$hours['ore_assegnate']) ?></div></div></div></div>
<div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Ore svolte (profilo)</div><div class="h3"><?= e((string)$hours['ore_svolte']) ?></div></div></div></div>
<div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Ore svolte (calcolate)</div><div class="h3"><?= e((string)$calc) ?></div></div></div></div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
