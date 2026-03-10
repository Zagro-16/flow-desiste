<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Report';

$summary = [
    'corsi' => (int)$pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
    'lezioni' => (int)$pdo->query('SELECT COUNT(*) FROM lessons')->fetchColumn(),
    'docenti' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='docente'")->fetchColumn(),
    'corsisti' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='corsista'")->fetchColumn(),
    'protocolli' => (int)$pdo->query('SELECT COUNT(*) FROM protocollo')->fetchColumn(),
    'attestati' => (int)$pdo->query('SELECT COUNT(*) FROM certificates')->fetchColumn(),
];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4"><?php require __DIR__ . '/../includes/page-header.php'; ?>
<div class="row g-3 mb-3">
<?php foreach ($summary as $k => $v): ?><div class="col-md-4 col-xl-2"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted text-capitalize"><?= e($k) ?></div><div class="h4 mb-0"><?= (int)$v ?></div></div></div></div><?php endforeach; ?>
</div>
<div class="card shadow-sm"><div class="card-header">Andamento dati (chart)</div><div class="card-body"><canvas id="reportChart" height="100"></canvas></div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(async function(){
 const r = await fetch('/ajax/get-reports-data.php?report=overview');
 const data = await r.json();
 if(!data.ok){return;}
 const labels = data.data.courses_by_state.map(x=>x.stato);
 const values = data.data.courses_by_state.map(x=>parseInt(x.totale,10));
 const ctx = document.getElementById('reportChart');
 new Chart(ctx,{type:'bar',data:{labels:labels,datasets:[{label:'Corsi per stato',data:values,backgroundColor:'#0d6efd'}]}});
})();
</script>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
