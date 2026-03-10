<?php
declare(strict_types=1);

$checks = [
    'PHP >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'Estensione PDO' => extension_loaded('pdo'),
    'Estensione pdo_mysql' => extension_loaded('pdo_mysql'),
    'Directory uploads scrivibile' => is_dir(__DIR__ . '/../uploads') && is_writable(__DIR__ . '/../uploads'),
    'database/schema.sql presente' => file_exists(__DIR__ . '/../database/schema.sql'),
    'database/seed.sql presente' => file_exists(__DIR__ . '/../database/seed.sql'),
    'database/fix-demo-passwords.sql presente' => file_exists(__DIR__ . '/../database/fix-demo-passwords.sql'),
];

$allOk = !in_array(false, $checks, true);
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup FormaFlow By Desiste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Setup FormaFlow By Desiste</h1>
                    <p class="text-muted">Questa procedura importa schema, seed e fix credenziali demo in modo automatizzato.</p>

                    <ul class="list-group mb-4">
                        <?php foreach ($checks as $label => $ok): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="badge <?= $ok ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $ok ? 'OK' : 'KO' ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!$allOk): ?>
                        <div class="alert alert-danger">Correggi i controlli KO prima di procedere con l'installazione.</div>
                    <?php endif; ?>

                    <form action="/setup/install.php" method="post" class="row g-3">
                        <div class="col-md-4"><label class="form-label">Host DB</label><input class="form-control" name="db_host" value="127.0.0.1" required></div>
                        <div class="col-md-4"><label class="form-label">Nome DB</label><input class="form-control" name="db_name" value="formaflow" required></div>
                        <div class="col-md-4"><label class="form-label">Porta</label><input class="form-control" name="db_port" value="3306" required></div>
                        <div class="col-md-6"><label class="form-label">Utente DB</label><input class="form-control" name="db_user" value="root" required></div>
                        <div class="col-md-6"><label class="form-label">Password DB</label><input class="form-control" type="password" name="db_pass" value=""></div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary" type="submit" <?= $allOk ? '' : 'disabled' ?>>Installa database</button>
                            <a class="btn btn-outline-secondary" href="/index.php">Vai al login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
