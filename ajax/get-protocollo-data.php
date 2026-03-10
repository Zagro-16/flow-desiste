<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
header('Content-Type: application/json; charset=utf-8');

$state = trim((string)($_GET['state'] ?? ''));
$limit = min(200, max(10, (int)($_GET['limit'] ?? 50)));

$sql = 'SELECT p.id, p.numero_protocollo, p.data_protocollo, p.oggetto, p.stato, p.tipologia,
        (SELECT COUNT(*) FROM protocolli_destinatari pd WHERE pd.protocollo_id=p.id) AS destinatari
        FROM protocollo p WHERE 1=1';
$params = [];
if ($state !== '') {
    $sql .= ' AND p.stato = :state';
    $params['state'] = $state;
}
$sql .= ' ORDER BY p.id DESC LIMIT ' . $limit;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode(['ok' => true, 'data' => $stmt->fetchAll()]);
