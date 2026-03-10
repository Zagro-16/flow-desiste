<form method="post" action="/actions/docente-save.php" class="card shadow-sm">
<input type="hidden" name="id" value="<?= (int)$docente['id'] ?>">
<div class="card-body"><div class="row g-3">
<div class="col-md-3"><label class="form-label">Nome</label><input class="form-control" name="nome" value="<?= e((string)$docente['nome']) ?>" required></div>
<div class="col-md-3"><label class="form-label">Cognome</label><input class="form-control" name="cognome" value="<?= e((string)$docente['cognome']) ?>" required></div>
<div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= e((string)$docente['email']) ?>" required></div>
<div class="col-md-2"><label class="form-label">Stato</label><select class="form-select" name="stato"><?php foreach(['attivo','disattivo'] as $s): ?><option value="<?= e($s) ?>" <?= $docente['stato']===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Codice fiscale</label><input class="form-control" maxlength="16" name="codice_fiscale" value="<?= e((string)$docente['codice_fiscale']) ?>" required></div>
<div class="col-md-3"><label class="form-label">Telefono</label><input class="form-control" name="telefono" value="<?= e((string)$docente['telefono']) ?>"></div>
<div class="col-md-2"><label class="form-label">Ore assegnate</label><input type="number" step="0.5" class="form-control" name="ore_assegnate" value="<?= e((string)$docente['ore_assegnate']) ?>"></div>
<div class="col-md-2"><label class="form-label">Ore svolte</label><input type="number" step="0.5" class="form-control" name="ore_svolte" value="<?= e((string)$docente['ore_svolte']) ?>"></div>
<div class="col-md-2"><label class="form-label">Password</label><input type="text" class="form-control" name="password" placeholder="Solo nuovo/reset"></div>
</div></div>
<div class="card-footer d-flex justify-content-between"><a href="/admin/docenti.php" class="btn btn-outline-secondary">Annulla</a><button class="btn btn-primary">Salva docente</button></div>
</form>
