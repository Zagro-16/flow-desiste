<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Nuova Giornata Corso';
$data = ['id'=>0,'giorno'=>date('d'),'mese'=>date('m'),'anno'=>date('Y'),'ora'=>'09','minuto'=>'00','durata'=>'240','idcorso'=>'','stage'=>'A','cf_docente'=>'','idmodulo'=>'','idsede'=>'','cf_codocente'=>'','cf_tutor'=>''];
$corsi = $pdo->query('SELECT id,titolo FROM courses ORDER BY titolo')->fetchAll();
$moduli = $pdo->query('SELECT id,nome FROM course_modules ORDER BY nome')->fetchAll();
$sedi = $pdo->query('SELECT id,nome FROM course_locations ORDER BY nome')->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/giornata-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
