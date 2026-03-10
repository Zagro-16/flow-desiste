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

$lessons = $pdo->query("SELECT l.titolo, l.data_lezione, l.ora_inizio, l.ora_fine, l.google_meet_link, c.titolo AS corso_titolo
                        FROM lessons l
                        JOIN courses c ON c.id=l.course_id
                        ORDER BY l.data_lezione ASC LIMIT 20")->fetchAll();

$latestComms = [];
try {
    $latestComms = $pdo->query("SELECT subject, target_type, created_at FROM communications ORDER BY created_at DESC LIMIT 8")->fetchAll();
} catch (Throwable) {
    $latestComms = [];
}

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
                    <div class="col-6 col-xl-2">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="small text-muted text-uppercase"><?= e(str_replace('_', ' ', ucfirst($label))) ?></div>
                                <div class="h4 mb-0"><?= e((string)$value) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">Azioni rapide</div>
                <div class="card-body d-flex flex-wrap gap-2">
                    <a class="btn btn-primary btn-sm" href="/admin/corso-new.php">+ Nuovo corso</a>
                    <a class="btn btn-outline-primary btn-sm" href="/admin/lezione-new.php">+ Nuova lezione</a>
                    <a class="btn btn-outline-primary btn-sm" href="/admin/docente-new.php">+ Nuovo docente</a>
                    <a class="btn btn-outline-primary btn-sm" href="/admin/corsista-new.php">+ Nuovo corsista</a>
                    <a class="btn btn-outline-secondary btn-sm" href="/admin/protocollo-new.php">+ Nuovo protocollo</a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-xl-8">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center gap-2">
                            <span>Prossime lezioni</span>
                            <input class="form-control form-control-sm w-auto" type="search" placeholder="Cerca" data-table-filter="#dashLessonsTable">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped mb-0" id="dashLessonsTable">
                                <thead><tr><th>Data</th><th>Corso</th><th>Titolo</th><th>Orario</th><th>Meet</th></tr></thead>
                                <tbody>
                                <?php foreach ($lessons as $lesson): ?>
                                    <tr>
                                        <td><?= e((string)$lesson['data_lezione']) ?></td>
                                        <td><?= e((string)$lesson['corso_titolo']) ?></td>
                                        <td><?= e((string)$lesson['titolo']) ?></td>
                                        <td><?= e(substr((string)$lesson['ora_inizio'], 0, 5)) ?> - <?= e(substr((string)$lesson['ora_fine'], 0, 5)) ?></td>
                                        <td><?php if (!empty($lesson['google_meet_link'])): ?><a href="<?= e((string)$lesson['google_meet_link']) ?>" target="_blank">Apri</a><?php else: ?>-<?php endif; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">Ultime comunicazioni</div>
                        <div class="list-group list-group-flush">
                            <?php if (!$latestComms): ?>
                                <div class="list-group-item text-muted">Nessuna comunicazione recente.</div>
                            <?php endif; ?>
                            <?php foreach ($latestComms as $comm): ?>
                                <div class="list-group-item">
                                    <div class="fw-semibold small"><?= e((string)$comm['subject']) ?></div>
                                    <div class="small text-muted"><?= e((string)$comm['target_type']) ?> · <?= e((string)$comm['created_at']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
