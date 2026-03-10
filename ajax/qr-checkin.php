<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin', 'docente']);
header('Content-Type: application/json; charset=utf-8');

$lessonId = (int)($_POST['lesson_id'] ?? 0);
$studentId = (int)($_POST['student_id'] ?? 0);

if ($lessonId <= 0 || $studentId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'Dati check-in non validi']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, course_id, docente_id FROM lessons WHERE id=:id LIMIT 1');
    $stmt->execute(['id' => $lessonId]);
    $lesson = $stmt->fetch();
    if (!$lesson) {
        throw new RuntimeException('Lezione non trovata');
    }

    if (($_SESSION['role'] ?? '') === 'docente' && (int)$lesson['docente_id'] !== (int)$_SESSION['user_id']) {
        throw new RuntimeException('Lezione non assegnata al docente corrente');
    }

    $stmt = $pdo->prepare('SELECT id FROM enrollments WHERE course_id=:course_id AND student_id=:student_id AND stato="attivo" LIMIT 1');
    $stmt->execute(['course_id' => (int)$lesson['course_id'], 'student_id' => $studentId]);
    if (!$stmt->fetch()) {
        throw new RuntimeException('Corsista non iscritto al corso della lezione');
    }

    $stmt = $pdo->prepare('INSERT INTO attendance (lesson_id, student_id, presente, checkin_qr_at)
                           VALUES (:lesson_id,:student_id,1,:checkin)
                           ON DUPLICATE KEY UPDATE presente=1, checkin_qr_at=VALUES(checkin_qr_at)');
    $stmt->execute(['lesson_id' => $lessonId, 'student_id' => $studentId, 'checkin' => date('Y-m-d H:i:s')]);

    echo json_encode(['ok' => true, 'message' => 'Check-in registrato con successo']);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
