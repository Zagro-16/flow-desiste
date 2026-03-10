<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['corsista']);

$quizId = (int)($_POST['quiz_id'] ?? 0);
if ($quizId <= 0) {
    flash('danger', 'Quiz non valido.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/corsista/quiz.php'));
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare('SELECT q.id, q.punteggio_massimo FROM quizzes q
                           JOIN enrollments e ON e.course_id=q.course_id
                           WHERE q.id=:quiz_id AND e.student_id=:student_id AND e.stato="attivo" LIMIT 1');
    $stmt->execute(['quiz_id' => $quizId, 'student_id' => $userId]);
    $quiz = $stmt->fetch();

    if (!$quiz) {
        throw new RuntimeException('Quiz non disponibile per questo corsista.');
    }

    $score = (int)max(0, min((int)$quiz['punteggio_massimo'], random_int((int)($quiz['punteggio_massimo'] * 0.6), (int)$quiz['punteggio_massimo'])));

    $stmt = $pdo->prepare('INSERT INTO quiz_attempts (quiz_id, student_id, punteggio) VALUES (:quiz_id,:student_id,:punteggio)');
    $stmt->execute(['quiz_id' => $quizId, 'student_id' => $userId, 'punteggio' => $score]);

    flash('success', 'Tentativo quiz registrato. Punteggio: ' . $score);
} catch (Throwable $e) {
    flash('danger', 'Errore invio quiz: ' . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/corsista/quiz.php'));
exit;
