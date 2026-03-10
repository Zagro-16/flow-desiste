<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$id=(int)($_GET['id'] ?? 0);
$stmt=$pdo->prepare('SELECT u.id,u.nome,u.cognome,u.email,u.stato,tp.codice_fiscale,tp.telefono,tp.ore_assegnate,tp.ore_svolte FROM users u LEFT JOIN teacher_profiles tp ON tp.user_id=u.id WHERE u.id=:id AND u.role="docente"');
$stmt->execute(['id'=>$id]); $docente=$stmt->fetch();
if(!$docente){ flash('danger','Docente non trovato'); header('Location:/admin/docenti.php'); exit; }
$pageTitle='Modifica Docente';
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?><?php require __DIR__ . '/partials/docente-form.php'; ?></main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
