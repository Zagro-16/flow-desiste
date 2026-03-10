<form action="/actions/corso-save.php" method="post" class="card shadow-sm">
    <input type="hidden" name="id" value="<?= (int)$corso['id'] ?>">
    <div class="card-body"><div class="row g-3">
        <div class="col-md-3"><label class="form-label">Codice</label><input class="form-control" name="codice" value="<?= e((string)$corso['codice']) ?>" required></div>
        <div class="col-md-9"><label class="form-label">Titolo</label><input class="form-control" name="titolo" value="<?= e((string)$corso['titolo']) ?>" required></div>
        <div class="col-md-6"><label class="form-label">Descrizione</label><textarea class="form-control" name="descrizione" rows="4"><?= e((string)$corso['descrizione']) ?></textarea></div>
        <div class="col-md-2"><label class="form-label">Data inizio</label><input type="date" class="form-control" name="data_inizio" value="<?= e((string)$corso['data_inizio']) ?>" required></div>
        <div class="col-md-2"><label class="form-label">Data fine</label><input type="date" class="form-control" name="data_fine" value="<?= e((string)$corso['data_fine']) ?>" required></div>
        <div class="col-md-2"><label class="form-label">Monte ore</label><input type="number" step="0.5" min="0" class="form-control" name="monte_ore_totali" value="<?= e((string)$corso['monte_ore_totali']) ?>" required></div>
        <div class="col-md-3"><label class="form-label">Stato</label><select class="form-select" name="stato" required><?php foreach(['bozza','attivo','sospeso','chiuso'] as $s): ?><option value="<?= e($s) ?>" <?= $corso['stato']===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?></select></div>
    </div></div>
    <div class="card-footer d-flex justify-content-between"><a href="/admin/corsi.php" class="btn btn-outline-secondary">Annulla</a><button class="btn btn-primary">Salva corso</button></div>
</form>
