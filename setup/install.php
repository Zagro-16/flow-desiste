<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /setup/index.php');
    exit;
}

function out(string $message, string $type = 'info'): void
{
    $class = [
        'success' => 'text-bg-success',
        'danger' => 'text-bg-danger',
        'warning' => 'text-bg-warning',
        'info' => 'text-bg-secondary',
    ][$type] ?? 'text-bg-secondary';
    echo '<div class="badge ' . $class . ' mb-2">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div><br>';
}

$dbHost = trim((string)($_POST['db_host'] ?? '127.0.0.1'));
$dbName = trim((string)($_POST['db_name'] ?? 'formaflow'));
$dbPort = trim((string)($_POST['db_port'] ?? '3306'));
$dbUser = trim((string)($_POST['db_user'] ?? 'root'));
$dbPass = (string)($_POST['db_pass'] ?? '');

$dsnNoDb = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $dbHost, $dbPort);
$dsnDb = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName);

$schemaFile = __DIR__ . '/../database/schema.sql';
$seedFile = __DIR__ . '/../database/seed.sql';
$fixFile = __DIR__ . '/../database/fix-demo-passwords.sql';

?><!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installazione FormaFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Installazione database FormaFlow</h1>
<?php
try {
    $pdoNoDb = new PDO($dsnNoDb, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    out('Connessione MySQL riuscita', 'success');

    $pdoNoDb->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    out('Database creato/verificato: ' . $dbName, 'success');

    $pdo = new PDO($dsnDb, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
    ]);

    $schemaSql = file_get_contents($schemaFile);
    if ($schemaSql === false) {
        throw new RuntimeException('Impossibile leggere schema.sql');
    }
    $pdo->exec($schemaSql);
    out('Schema importato correttamente', 'success');

    $seedSql = file_get_contents($seedFile);
    if ($seedSql === false) {
        throw new RuntimeException('Impossibile leggere seed.sql');
    }
    $pdo->exec($seedSql);
    out('Seed importato correttamente', 'success');

    $fixSql = file_get_contents($fixFile);
    if ($fixSql === false) {
        throw new RuntimeException('Impossibile leggere fix-demo-passwords.sql');
    }
    $pdo->exec($fixSql);
    out('Fix credenziali demo applicato', 'success');

    out('Installazione completata. Credenziali demo: admin/docente/corsista con Password123!', 'success');
    echo '<a class="btn btn-primary mt-3" href="/index.php">Vai al login</a>';
} catch (Throwable $e) {
    out('Errore installazione: ' . $e->getMessage(), 'danger');
    echo '<a class="btn btn-outline-secondary mt-3" href="/setup/index.php">Torna al setup</a>';
}
?>
        </div>
    </div>
</div>
</body>
</html>
