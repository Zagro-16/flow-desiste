<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$pair = trim((string)($_POST['enrollment_pair'] ?? ''));
$stampType = trim((string)($_POST['stamp_type'] ?? 'timbro_firma'));
$issueDate = trim((string)($_POST['issue_date'] ?? date('Y-m-d')));
$modules = trim((string)($_POST['modules'] ?? ''));
$skills = trim((string)($_POST['skills'] ?? ''));
if ($pair === '' || !str_contains($pair, ':')) {
    flash('danger', 'Seleziona corsista e corso per l\'attestato.');
    header('Location: /admin/attestati.php');
    exit;
}
[$studentId, $courseId] = array_map('intval', explode(':', $pair, 2));
if ($studentId <= 0 || $courseId <= 0) {
    flash('danger', 'Pair corsista/corso non valido.');
    header('Location: /admin/attestati.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT sp.*, c.titolo AS corso_titolo, c.codice AS corso_codice, c.monte_ore_totali
                           FROM student_profiles sp
                           JOIN users u ON u.id=sp.user_id
                           JOIN enrollments e ON e.student_id=u.id
                           JOIN courses c ON c.id=e.course_id
                           WHERE u.id=:student_id AND c.id=:course_id LIMIT 1');
    $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
    $data = $stmt->fetch();
    if (!$data) {
        throw new RuntimeException('Dati corsista/corso non trovati per attestato.');
    }

    $code = 'ATT-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    $fileName = 'attestato_' . $code . '.pdf';
    $htmlName = 'attestato_' . $code . '.html';
    $dir = ROOT_PATH . '/uploads/attestati';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Impossibile creare cartella attestati.');
    }

    $stampFile = ($stampType === 'timbro') ? 't.jpg' : 'tf.jpg';
    $stampPath = ROOT_PATH . '/assets/img/' . $stampFile;
    $logoPath = ROOT_PATH . '/assets/img/logo.png';

    $fullName = trim(($data['cognome'] ?? '') . ' ' . ($data['nome'] ?? ''));
    $bornAt = trim((string)($data['citta_nascita'] ?? ''));
    $bornDate = trim((string)($data['data_nascita'] ?? ''));
    $courseTitle = trim((string)($data['corso_titolo'] ?? ''));
    $courseCode = trim((string)($data['corso_codice'] ?? ''));
    $hours = (int)($data['monte_ore_totali'] ?? 0);

    $html = '<html><head><meta charset="UTF-8"><style>
        body{font-family:DejaVu Sans,Arial,sans-serif;padding:30px;color:#1f2937;background:#f8fafc}
        .sheet{background:#fff;border:3px solid #1e3a8a;border-radius:14px;padding:30px;}
        .title{text-align:center;font-size:30px;font-weight:bold;margin-top:16px;color:#0f172a;letter-spacing:1px}
        .subtitle{text-align:center;color:#64748b;margin-bottom:24px}
        .box{border:1px solid #cbd5e1;padding:20px;border-radius:10px}
        .row{margin:8px 0}
        .footer{margin-top:40px;display:flex;justify-content:space-between;align-items:flex-end}
        .sec-title{font-size:13px;font-weight:bold;color:#334155;margin-top:14px;text-transform:uppercase}
        ul{margin:8px 0 0 18px;padding:0}
    </style></head><body>
    <div class="sheet">
    <div style="text-align:center;">' . (is_file($logoPath) ? '<img src="' . $logoPath . '" style="height:60px;">' : '') . '</div>
    <div class="title">ATTESTATO DI FREQUENZA</div>
    <div class="subtitle">Codice attestato: ' . e($code) . '</div>
    <div class="box">
      <div class="row">Si certifica che <strong>' . e($fullName) . '</strong></div>
      <div class="row">nato/a a ' . e($bornAt) . ' il ' . e($bornDate) . '</div>
      <div class="row">ha completato con esito positivo il corso <strong>' . e($courseTitle) . '</strong> (' . e($courseCode) . ')</div>
      <div class="row">per un totale di ore frequentate: <strong>' . e((string)$hours) . '</strong></div>
      <div class="row">Data emissione: ' . e($issueDate) . '</div>
      ' . ($modules !== '' ? '<div class="sec-title">Moduli trattati</div><div>' . nl2br(e($modules)) . '</div>' : '') . '
      ' . ($skills !== '' ? '<div class="sec-title">Competenze acquisite</div><div>' . nl2br(e($skills)) . '</div>' : '') . '
    </div>
    <div class="footer"><div>Responsabile Ente</div><div>' . (is_file($stampPath) ? '<img src="' . $stampPath . '" style="height:80px;">' : '<em>Timbro/Firma non disponibile</em>') . '</div></div>
    </div>
    </body></html>';

    file_put_contents($dir . '/' . $htmlName, $html);

    $pdfContent = null;
    $autoloadPaths = [
        ROOT_PATH . '/vendor/autoload.php',
        ROOT_PATH . '/../vendor/autoload.php',
    ];
    foreach ($autoloadPaths as $autoload) {
        if (is_file($autoload)) {
            require_once $autoload;
            break;
        }
    }

    if (class_exists('Dompdf\\Dompdf')) {
        $dompdf = new Dompdf\Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();
    } else {
        $text = [
            'ATTESTATO DI FREQUENZA',
            'Codice: ' . $code,
            'Corsista: ' . $fullName,
            'Nato/a a ' . $bornAt . ' il ' . $bornDate,
            'Corso: ' . $courseTitle . ' (' . $courseCode . ')',
            'Ore frequentate: ' . $hours,
            'Data emissione: ' . $issueDate,
            $modules !== '' ? 'Moduli: ' . preg_replace('/\s+/', ' ', $modules) : null,
            $skills !== '' ? 'Competenze: ' . preg_replace('/\s+/', ' ', $skills) : null,
        ];
        $safeText = implode(' | ', array_filter($text));
        $safeText = str_replace(['(', ')'], '', $safeText);
        $stream = "BT /F1 12 Tf 50 780 Td (" . addslashes($safeText) . ") Tj ET";
        $pdfContent = "%PDF-1.4\n"
            . "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            . "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n"
            . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
            . "4 0 obj<</Length " . strlen($stream) . ">>stream\n" . $stream . "\nendstream endobj\n"
            . "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
            . "xref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000260 00000 n \n0000000415 00000 n \n"
            . "trailer<</Size 6/Root 1 0 R>>\nstartxref\n490\n%%EOF";
    }

    $full = $dir . '/' . $fileName;
    file_put_contents($full, $pdfContent);

    $relative = 'uploads/attestati/' . $fileName;

    $stampIdStmt = $pdo->prepare('SELECT id FROM stamps_signatures WHERE tipo=:tipo AND attivo=1 ORDER BY id DESC LIMIT 1');
    $stampIdStmt->execute(['tipo' => $stampType === 'timbro' ? 'timbro' : 'timbro_firma']);
    $stampId = (int)($stampIdStmt->fetchColumn() ?: 0);

    $stmt = $pdo->prepare('INSERT INTO certificates (codice_univoco, student_id, course_id, file_path, stamp_signature_id, generato_da)
                           VALUES (:codice,:student_id,:course_id,:file_path,:stamp_signature_id,:generato_da)');
    $stmt->execute([
        'codice' => $code,
        'student_id' => $studentId,
        'course_id' => $courseId,
        'file_path' => $relative,
        'stamp_signature_id' => $stampId > 0 ? $stampId : null,
        'generato_da' => (int)$_SESSION['user_id'],
    ]);

    flash('success', 'Attestato generato con codice ' . $code);
} catch (Throwable $e) {
    flash('danger', 'Errore generazione attestato: ' . $e->getMessage());
}

header('Location: /admin/attestati.php');
exit;
