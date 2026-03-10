<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Calendario Lezioni';

$from = trim((string)($_GET['from'] ?? date('Y-m-01')));
$to = trim((string)($_GET['to'] ?? date('Y-m-t')));
$courseId = (int)($_GET['course_id'] ?? 0);
$status = trim((string)($_GET['stato'] ?? ''));

$courses = $pdo->query('SELECT id, titolo FROM courses ORDER BY titolo')->fetchAll();

$sql = 'SELECT l.*, c.titolo AS corso_titolo, CONCAT(u.cognome," ",u.nome) AS docente_nome
        FROM lessons l
        JOIN courses c ON c.id=l.course_id
        JOIN users u ON u.id=l.docente_id
        WHERE l.data_lezione BETWEEN :from AND :to';
$params = ['from' => $from, 'to' => $to];

if ($courseId > 0) {
    $sql .= ' AND l.course_id = :course_id';
    $params['course_id'] = $courseId;
}
if ($status !== '') {
    $sql .= ' AND l.stato = :stato';
    $params['stato'] = $status;
}

$sql .= ' ORDER BY l.data_lezione, l.ora_inizio';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$stats = [
    'totale' => count($rows),
    'attive' => 0,
    'bozza' => 0,
    'rimandata' => 0,
    'durata' => 0,
];

foreach ($rows as $r) {
    $st = (string)($r['stato'] ?? '');
    if ($st === 'attiva') {
        $stats['attive']++;
    }
    if ($st === 'bozza') {
        $stats['bozza']++;
    }
    if ($st === 'rimandata') {
        $stats['rimandata']++;
    }

    if (!empty($r['ora_inizio']) && !empty($r['ora_fine'])) {
        $start = strtotime((string)$r['ora_inizio']);
        $end = strtotime((string)$r['ora_fine']);
        if ($start && $end && $end > $start) {
            $stats['durata'] += (int)(($end - $start) / 60);
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>

<div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Lezioni nel periodo</div><div class="h4 mb-0"><?= (int)$stats['totale'] ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Lezioni attive</div><div class="h4 mb-0 text-success"><?= (int)$stats['attive'] ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Bozze / Rimandate</div><div class="h4 mb-0 text-warning"><?= (int)($stats['bozza'] + $stats['rimandata']) ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Durata pianificata</div><div class="h4 mb-0"><?= (int)round($stats['durata'] / 60, 1) ?> h</div></div></div></div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form class="row g-2 align-items-end" id="calendarFilters">
            <div class="col-md-2"><label class="form-label">Dal</label><input type="date" class="form-control" name="from" value="<?= e($from) ?>"></div>
            <div class="col-md-2"><label class="form-label">Al</label><input type="date" class="form-control" name="to" value="<?= e($to) ?>"></div>
            <div class="col-md-3"><label class="form-label">Corso</label><select class="form-select" name="course_id"><option value="">Tutti i corsi</option><?php foreach($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $courseId===(int)$c['id']?'selected':'' ?>><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><label class="form-label">Stato</label><select class="form-select" name="stato"><option value="">Tutti</option><option value="attiva" <?= $status==='attiva'?'selected':'' ?>>Attiva</option><option value="da_fare" <?= $status==='da_fare'?'selected':'' ?>>Da fare</option><option value="bozza" <?= $status==='bozza'?'selected':'' ?>>Bozza</option><option value="rimandata" <?= $status==='rimandata'?'selected':'' ?>>Rimandata</option></select></div>
            <div class="col-md-3"><label class="form-label">Ricerca rapida</label><input type="search" class="form-control" id="calendarSearch" placeholder="Titolo, corso, docente..."></div>
            <div class="col-12 d-flex gap-2"><button class="btn btn-primary">Aggiorna calendario</button><a class="btn btn-outline-secondary" href="/admin/calendario.php">Reset</a></div>
        </form>
    </div>
</div>

<div class="row g-3" id="calendarCards">
<?php if(!$rows): ?><div class="col-12"><div class="alert alert-info">Nessuna lezione nel range selezionato.</div></div><?php endif; ?>
<?php foreach($rows as $r):
$waText = urlencode('Ciao, ricordati che oggi hai lezione. Ecco il link della lezione: '.($r['google_meet_link']?:''));
$waDocente = 'https://wa.me/?text=' . $waText;
$waCorsisti = 'https://wa.me/?text=' . urlencode('Promemoria per i corsisti del corso ' . $r['corso_titolo'] . '. Link lezione: ' . ($r['google_meet_link']?:''));
?>
<div class="col-md-6 col-xl-4 calendar-item" data-search="<?= e(strtolower($r['titolo'].' '.$r['corso_titolo'].' '.$r['docente_nome'])) ?>">
    <div class="card shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="small text-muted"><?= e($r['data_lezione']) ?> · <?= e(substr((string)$r['ora_inizio'],0,5)) ?> - <?= e(substr((string)$r['ora_fine'],0,5)) ?></div>
                <span class="badge text-bg-secondary"><?= e((string)$r['stato']) ?></span>
            </div>
            <h6 class="mb-1"><?= e((string)$r['titolo']) ?></h6>
            <div class="small mb-2"><?= e((string)$r['corso_titolo']) ?> · <?= e((string)$r['docente_nome']) ?></div>
            <div class="d-flex gap-2 flex-wrap">
                <?php if(!empty($r['google_meet_link'])): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="<?= e((string)$r['google_meet_link']) ?>">Apri Meet</a><?php endif; ?>
                <a class="btn btn-sm btn-outline-success" target="_blank" href="<?= e($waDocente) ?>">Condividi docente</a>
                <a class="btn btn-sm btn-outline-success" target="_blank" href="<?= e($waCorsisti) ?>">Condividi corsisti</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header">Timeline dettagliata</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0" id="calendarTable">
            <thead><tr><th>Data</th><th>Orario</th><th>Corso</th><th>Titolo</th><th>Docente</th><th>Stato</th><th>Meet</th></tr></thead>
            <tbody>
                <?php if(!$rows): ?><tr><td colspan="7" class="text-center text-muted py-4">Nessun dato disponibile.</td></tr><?php endif; ?>
                <?php foreach($rows as $r): ?>
                <tr class="calendar-item" data-search="<?= e(strtolower($r['titolo'].' '.$r['corso_titolo'].' '.$r['docente_nome'])) ?>">
                    <td><?= e((string)$r['data_lezione']) ?></td>
                    <td><?= e(substr((string)$r['ora_inizio'],0,5)) ?> - <?= e(substr((string)$r['ora_fine'],0,5)) ?></td>
                    <td><?= e((string)$r['corso_titolo']) ?></td>
                    <td><?= e((string)$r['titolo']) ?></td>
                    <td><?= e((string)$r['docente_nome']) ?></td>
                    <td><span class="badge text-bg-secondary"><?= e((string)$r['stato']) ?></span></td>
                    <td><?php if(!empty($r['google_meet_link'])): ?><a target="_blank" href="<?= e((string)$r['google_meet_link']) ?>">Link</a><?php else: ?>-<?php endif; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</main></div></div>
<script src="/assets/js/calendar.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
