<?php
$currentRole = (string)($_SESSION['role'] ?? '');
$roleLabel = match ($currentRole) {
    'admin' => 'Admin / Segreteria',
    'docente' => 'Docente',
    'corsista' => 'Corsista',
    default => 'Utente',
};
?>
<nav class="navbar navbar-expand-lg ff-topbar sticky-top border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/dashboard.php">
            <img src="/assets/img/logo1.png" alt="FormaFlow" class="ff-brand-logo" onerror="this.style.display='none'">
            <span class="ff-brand-title"><?= e(APP_NAME) ?></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#ffTopbarUserArea" aria-controls="ffTopbarUserArea" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="ffTopbarUserArea">
            <div class="d-flex align-items-center gap-3 py-2 py-lg-0">
                <div class="text-end">
                    <div class="small text-muted mb-0"><?= e($roleLabel) ?></div>
                    <div class="fw-semibold small"><?= e($_SESSION['full_name'] ?? 'Utente') ?></div>
                </div>
                <a href="/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>
