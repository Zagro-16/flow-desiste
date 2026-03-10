<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Export';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="card shadow-sm"><div class="card-body">
    <h5 class="mb-3">Export Giornate Corso</h5>
    <p class="text-muted">Le esportazioni rispettano il tracciato richiesto: GIORNO, MESE, ANNO, ORA, MINUTO, DURATA, IDCORSO, STAGE, CF_Docente, IdModulo, idSede, CF_Codocente, CF_Tutor.</p>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-primary" href="/actions/export-giornate-corso-csv.php">Scarica CSV</a>
        <a class="btn btn-outline-success" href="/actions/export-giornate-corso-excel.php">Scarica Excel (.xls)</a>
    </div>
</div></div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
