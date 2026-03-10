<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');

$teacherId = (int)($_GET['teacher_id'] ?? $_SESSION['user_id']);
if(($_SESSION['role'] ?? '')==='docente'){ $teacherId=(int)$_SESSION['user_id']; }
$stmt=$pdo->prepare('SELECT u.id, u.nome, u.cognome, tp.ore_assegnate, tp.ore_svolte,
(SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, l.ora_inizio, l.ora_fine))/60,0) FROM lessons l WHERE l.docente_id=u.id AND l.stato="svolta") AS ore_da_lezioni
FROM users u LEFT JOIN teacher_profiles tp ON tp.user_id=u.id WHERE u.id=:id AND u.role="docente" LIMIT 1');
$stmt->execute(['id'=>$teacherId]);
$data=$stmt->fetch();
echo json_encode(['ok'=> (bool)$data, 'data'=>$data]);
