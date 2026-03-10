<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$studentId = (int)($_POST['student_id'] ?? 0);
$courseId = (int)($_POST['course_id'] ?? 0);
$stato = trim((string)($_POST['stato'] ?? 'attivo'));
if ($studentId <= 0 || $courseId <= 0 || !in_array($stato, ['attivo', 'ritirato', 'completato'], true)) {
    flash('danger', 'Dati iscrizione non validi.');
    header('Location: /admin/iscrizioni.php');
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO enrollments (course_id, student_id, stato) VALUES (:course_id,:student_id,:stato)
                           ON DUPLICATE KEY UPDATE stato=VALUES(stato)');
    $stmt->execute(['course_id' => $courseId, 'student_id' => $studentId, 'stato' => $stato]);
    flash('success', 'Iscrizione salvata correttamente.');
} catch (Throwable $e) {
    flash('danger', 'Errore salvataggio iscrizione: ' . $e->getMessage());
}

header('Location: /admin/iscrizioni.php');
exit;
