<?php
declare(strict_types=1);

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function requireAuth(): void
{
    if (!isAuthenticated()) {
        $_SESSION['error'] = 'Sessione scaduta. Effettua nuovamente il login.';
        header('Location: /index.php');
        exit;
    }
}

function requireRole(array $roles): void
{
    requireAuth();

    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        exit('Accesso negato');
    }
}

function redirectToDashboardByRole(string $role): void
{
    $map = [
        'admin' => '/admin/dashboard.php',
        'docente' => '/docente/dashboard.php',
        'corsista' => '/corsista/dashboard.php',
    ];

    header('Location: ' . ($map[$role] ?? '/dashboard.php'));
    exit;
}
