<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$numeroProtocollo = trim((string)($_POST['numero_protocollo'] ?? ''));
$dataProtocollo = trim((string)($_POST['data_protocollo'] ?? ''));
$tipologia = trim((string)($_POST['tipologia'] ?? ''));
$stato = trim((string)($_POST['stato'] ?? 'bozza'));
$mittenteTipo = trim((string)($_POST['mittente_tipo'] ?? ''));
$oggetto = trim((string)($_POST['oggetto'] ?? ''));
$contenuto = trim((string)($_POST['contenuto'] ?? ''));
$destinatari = $_POST['destinatari'] ?? [];

if ($numeroProtocollo === '' || $dataProtocollo === '' || $tipologia === '' || $mittenteTipo === '' || $oggetto === '' || $contenuto === '' || !is_array($destinatari) || !$destinatari) {
    flash('danger', 'Compila tutti i campi obbligatori del protocollo.');
    header('Location: /admin/protocollo-new.php');
    exit;
}

$year = (int)substr($numeroProtocollo, 0, 4);
$parts = explode('/', $numeroProtocollo);
$progressivo = isset($parts[1]) ? (int)$parts[1] : 0;

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO protocollo (anno, progressivo, numero_protocollo, oggetto, contenuto, data_protocollo, mittente_tipo, stato, tipologia, autore_id)
                           VALUES (:anno,:progressivo,:numero,:oggetto,:contenuto,:data_protocollo,:mittente,:stato,:tipologia,:autore)');
    $stmt->execute([
        'anno' => $year,
        'progressivo' => $progressivo,
        'numero' => $numeroProtocollo,
        'oggetto' => $oggetto,
        'contenuto' => $contenuto,
        'data_protocollo' => $dataProtocollo,
        'mittente' => $mittenteTipo,
        'stato' => $stato,
        'tipologia' => $tipologia,
        'autore' => (int)$_SESSION['user_id'],
    ]);
    $protocolloId = (int)$pdo->lastInsertId();

    $stmtDest = $pdo->prepare('INSERT INTO protocolli_destinatari (protocollo_id, destinatario_tipo, destinatario_label, inviato_il)
                               VALUES (:pid,:tipo,:label,:inviato_il)');
    foreach ($destinatari as $d) {
        $d = trim((string)$d);
        if ($d === '') { continue; }
        $stmtDest->execute([
            'pid' => $protocolloId,
            'tipo' => $d,
            'label' => $d,
            'inviato_il' => $stato === 'inviato' ? date('Y-m-d H:i:s') : null,
        ]);
    }

    if (!empty($_FILES['allegato_pdf']) && (int)$_FILES['allegato_pdf']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = securePdfUpload($_FILES['allegato_pdf'], ROOT_PATH . '/uploads/protocolli');
        $percorso = 'uploads/protocolli/' . basename($upload['path']);
        $stmt = $pdo->prepare('INSERT INTO protocol_attachments (protocollo_id, nome_originale, nome_salvato, percorso, mime_type, uploaded_by)
                               VALUES (:pid,:orig,:saved,:percorso,:mime,:uid)');
        $stmt->execute([
            'pid' => $protocolloId,
            'orig' => $upload['original_name'],
            'saved' => $upload['saved_name'],
            'percorso' => $percorso,
            'mime' => $upload['mime'],
            'uid' => (int)$_SESSION['user_id'],
        ]);
    }

    $pdo->commit();
    flash('success', 'Protocollo registrato con successo.');
    header('Location: /admin/protocollo-view.php?id=' . $protocolloId);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', 'Errore salvataggio protocollo: ' . $e->getMessage());
    header('Location: /admin/protocollo-new.php');
}
exit;
