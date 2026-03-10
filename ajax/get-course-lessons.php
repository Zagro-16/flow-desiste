<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');

$courseId = (int)($_GET['course_id'] ?? 0);
if($courseId<=0){ echo json_encode(['ok'=>false,'message'=>'course_id non valido']); exit; }
$stmt=$pdo->prepare('SELECT id,titolo,data_lezione,ora_inizio,ora_fine,google_meet_link,stato FROM lessons WHERE course_id=:course_id ORDER BY data_lezione,ora_inizio');
$stmt->execute(['course_id'=>$courseId]);

echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll()]);
