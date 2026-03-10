<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1"><?= e(APP_NAME) ?></span>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted"><?= e($_SESSION['full_name'] ?? 'Utente') ?></span>
            <a href="/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>
