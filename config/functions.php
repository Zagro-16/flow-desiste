<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function sanitizeFileName(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name) ?? 'file';
    return strtolower(trim($name, '_'));
}

function uniqueDocumentName(string $prefix, string $originalName): string
{
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return sprintf('%s_%s.%s', $prefix, bin2hex(random_bytes(8)), $ext ?: 'pdf');
}

function securePdfUpload(array $file, string $targetDirectory, int $maxMb = 10): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload non valido.');
    }

    $maxBytes = $maxMb * 1024 * 1024;
    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('File troppo grande.');
    }

    $mimeType = mime_content_type($file['tmp_name']);
    if ($mimeType !== 'application/pdf') {
        throw new RuntimeException('È consentito solo il formato PDF.');
    }

    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('Impossibile creare directory upload.');
    }

    $savedName = uniqueDocumentName('pdf', $file['name']);
    $destination = rtrim($targetDirectory, '/') . '/' . $savedName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Errore salvataggio file.');
    }

    return [
        'original_name' => $file['name'],
        'saved_name' => $savedName,
        'path' => $destination,
        'mime' => $mimeType,
        'size' => $file['size'],
    ];
}

function nextProtocolNumber(PDO $pdo): string
{
    $year = date('Y');
    $stmt = $pdo->prepare('SELECT COALESCE(MAX(progressivo), 0) + 1 FROM protocollo WHERE anno = :anno');
    $stmt->execute(['anno' => $year]);
    $progressivo = (int) $stmt->fetchColumn();
    return sprintf('%s/%05d', $year, $progressivo);
}

function chooseStampForDocument(string $documentType, string $status = 'draft'): string
{
    $signedTypes = ['attestato', 'rinuncia', 'protocollo_firmato'];
    $signed = in_array($documentType, $signedTypes, true) || $status === 'firmato';
    return $signed ? STAMP_PATH . '/tf.jpg' : STAMP_PATH . '/t.jpg';
}

function exportHeaders(string $filename, string $mime): void
{
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function normalizeWithdrawalState(?string $state): string
{
    $allowed = ['attivo', 'rinunciatario', 'desistente', 'completato'];
    return in_array($state, $allowed, true) ? $state : 'attivo';
}

function current_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);
    return is_string($path) ? $path : '';
}

function nav_is_active(string $path): string
{
    return current_path() === $path ? 'active' : '';
}
