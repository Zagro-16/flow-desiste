<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, nome, cognome, codice_fiscale FROM student_profiles WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$corsista = $stmt->fetch();
if (!$corsista) {
    flash('danger', 'Corsista non trovato.');
    header('Location: /admin/corsisti.php');
    exit;
}

$stmt = $pdo->prepare('SELECT sd.*, u.nome, u.cognome FROM student_documents sd JOIN users u ON u.id = sd.uploaded_by WHERE sd.student_profile_id = :id ORDER BY sd.data_upload DESC');
$stmt->execute(['id' => $id]);
$docs = $stmt->fetchAll();

$pageTitle = 'Documenti PDF Corsista';
$tipologie = ['Documento identità','Codice fiscale','Curriculum','Contratto','Domanda iscrizione','Privacy','Altro'];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4">
    <?php require __DIR__ . '/../includes/page-header.php'; ?>
    <?php require __DIR__ . '/../includes/alerts.php'; ?>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Carica PDF - <?= e($corsista['cognome'] . ' ' . $corsista['nome']) ?></div>
        <div class="card-body">
            <form action="/actions/corsista-pdf-upload.php" method="post" enctype="multipart/form-data" class="row g-3">
                <input type="hidden" name="student_profile_id" value="<?= (int)$corsista['id'] ?>">
                <div class="col-md-4">
                    <label class="form-label">Tipologia documento</label>
                    <select name="tipologia" class="form-select" required>
                        <?php foreach ($tipologie as $t): ?>
                            <option value="<?= e($t) ?>"><?= e($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">File PDF</label>
                    <input type="file" name="pdf_file" class="form-control" accept="application/pdf" required>
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary" type="submit">Carica</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Tipologia</th><th>Nome originale</th><th>Nome salvato</th><th>Upload</th><th>Operatore</th><th>File</th></tr></thead>
                <tbody>
                <?php if (!$docs): ?><tr><td colspan="6" class="text-center text-muted py-4">Nessun documento caricato.</td></tr><?php endif; ?>
                <?php foreach ($docs as $d): ?>
                    <tr>
                        <td><?= e($d['tipologia']) ?></td>
                        <td><?= e($d['nome_originale']) ?></td>
                        <td><small><?= e($d['nome_salvato']) ?></small></td>
                        <td><?= e($d['data_upload']) ?></td>
                        <td><?= e($d['cognome'] . ' ' . $d['nome']) ?></td>
                        <td><a class="btn btn-sm btn-outline-dark" href="/<?= e($d['percorso']) ?>" target="_blank">Apri</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
