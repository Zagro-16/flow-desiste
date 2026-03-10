<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Dashboard Admin';

$stats = [];
$queries = [
    'totale_corsi' => 'SELECT COUNT(*) FROM courses',
    'corsi_attivi' => "SELECT COUNT(*) FROM courses WHERE stato = 'attivo'",
    'totale_docenti' => "SELECT COUNT(*) FROM users WHERE role = 'docente'",
    'totale_corsisti' => "SELECT COUNT(*) FROM users WHERE role = 'corsista'",
    'protocolli' => 'SELECT COUNT(*) FROM protocollo',
    'documenti_pdf' => 'SELECT COUNT(*) FROM student_documents',
];

foreach ($queries as $key => $sql) {
    $stats[$key] = (int) $pdo->query($sql)->fetchColumn();
}

$lessons = $pdo->query("SELECT titolo, data_lezione, google_meet_link FROM lessons ORDER BY data_lezione ASC LIMIT 10")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
        <main class="col-lg-10 p-4">
            <?php require __DIR__ . '/../includes/page-header.php'; ?>
            <div class="row g-3 mb-4">
                <?php foreach ($stats as $label => $value): ?>
                    <div class="col-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted"><?= e(str_replace('_', ' ', ucfirst($label))) ?></div><div class="h4 mb-0"><?= e((string)$value) ?></div></div></div></div>
                <?php endforeach; ?>
            </div>
            <div class="card shadow-sm">
                <div class="card-header">Ultime lezioni</div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Titolo</th><th>Data</th><th>Google Meet</th></tr></thead>
                        <tbody>
                        <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td><?= e($lesson['titolo']) ?></td>
                                <td><?= e($lesson['data_lezione']) ?></td>
                                <td><a href="<?= e($lesson['google_meet_link']) ?>" target="_blank">Apri link</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
