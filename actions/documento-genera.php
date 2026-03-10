<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$protocolloId = (int)($_POST['protocollo_id'] ?? 0);
$studentProfileId = (int)($_POST['student_profile_id'] ?? 0);
$title = trim((string)($_POST['titolo'] ?? 'Documento amministrativo'));
$content = trim((string)($_POST['contenuto'] ?? ''));
$categoryId = (int)($_POST['categoria_id'] ?? 0);
$stampType = trim((string)($_POST['stamp_type'] ?? 'timbro_firma'));

if ($title === '' || $content === '' || $categoryId <= 0) {
    flash('danger', 'Compila titolo, contenuto e categoria documento.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/protocollo.php'));
    exit;
}

try {
    $dir = ROOT_PATH . '/uploads/documenti-generati';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Impossibile creare la cartella documenti generati.');
    }

    $code = 'DOC-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    $baseName = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($title));
    $baseName = trim($baseName ?: 'documento', '-');
    $pdfName = $baseName . '_' . $code . '.pdf';
    $htmlName = $baseName . '_' . $code . '.html';

    $stampFile = ($stampType === 'timbro') ? 'Timbro Desiste.jpg' : 'Timbro con Firma Desiste.jpg';
    $stampPath = ROOT_PATH . '/assets/stamps/' . $stampFile;

    $html = '<html><head><meta charset="UTF-8"><style>'
        . 'body{font-family:Arial,sans-serif;color:#0f172a;padding:40px}'
        . '.title{font-size:24px;font-weight:700;margin-bottom:4px}'
        . '.meta{color:#64748b;margin-bottom:20px}'
        . '.content{line-height:1.6;white-space:pre-wrap;border:1px solid #cbd5e1;padding:16px;border-radius:8px}'
        . '.footer{margin-top:48px;display:flex;justify-content:space-between;align-items:flex-end}'
        . '</style></head><body>'
        . '<div class="title">' . e($title) . '</div>'
        . '<div class="meta">Codice documento: ' . e($code) . ' · Data: ' . date('Y-m-d H:i') . '</div>'
        . '<div class="content">' . e($content) . '</div>'
        . '<div class="footer"><div>Autore: ' . e((string)($_SESSION['user_name'] ?? 'Admin')) . '</div><div>'
        . (is_file($stampPath) ? '<img src="' . $stampPath . '" style="height:80px">' : '<em>Timbro/Firma non disponibile</em>')
        . '</div></div></body></html>';

    file_put_contents($dir . '/' . $htmlName, $html);

    $text = str_replace(['(', ')', "\n", "\r"], [' ', ' ', ' ', ' '], $title . ' | ' . $content);
    $stream = 'BT /F1 12 Tf 50 780 Td (' . addslashes($text) . ') Tj ET';
    $pdfContent = "%PDF-1.4\n"
        . "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
        . "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n"
        . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
        . "4 0 obj<</Length " . strlen($stream) . ">>stream\n" . $stream . "\nendstream endobj\n"
        . "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
        . "xref\n0 6\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000117 00000 n \n0000000260 00000 n \n0000000415 00000 n \n"
        . "trailer<</Size 6/Root 1 0 R>>\nstartxref\n490\n%%EOF";

    file_put_contents($dir . '/' . $pdfName, $pdfContent);

    $stampIdStmt = $pdo->prepare('SELECT id FROM stamps_signatures WHERE tipo=:tipo AND attivo=1 ORDER BY id DESC LIMIT 1');
    $stampIdStmt->execute(['tipo' => $stampType === 'timbro' ? 'timbro' : 'timbro_firma']);
    $stampId = (int)($stampIdStmt->fetchColumn() ?: 0);

    $stmt = $pdo->prepare('INSERT INTO generated_documents (categoria_id, protocollo_id, student_profile_id, titolo, file_path, stamp_signature_id, creato_da)
                           VALUES (:categoria_id,:protocollo_id,:student_profile_id,:titolo,:file_path,:stamp_signature_id,:creato_da)');
    $stmt->execute([
        'categoria_id' => $categoryId,
        'protocollo_id' => $protocolloId > 0 ? $protocolloId : null,
        'student_profile_id' => $studentProfileId > 0 ? $studentProfileId : null,
        'titolo' => $title,
        'file_path' => 'uploads/documenti-generati/' . $pdfName,
        'stamp_signature_id' => $stampId > 0 ? $stampId : null,
        'creato_da' => (int)$_SESSION['user_id'],
    ]);

    flash('success', 'Documento generato e archiviato con codice ' . $code . '.');
} catch (Throwable $e) {
    flash('danger', 'Errore durante la generazione documento: ' . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/protocollo.php'));
exit;
