<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Impostazioni';

$keys = [
    'ente_nome' => 'Nome ente',
    'ente_email' => 'Email ente',
    'ente_telefono' => 'Telefono ente',
    'smtp_host' => 'SMTP Host',
    'smtp_port' => 'SMTP Porta',
    'smtp_user' => 'SMTP Utente',
    'smtp_pass' => 'SMTP Password',
    'pdf_default_layout' => 'Layout PDF predefinito',
    'stamp_default_type' => 'Timbro predefinito',
];

$stmt = $pdo->query('SELECT chiave, valore FROM settings');
$settingsRows = $stmt->fetchAll();
$settings = [];
foreach ($settingsRows as $r) {
    $settings[$r['chiave']] = (string)$r['valore'];
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>

<form action="/actions/settings-save.php" method="post" class="card shadow-sm">
<div class="card-header">Configurazione gestionale</div>
<div class="card-body"><div class="row g-3">
<?php foreach ($keys as $k => $label): ?>
<div class="col-md-4">
<label class="form-label" for="<?= e($k) ?>"><?= e($label) ?></label>
<input class="form-control" id="<?= e($k) ?>" name="settings[<?= e($k) ?>]" value="<?= e($settings[$k] ?? '') ?>">
</div>
<?php endforeach; ?>
</div></div>
<div class="card-footer"><button class="btn btn-primary">Salva impostazioni</button></div>
</form>

</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
