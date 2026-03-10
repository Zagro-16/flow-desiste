<?php
require_once __DIR__ . '/../config/config.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || $password === '') {
    $_SESSION['error'] = 'Compila email e password.';
    header('Location: /index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nome, cognome, email, password_hash, role FROM users WHERE email = :email AND stato = "attivo" LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['error'] = 'Credenziali non valide.';
    header('Location: /index.php');
    exit;
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['full_name'] = $user['nome'] . ' ' . $user['cognome'];

redirectToDashboardByRole($user['role']);
