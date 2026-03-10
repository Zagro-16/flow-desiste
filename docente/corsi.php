<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle = 'Corsi Assegnati';
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT DISTINCT c.*
                       FROM courses c
                       JOIN lessons l ON l.course_id=c.id
                       WHERE l.docente_id=:uid
                       ORDER BY c.data_inizio DESC');
$stmt->execute(['uid' => $userId]);
$courses = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<thead><tr><th>Codice</th><th>Titolo</th><th>Periodo</th><th>Stato</th><th>Ore</th></tr></thead>
<tbody><?php if(!$courses): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessun corso assegnato.</td></tr><?php endif; ?>
<?php foreach($courses as $c): ?><tr><td><?= e($c['codice']) ?></td><td><?= e($c['titolo']) ?></td><td><?= e($c['data_inizio']) ?> → <?= e($c['data_fine']) ?></td><td><?= e($c['stato']) ?></td><td><?= e((string)$c['monte_ore_totali']) ?></td></tr><?php endforeach; ?></tbody>
</table></div></div></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
