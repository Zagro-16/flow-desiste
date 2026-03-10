<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$pair = trim((string)($_POST['enrollment_pair'] ?? ''));
$stampType = trim((string)($_POST['stamp_type'] ?? 'timbro_firma'));
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
    $dir = ROOT_PATH . '/uploads/attestati';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Impossibile creare cartella attestati.');
    }

    $stampFile = ($stampType === 'timbro') ? 'Timbro Desiste.jpg' : 'Timbro con Firma Desiste.jpg';
    $stampPath = 'assets/stamps/' . $stampFile;

    $html = '<html><head><meta charset="UTF-8"><style>
        body{font-family:DejaVu Sans,Arial,sans-serif;padding:30px;color:#1f2937}
        .title{text-align:center;font-size:28px;font-weight:bold;margin-top:20px}
        .subtitle{text-align:center;color:#6b7280;margin-bottom:30px}
        .box{border:1px solid #ddd;padding:20px;border-radius:8px}
        .row{margin:8px 0}
        .footer{margin-top:50px;display:flex;justify-content:space-between;align-items:center}
    </style></head><body>
    <div style="text-align:center;"><img src="' . ROOT_PATH . '/assets/img/logo1.png" style="height:60px;"></div>
    <div class="title">ATTESTATO DI FREQUENZA</div>
    <div class="subtitle">Codice attestato: ' . e($code) . '</div>
    <div class="box">
      <div class="row">Si certifica che <strong>' . e($data['cognome'] . ' ' . $data['nome']) . '</strong></div>
      <div class="row">nato/a a ' . e($data['citta_nascita']) . ' il ' . e((string)$data['data_nascita']) . '</div>
      <div class="row">ha frequentato il corso <strong>' . e($data['corso_titolo']) . '</strong> (' . e($data['corso_codice']) . ')</div>
      <div class="row">per un totale di ore: <strong>' . e((string)$data['monte_ore_totali']) . '</strong></div>
      <div class="row">Data emissione: ' . date('Y-m-d') . '</div>
    </div>
    <div class="footer"><div>Responsabile Ente</div><div><img src="' . ROOT_PATH . '/' . $stampPath . '" style="height:80px;"></div></div>
    </body></html>';

    $pdfContent = "%PDF-1.4\n" .
        "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n" .
        "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n" .
        "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n" .
        "4 0 obj<</Length 100>>stream\nBT /F1 18 Tf 50 780 Td (Attestato " . str_replace(['(', ')'], '', $code) . ") Tj ET\nendstream endobj\n" .
        "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n" .
        "xref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000260 00000 n \n0000000415 00000 n \n" .
        "trailer<</Size 6/Root 1 0 R>>\nstartxref\n490\n%%EOF";

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
