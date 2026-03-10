<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$pageTitle='Nuova Lezione';
$corsi=$pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();
$docenti=$pdo->query("SELECT id,nome,cognome FROM users WHERE role='docente' AND stato='attivo' ORDER BY cognome,nome")->fetchAll();
$lezione=['id'=>0,'titolo'=>'','course_id'=>$corsi[0]['id'] ?? 0,'docente_id'=>$docenti[0]['id'] ?? 0,'data_lezione'=>date('Y-m-d'),'ora_inizio'=>'09:00','ora_fine'=>'13:00','google_meet_link'=>'','stato'=>'programmata'];
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/lezione-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
