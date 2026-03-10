<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
flash('info', 'Endpoint pronto per implementazione completa con validazioni PDO e redirect.');
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/dashboard.php'));
exit;
