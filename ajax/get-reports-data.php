<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
header('Content-Type: application/json; charset=utf-8');

$report = trim((string)($_GET['report'] ?? 'overview'));
if($report==='overview'){
    $data = [
        'courses_by_state' => $pdo->query('SELECT stato, COUNT(*) AS totale FROM courses GROUP BY stato')->fetchAll(),
        'students_by_state' => $pdo->query('SELECT stato_corsista AS stato, COUNT(*) AS totale FROM student_profiles GROUP BY stato_corsista')->fetchAll(),
        'protocol_by_state' => $pdo->query('SELECT stato, COUNT(*) AS totale FROM protocollo GROUP BY stato')->fetchAll(),
    ];
    echo json_encode(['ok'=>true,'data'=>$data]);
    exit;
}
echo json_encode(['ok'=>false,'message'=>'Tipo report non supportato']);
