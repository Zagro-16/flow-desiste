<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');
$q = trim((string)($_GET['q'] ?? ''));
if($q===''){ echo json_encode(['ok'=>true,'data'=>[]]); exit; }
$stmt=$pdo->prepare('SELECT id,nome,cognome,email,role FROM users WHERE nome LIKE :q OR cognome LIKE :q OR email LIKE :q ORDER BY cognome,nome LIMIT 30');
$stmt->execute(['q'=>'%'.$q.'%']);
echo json_encode(['ok'=>true,'data'=>$stmt->fetchAll()]);
