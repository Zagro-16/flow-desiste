<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$courseId = (int)($_POST['course_id'] ?? 0);
$titolo = trim((string)($_POST['titolo'] ?? ''));
$punteggioMassimo = (int)($_POST['punteggio_massimo'] ?? 100);
$questionsBlob = trim((string)($_POST['questions_blob'] ?? ''));

if ($courseId <= 0 || $titolo === '' || $questionsBlob === '') {
    flash('danger', 'Compila i campi obbligatori del quiz.');
    header('Location: /admin/quiz-new.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('INSERT INTO quizzes (course_id, titolo, punteggio_massimo) VALUES (:course_id,:titolo,:punteggio_massimo)');
    $stmt->execute(['course_id' => $courseId, 'titolo' => $titolo, 'punteggio_massimo' => $punteggioMassimo]);
    $quizId = (int)$pdo->lastInsertId();

    $qStmt = $pdo->prepare('INSERT INTO quiz_questions (quiz_id, domanda, opzioni_json, risposta_corretta) VALUES (:quiz_id,:domanda,:opzioni_json,:risposta_corretta)');
    $lines = preg_split('/\r\n|\r|\n/', $questionsBlob) ?: [];
    $inserted = 0;
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $parts = explode('|', $line);
        if (count($parts) !== 3) continue;
        [$domanda, $opt, $corretta] = $parts;
        $opts = array_values(array_filter(array_map('trim', explode(';', $opt)), fn($v) => $v !== ''));
        if (!$opts) continue;
        $qStmt->execute([
            'quiz_id' => $quizId,
            'domanda' => trim($domanda),
            'opzioni_json' => json_encode($opts, JSON_UNESCAPED_UNICODE),
            'risposta_corretta' => trim($corretta),
        ]);
        $inserted++;
    }

    if ($inserted === 0) {
        throw new RuntimeException('Nessuna domanda valida trovata nel formato inserito.');
    }

    $pdo->commit();
    flash('success', 'Quiz creato con ' . $inserted . ' domande.');
    header('Location: /admin/quiz.php');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    flash('danger', 'Errore salvataggio quiz: ' . $e->getMessage());
    header('Location: /admin/quiz-new.php');
}
exit;
