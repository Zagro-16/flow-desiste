<?php
require_once __DIR__ . '/config/config.php';
requireAuth();
redirectToDashboardByRole($_SESSION['role']);
