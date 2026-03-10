<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');

$studentProfileId = (int)($_GET['student_profile_id'] ?? 0);
if ($studentProfileId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'student_profile_id non valido']);
    exit;
}

if (($_SESSION['role'] ?? '') === 'corsista') {
    $chk = $pdo->prepare('SELECT id FROM student_profiles WHERE id=:id AND user_id=:uid LIMIT 1');
    $chk->execute(['id' => $studentProfileId, 'uid' => (int)$_SESSION['user_id']]);
    if (!$chk->fetch()) {
        echo json_encode(['ok' => false, 'message' => 'Non autorizzato']);
        exit;
    }
}

$stmt = $pdo->prepare('SELECT id, tipologia, nome_originale, nome_salvato, percorso, data_upload FROM student_documents WHERE student_profile_id=:id ORDER BY data_upload DESC');
$stmt->execute(['id' => $studentProfileId]);

echo json_encode(['ok' => true, 'data' => $stmt->fetchAll()]);
