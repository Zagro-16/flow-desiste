<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$studentProfileId = (int)($_POST['student_profile_id'] ?? 0);
$tipologia = trim((string)($_POST['tipologia'] ?? ''));
$allowed = ['Documento identità','Codice fiscale','Curriculum','Contratto','Domanda iscrizione','Privacy','Altro'];

if ($studentProfileId <= 0 || !in_array($tipologia, $allowed, true) || !isset($_FILES['pdf_file'])) {
    flash('danger', 'Dati upload non validi.');
    header('Location: /admin/corsisti.php');
    exit;
}

try {
    $uploadData = securePdfUpload($_FILES['pdf_file'], ROOT_PATH . '/uploads/anagrafica');
    $relativePath = 'uploads/anagrafica/' . basename($uploadData['path']);

    $stmt = $pdo->prepare('INSERT INTO student_documents (student_profile_id, tipologia, nome_originale, nome_salvato, percorso, uploaded_by)
                           VALUES (:sid, :tipologia, :orig, :saved, :percorso, :uid)');
    $stmt->execute([
        'sid' => $studentProfileId,
        'tipologia' => $tipologia,
        'orig' => $uploadData['original_name'],
        'saved' => $uploadData['saved_name'],
        'percorso' => $relativePath,
        'uid' => (int)$_SESSION['user_id'],
    ]);

    flash('success', 'PDF caricato correttamente.');
} catch (Throwable $e) {
    flash('danger', 'Errore upload PDF: ' . $e->getMessage());
}

header('Location: /admin/corsista-documenti.php?id=' . $studentProfileId);
exit;
