<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$studentProfileId = (int)($_POST['student_profile_id'] ?? 0);
$tipo = trim((string)($_POST['tipo'] ?? ''));
$dataEvento = trim((string)($_POST['data_evento'] ?? ''));
$motivazione = trim((string)($_POST['motivazione'] ?? ''));

if ($studentProfileId <= 0 || !in_array($tipo, ['rinuncia','desistenza'], true) || $dataEvento === '' || $motivazione === '') {
    flash('danger', 'Dati rinuncia/desistenza non validi.');
    header('Location: /admin/rinunce.php');
    exit;
}

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO withdrawals_or_renunciations (student_profile_id, tipo, data_evento, motivazione, registrato_da)
                           VALUES (:sid,:tipo,:data_evento,:motivazione,:uid)');
    $stmt->execute([
        'sid' => $studentProfileId,
        'tipo' => $tipo,
        'data_evento' => $dataEvento,
        'motivazione' => $motivazione,
        'uid' => (int)$_SESSION['user_id'],
    ]);

    $newStatus = $tipo === 'rinuncia' ? 'rinunciatario' : 'desistente';
    $stmt = $pdo->prepare('UPDATE student_profiles SET stato_corsista = :stato, data_eventuale_rinuncia = :data_evento, motivazione_rinuncia = :motivazione WHERE id = :sid');
    $stmt->execute(['stato' => $newStatus, 'data_evento' => $dataEvento, 'motivazione' => $motivazione, 'sid' => $studentProfileId]);

    $pdo->commit();
    flash('success', 'Evento registrato correttamente.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', 'Errore registrazione evento: ' . $e->getMessage());
}

header('Location: /admin/rinunce.php');
exit;
