<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
header('Content-Type: application/json; charset=utf-8');

$stats = [
'corsi_totali'=>(int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
'corsi_attivi'=>(int)$pdo->query("SELECT COUNT(*) FROM courses WHERE stato='attivo'")->fetchColumn(),
'docenti_totali'=>(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='docente'")->fetchColumn(),
'corsisti_totali'=>(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='corsista'")->fetchColumn(),
'protocolli' => (int)$pdo->query('SELECT COUNT(*) FROM protocollo')->fetchColumn(),
'rinunce_desistenze' => (int)$pdo->query('SELECT COUNT(*) FROM withdrawals_or_renunciations')->fetchColumn(),
];

echo json_encode(['ok'=>true,'data'=>$stats]);
