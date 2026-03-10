<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$targetType = trim((string)($_POST['target_type'] ?? ''));
$courseId = (int)($_POST['course_id'] ?? 0);
$userId = (int)($_POST['user_id'] ?? 0);
$subject = trim((string)($_POST['subject'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($targetType === '' || $subject === '' || $message === '') {
    flash('danger', 'Compila tutti i campi obbligatori.');
    header('Location: /admin/comunicazioni.php');
    exit;
}

try {
    $recipients = [];
    if ($targetType === 'Docenti') {
        $stmt = $pdo->query("SELECT id,email FROM users WHERE role='docente' AND stato='attivo'");
        $recipients = $stmt->fetchAll();
    } elseif ($targetType === 'Corsisti') {
        $stmt = $pdo->query("SELECT id,email FROM users WHERE role='corsista' AND stato='attivo'");
        $recipients = $stmt->fetchAll();
    } elseif ($targetType === 'Corso' && $courseId > 0) {
        $stmt = $pdo->prepare('SELECT u.id,u.email FROM enrollments e JOIN users u ON u.id=e.student_id WHERE e.course_id=:cid AND e.stato="attivo"');
        $stmt->execute(['cid' => $courseId]);
        $recipients = $stmt->fetchAll();
    } elseif ($targetType === 'Utente' && $userId > 0) {
        $stmt = $pdo->prepare('SELECT id,email FROM users WHERE id=:id LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        $recipients = $row ? [$row] : [];
    } else {
        $stmt = $pdo->query("SELECT id,email FROM users WHERE stato='attivo'");
        $recipients = $stmt->fetchAll();
    }

    $pdo->beginTransaction();

    $commSql = 'INSERT INTO communications (target_type, target_course_id, target_user_id, subject, message, total_recipients, created_by)
                VALUES (:target_type,:target_course_id,:target_user_id,:subject,:message,:total_recipients,:created_by)';
    $stmt = $pdo->prepare($commSql);
    $stmt->execute([
        'target_type' => $targetType,
        'target_course_id' => $courseId > 0 ? $courseId : null,
        'target_user_id' => $userId > 0 ? $userId : null,
        'subject' => $subject,
        'message' => $message,
        'total_recipients' => count($recipients),
        'created_by' => (int)$_SESSION['user_id'],
    ]);

    $logStmt = $pdo->prepare('INSERT INTO email_logs (destinatario_email, oggetto, corpo, tipo_reminder) VALUES (:email,:oggetto,:corpo,"manuale")');
    $sent = 0;
    $failed = 0;
    foreach ($recipients as $r) {
        $email = trim((string)($r['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $failed++;
            continue;
        }

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8',
            'From: noreply@formaflow.local',
        ];

        $mailOk = @mail($email, $subject, $message, implode("\r\n", $headers));
        if ($mailOk) {
            $sent++;
        } else {
            $failed++;
        }

        $logStmt->execute([
            'email' => $email,
            'oggetto' => $subject,
            'corpo' => $message,
        ]);
    }

    $pdo->commit();
    flash('success', 'Comunicazione registrata. Invii OK: ' . $sent . ' · non inviati: ' . $failed . '.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', 'Errore invio comunicazione: ' . $e->getMessage());
}

header('Location: /admin/comunicazioni.php');
exit;
