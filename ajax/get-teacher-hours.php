<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true, 'message' => 'Endpoint AJAX pronto']);
