<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente']);
$pageTitle = 'Dashboard Docente';
$userId = (int)$_SESSION['user_id'];

$stats = [
    'corsi_assegnati' => 0,
    'ore_assegnate' => 0,
    'ore_svolte' => 0,
    'lezioni_future' => 0,
    'materiali_caricati' => 0,
];

$stmt = $pdo->prepare('SELECT COUNT(*) FROM courses c WHERE EXISTS (SELECT 1 FROM lessons l WHERE l.course_id=c.id AND l.docente_id=:uid)');
$stmt->execute(['uid' => $userId]);
$stats['corsi_assegnati'] = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COALESCE(ore_assegnate,0) AS ore_assegnate, COALESCE(ore_svolte,0) AS ore_svolte FROM teacher_profiles WHERE user_id=:uid LIMIT 1');
$stmt->execute(['uid' => $userId]);
$profile = $stmt->fetch();
if ($profile) {
    $stats['ore_assegnate'] = (float)$profile['ore_assegnate'];
    $stats['ore_svolte'] = (float)$profile['ore_svolte'];
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM lessons WHERE docente_id=:uid AND data_lezione >= CURDATE()');
$stmt->execute(['uid' => $userId]);
$stats['lezioni_future'] = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM materials WHERE uploaded_by=:uid');
$stmt->execute(['uid' => $userId]);
$stats['materiali_caricati'] = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare('SELECT l.*, c.titolo AS corso_titolo
                       FROM lessons l
                       JOIN courses c ON c.id=l.course_id
                       WHERE l.docente_id=:uid AND l.data_lezione >= CURDATE()
                       ORDER BY l.data_lezione, l.ora_inizio
                       LIMIT 10');
$stmt->execute(['uid' => $userId]);
$nextLessons = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-docente.php'; ?></div>
<main class="col-lg-10 p-4">
    <?php require __DIR__ . '/../includes/page-header.php'; ?>
    <?php require __DIR__ . '/../includes/alerts.php'; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-xl-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Corsi assegnati</div><div class="h4 mb-0"><?= (int)$stats['corsi_assegnati'] ?></div></div></div></div>
        <div class="col-md-4 col-xl-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Ore assegnate</div><div class="h4 mb-0"><?= e((string)$stats['ore_assegnate']) ?></div></div></div></div>
        <div class="col-md-4 col-xl-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Ore svolte</div><div class="h4 mb-0"><?= e((string)$stats['ore_svolte']) ?></div></div></div></div>
        <div class="col-md-4 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Lezioni future</div><div class="h4 mb-0"><?= (int)$stats['lezioni_future'] ?></div></div></div></div>
        <div class="col-md-4 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Materiali caricati</div><div class="h4 mb-0"><?= (int)$stats['materiali_caricati'] ?></div></div></div></div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Prossime lezioni</div>
        <div class="table-responsive"><table class="table table-striped mb-0">
            <thead><tr><th>Data</th><th>Orario</th><th>Corso</th><th>Titolo</th><th>Meet</th></tr></thead>
            <tbody>
            <?php if (!$nextLessons): ?><tr><td colspan="5" class="text-center text-muted py-4">Nessuna lezione pianificata.</td></tr><?php endif; ?>
            <?php foreach ($nextLessons as $l): ?>
                <tr>
                    <td><?= e($l['data_lezione']) ?></td>
                    <td><?= e(substr($l['ora_inizio'], 0, 5) . ' - ' . substr($l['ora_fine'], 0, 5)) ?></td>
                    <td><?= e($l['corso_titolo']) ?></td>
                    <td><?= e($l['titolo']) ?></td>
                    <td><?php if ($l['google_meet_link']): ?><a target="_blank" href="<?= e($l['google_meet_link']) ?>">Apri link</a><?php else: ?>-<?php endif; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
