<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$settings = $_POST['settings'] ?? [];
if (!is_array($settings)) {
    flash('danger', 'Payload impostazioni non valido.');
    header('Location: /admin/impostazioni.php');
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO settings (chiave, valore) VALUES (:chiave,:valore)
                           ON DUPLICATE KEY UPDATE valore=VALUES(valore)');

    foreach ($settings as $key => $value) {
        $k = trim((string)$key);
        if ($k === '') {
            continue;
        }
        $v = trim((string)$value);
        $stmt->execute(['chiave' => $k, 'valore' => $v]);
    }

    $pdo->commit();
    flash('success', 'Impostazioni aggiornate con successo.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', 'Errore salvataggio impostazioni: ' . $e->getMessage());
}

header('Location: /admin/impostazioni.php');
exit;
