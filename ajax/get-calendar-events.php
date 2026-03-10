<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');

$start = trim((string)($_GET['start'] ?? ''));
$end = trim((string)($_GET['end'] ?? ''));

$sql = 'SELECT l.id, l.titolo, l.data_lezione, l.ora_inizio, l.ora_fine, c.titolo AS corso FROM lessons l JOIN courses c ON c.id=l.course_id WHERE 1=1';
$params=[];
if($start!==''){ $sql.=' AND l.data_lezione >= :start'; $params['start']=$start; }
if($end!==''){ $sql.=' AND l.data_lezione <= :end'; $params['end']=$end; }
if(($_SESSION['role'] ?? '')==='docente'){ $sql.=' AND l.docente_id = :docente'; $params['docente']=(int)$_SESSION['user_id']; }
$sql.=' ORDER BY l.data_lezione, l.ora_inizio';
$stmt=$pdo->prepare($sql); $stmt->execute($params);
$events=[];
foreach($stmt->fetchAll() as $r){
    $events[]=[
        'id'=>(int)$r['id'],
        'title'=>$r['corso'].' - '.$r['titolo'],
        'start'=>$r['data_lezione'].'T'.$r['ora_inizio'],
        'end'=>$r['data_lezione'].'T'.$r['ora_fine'],
    ];
}

echo json_encode($events);
