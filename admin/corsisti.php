<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$pageTitle = 'Gestione Corsisti';
$search = trim((string)($_GET['q'] ?? ''));
$state = trim((string)($_GET['stato'] ?? ''));

$sql = 'SELECT sp.id, sp.nome, sp.cognome, sp.codice_fiscale, sp.mail, sp.cellulare, sp.comune, sp.provincia,
               sp.data_inserimento_corsista, sp.data_eventuale_rinuncia, sp.stato_corsista,
               u.id AS user_id
        FROM student_profiles sp
        INNER JOIN users u ON u.id = sp.user_id
        WHERE 1=1';
$params = [];

if ($search !== '') {
    $sql .= ' AND (sp.nome LIKE :search OR sp.cognome LIKE :search OR sp.codice_fiscale LIKE :search OR sp.mail LIKE :search)';
    $params['search'] = '%' . $search . '%';
}
if ($state !== '' && in_array($state, ['attivo', 'rinunciatario', 'desistente', 'completato'], true)) {
    $sql .= ' AND sp.stato_corsista = :stato';
    $params['stato'] = $state;
}

$sql .= ' ORDER BY sp.creato_il DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$corsisti = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
        <main class="col-lg-10 p-4">
            <?php require __DIR__ . '/../includes/page-header.php'; ?>
            <?php require __DIR__ . '/../includes/alerts.php'; ?>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form class="row g-2" method="get">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="q" placeholder="Cerca per nome, cognome, CF, email" value="<?= e($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="stato">
                                <option value="">Tutti gli stati</option>
                                <?php foreach (['attivo','rinunciatario','desistente','completato'] as $s): ?>
                                    <option value="<?= e($s) ?>" <?= $state === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary w-100" type="submit">Filtra</button>
                            <a class="btn btn-success w-100" href="/admin/corsista-new.php">Nuovo corsista</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>Corsista</th>
                            <th>CF</th>
                            <th>Contatti</th>
                            <th>Residenza</th>
                            <th>Data inserimento</th>
                            <th>Data rinuncia</th>
                            <th>Stato</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!$corsisti): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Nessun corsista trovato.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($corsisti as $c): ?>
                            <tr>
                                <td><strong><?= e($c['cognome'] . ' ' . $c['nome']) ?></strong></td>
                                <td><?= e($c['codice_fiscale']) ?></td>
                                <td><div><?= e($c['mail']) ?></div><small><?= e($c['cellulare']) ?></small></td>
                                <td><?= e($c['comune'] . ' (' . $c['provincia'] . ')') ?></td>
                                <td><?= e((string)$c['data_inserimento_corsista']) ?></td>
                                <td><?= e((string)($c['data_eventuale_rinuncia'] ?: '-')) ?></td>
                                <td><span class="badge text-bg-secondary"><?= e($c['stato_corsista']) ?></span></td>
                                <td class="text-end">
                                    <a href="/admin/corsista-edit.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary">Modifica</a>
                                    <a href="/admin/corsista-documenti.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-dark">Documenti</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
