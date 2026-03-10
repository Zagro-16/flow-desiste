<?php
$currentRole = (string)($_SESSION['role'] ?? '');
$roleLabel = match ($currentRole) {
    'admin' => 'Admin / Segreteria',
    'docente' => 'Docente',
    'corsista' => 'Corsista',
    default => 'Utente',
};

$roleMenus = [
    'admin' => [
        ['label' => 'Dashboard', 'href' => '/admin/dashboard.php'],
        ['label' => 'Corsi', 'href' => '/admin/corsi.php'],
        ['label' => 'Lezioni', 'href' => '/admin/lezioni.php'],
        ['label' => 'Calendario', 'href' => '/admin/calendario.php'],
        ['label' => 'Docenti', 'href' => '/admin/docenti.php'],
        ['label' => 'Corsisti', 'href' => '/admin/corsisti.php'],
        ['label' => 'Iscrizioni', 'href' => '/admin/iscrizioni.php'],
        ['label' => 'Presenze', 'href' => '/admin/presenze.php'],
        ['label' => 'Materiali', 'href' => '/admin/materiali.php'],
        ['label' => 'Quiz', 'href' => '/admin/quiz.php'],
        ['label' => 'Attestati', 'href' => '/admin/attestati.php'],
        ['label' => 'Comunicazioni', 'href' => '/admin/comunicazioni.php'],
        ['label' => 'Report', 'href' => '/admin/report.php'],
        ['label' => 'Protocollo', 'href' => '/admin/protocollo.php'],
        ['label' => 'Documenti corsista', 'href' => '/admin/corsista-documenti.php'],
        ['label' => 'Giornate corso', 'href' => '/admin/giornate-corso.php'],
        ['label' => 'Export', 'href' => '/admin/export.php'],
        ['label' => 'Rinunce / Desistenze', 'href' => '/admin/rinunce.php'],
        ['label' => 'Impostazioni', 'href' => '/admin/impostazioni.php'],
    ],
    'docente' => [
        ['label' => 'Dashboard', 'href' => '/docente/dashboard.php'],
        ['label' => 'Corsi assegnati', 'href' => '/docente/corsi.php'],
        ['label' => 'Lezioni', 'href' => '/docente/lezioni.php'],
        ['label' => 'Calendario', 'href' => '/docente/calendario.php'],
        ['label' => 'Presenze', 'href' => '/docente/presenze.php'],
        ['label' => 'Materiali', 'href' => '/docente/materiali.php'],
        ['label' => 'Quiz', 'href' => '/docente/quiz.php'],
        ['label' => 'Monte ore', 'href' => '/docente/monteore.php'],
        ['label' => 'Profilo', 'href' => '/docente/profilo.php'],
    ],
    'corsista' => [
        ['label' => 'Dashboard', 'href' => '/corsista/dashboard.php'],
        ['label' => 'Corso assegnato', 'href' => '/corsista/corsi.php'],
        ['label' => 'Calendario', 'href' => '/corsista/calendario.php'],
        ['label' => 'Materiali', 'href' => '/corsista/materiali.php'],
        ['label' => 'Quiz', 'href' => '/corsista/quiz.php'],
        ['label' => 'Attestati', 'href' => '/corsista/attestati.php'],
        ['label' => 'Profilo', 'href' => '/corsista/profilo.php'],
    ],
];

$menuItems = $roleMenus[$currentRole] ?? [];
?>
<nav class="navbar navbar-expand-lg ff-topbar sticky-top border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/dashboard.php">
            <img src="/assets/img/logo.png" alt="FormaFlow" class="ff-brand-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
            <span class="ff-brand-fallback" style="display:none;">FF</span>
            <span class="ff-brand-title"><?= e(APP_NAME) ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ffTopbarUserArea" aria-controls="ffTopbarUserArea" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="ffTopbarUserArea">
            <div class="d-flex align-items-center gap-3 py-2 py-lg-0 w-100 justify-content-end">
                <?php if ($menuItems): ?>
                <div class="dropdown ff-nav-dropdown">
                    <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu rapido
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 ff-nav-dropdown-menu shadow-lg">
                        <div class="small text-uppercase text-muted mb-2">Navigazione <?= e($roleLabel) ?></div>
                        <div class="row g-2">
                            <?php foreach ($menuItems as $item): ?>
                                <div class="col-12 col-md-6">
                                    <a class="dropdown-item ff-menu-item <?= nav_is_active($item['href']) ?>" href="<?= e($item['href']) ?>">
                                        <?= e($item['label']) ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="text-end">
                    <div class="small text-muted mb-0"><?= e($roleLabel) ?></div>
                    <div class="fw-semibold small"><?= e($_SESSION['full_name'] ?? 'Utente') ?></div>
                </div>
                <a href="/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>
