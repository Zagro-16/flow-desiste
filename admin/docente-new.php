<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$pageTitle='Nuovo Docente';
$docente=['id'=>0,'nome'=>'','cognome'=>'','email'=>'','stato'=>'attivo','codice_fiscale'=>'','telefono'=>'','ore_assegnate'=>'0','ore_svolte'=>'0'];
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/docente-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
