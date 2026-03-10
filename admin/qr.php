<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin','docente']);
$pageTitle='QR Check-in';
$lessons = $pdo->query('SELECT l.id, l.titolo, l.data_lezione, c.titolo AS corso_titolo FROM lessons l JOIN courses c ON c.id=l.course_id ORDER BY l.data_lezione DESC LIMIT 100')->fetchAll();
require_once __DIR__ . '/../includes/header.php'; require_once __DIR__ . '/../includes/topbar.php';
$sidebar = (($_SESSION['role'] ?? '') === 'docente') ? __DIR__ . '/../includes/sidebar-docente.php' : __DIR__ . '/../includes/sidebar-admin.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require $sidebar; ?></div><main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?><?php require __DIR__ . '/../includes/alerts.php'; ?>
<div class="card shadow-sm"><div class="card-header">Check-in presenza via QR (simulazione con ID corsista)</div><div class="card-body">
<form id="qrForm" class="row g-3">
<div class="col-md-4"><label class="form-label">Lezione</label><select class="form-select" name="lesson_id" required><?php foreach($lessons as $l): ?><option value="<?= (int)$l['id'] ?>"><?= e($l['data_lezione'].' - '.$l['corso_titolo'].' - '.$l['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-4"><label class="form-label">ID Corsista (dato dal QR)</label><input class="form-control" name="student_id" type="number" min="1" required></div>
<div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">Registra check-in</button></div>
</form>
<div id="qrResult" class="mt-3"></div>
</div></div>
<script>
document.getElementById('qrForm').addEventListener('submit', async function(e){
 e.preventDefault();
 const fd = new FormData(this);
 const res = await fetch('/ajax/qr-checkin.php',{method:'POST',body:fd});
 const data = await res.json();
 const box = document.getElementById('qrResult');
 box.innerHTML = `<div class="alert ${data.ok?'alert-success':'alert-danger'}">${data.message}</div>`;
});
</script>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
