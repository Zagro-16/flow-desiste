<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente', 'admin']);

$courseId = (int)($_POST['course_id'] ?? 0);
$titolo = trim((string)($_POST['titolo'] ?? ''));
$file = $_FILES['material_file'] ?? null;

if ($courseId <= 0 || $titolo === '' || !$file) {
    flash('danger', 'Compila tutti i campi del materiale.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/docente/materiali.php'));
    exit;
}

$allowed = [
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'application/vnd.ms-powerpoint',
    'image/png',
    'image/jpeg',
    'text/plain',
    'application/zip',
];

try {
    if ((int)$file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Errore upload file materiale.');
    }

    $mime = mime_content_type((string)$file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('Formato file non consentito.');
    }

    $max = 20 * 1024 * 1024;
    if ((int)$file['size'] > $max) {
        throw new RuntimeException('File troppo grande (max 20MB).');
    }

    $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
    $saved = uniqueDocumentName('materiale', (string)$file['name']);
    $dir = ROOT_PATH . '/uploads/materiali';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        throw new RuntimeException('Impossibile creare cartella upload materiali.');
    }

    $fullPath = $dir . '/' . $saved;
    if (!move_uploaded_file((string)$file['tmp_name'], $fullPath)) {
        throw new RuntimeException('Errore salvataggio file materiale.');
    }

    $relative = 'uploads/materiali/' . $saved;

    $stmt = $pdo->prepare('INSERT INTO materials (course_id, titolo, file_path, uploaded_by) VALUES (:course_id,:titolo,:file_path,:uploaded_by)');
    $stmt->execute([
        'course_id' => $courseId,
        'titolo' => $titolo,
        'file_path' => $relative,
        'uploaded_by' => (int)$_SESSION['user_id'],
    ]);

    flash('success', 'Materiale caricato con successo.');
} catch (Throwable $e) {
    flash('danger', 'Errore caricamento materiale: ' . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/docente/materiali.php'));
exit;
