<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['docente', 'admin']);

$lessonId = (int)($_POST['lesson_id'] ?? 0);
$presenti = $_POST['presenti'] ?? [];
if ($lessonId <= 0 || !is_array($presenti)) {
    flash('danger', 'Dati presenze non validi.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/docente/presenze.php'));
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT course_id, docente_id, ora_inizio, ora_fine, stato FROM lessons WHERE id=:id LIMIT 1');
    $stmt->execute(['id' => $lessonId]);
    $lesson = $stmt->fetch();
    if (!$lesson) {
        throw new RuntimeException('Lezione non trovata.');
    }

    if (($_SESSION['role'] ?? '') === 'docente' && (int)$lesson['docente_id'] !== (int)$_SESSION['user_id']) {
        throw new RuntimeException('Non autorizzato a registrare presenze su questa lezione.');
    }

    $stmt = $pdo->prepare('SELECT student_id FROM enrollments WHERE course_id=:course_id AND stato="attivo"');
    $stmt->execute(['course_id' => (int)$lesson['course_id']]);
    $students = array_map('intval', array_column($stmt->fetchAll(), 'student_id'));

    $presentSet = array_map('intval', $presenti);

    $upsert = $pdo->prepare('INSERT INTO attendance (lesson_id, student_id, presente, checkin_qr_at)
        VALUES (:lesson_id, :student_id, :presente, :checkin)
        ON DUPLICATE KEY UPDATE presente=VALUES(presente), checkin_qr_at=VALUES(checkin_qr_at)');

    foreach ($students as $sid) {
        $isPresent = in_array($sid, $presentSet, true);
        $upsert->execute([
            'lesson_id' => $lessonId,
            'student_id' => $sid,
            'presente' => $isPresent ? 1 : 0,
            'checkin' => $isPresent ? date('Y-m-d H:i:s') : null,
        ]);
    }

    $minutes = max(0, (int)((strtotime((string)$lesson['ora_fine']) - strtotime((string)$lesson['ora_inizio'])) / 60));
    $hours = round($minutes / 60, 2);

    if (($lesson['stato'] ?? '') !== 'svolta') {
        $stmt = $pdo->prepare('UPDATE lessons SET stato="svolta" WHERE id=:id');
        $stmt->execute(['id' => $lessonId]);

        $stmt = $pdo->prepare('UPDATE teacher_profiles SET ore_svolte = COALESCE(ore_svolte,0) + :h WHERE user_id=:uid');
        $stmt->execute(['h' => $hours, 'uid' => (int)$lesson['docente_id']]);
    }

    $pdo->commit();
    flash('success', 'Presenze registrate correttamente.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', 'Errore salvataggio presenze: ' . $e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/docente/presenze.php'));
exit;
