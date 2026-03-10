<?php
require_once __DIR__ . '/../config/config.php';

$targetDates = [
    ['days' => 2, 'type' => '2_giorni'],
    ['days' => 1, 'type' => '1_giorno'],
];

foreach ($targetDates as $rule) {
    $date = (new DateTimeImmutable('today'))->modify('+' . $rule['days'] . ' day')->format('Y-m-d');

    $stmt = $pdo->prepare('SELECT l.id, l.titolo, l.data_lezione, l.google_meet_link, u.email AS docente_email FROM lessons l JOIN users u ON u.id=l.docente_id WHERE l.data_lezione = :d');
    $stmt->execute(['d' => $date]);

    foreach ($stmt->fetchAll() as $lesson) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM email_logs WHERE lesson_id = :lesson_id AND tipo_reminder = :tipo');
        $check->execute(['lesson_id' => $lesson['id'], 'tipo' => $rule['type']]);
        if ((int) $check->fetchColumn() > 0) {
            continue;
        }

        $body = sprintf('Reminder lezione %s del %s. Meet: %s', $lesson['titolo'], $lesson['data_lezione'], $lesson['google_meet_link']);

        $insert = $pdo->prepare('INSERT INTO email_logs (destinatario_email, oggetto, corpo, lesson_id, tipo_reminder) VALUES (:email,:oggetto,:corpo,:lesson_id,:tipo)');
        $insert->execute([
            'email' => $lesson['docente_email'],
            'oggetto' => 'Reminder lezione ' . $lesson['titolo'],
            'corpo' => $body,
            'lesson_id' => $lesson['id'],
            'tipo' => $rule['type'],
        ]);
    }
}

echo "Reminder process completed\n";
