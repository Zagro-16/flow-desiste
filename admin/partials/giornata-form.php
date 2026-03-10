<form method="post" action="/actions/giornata-corso-save.php" class="card shadow-sm">
<input type="hidden" name="id" value="<?= (int)$data['id'] ?>">
<div class="card-body"><div class="row g-3">
    <div class="col-md-2"><label class="form-label">GIORNO</label><input class="form-control" name="giorno" type="number" min="1" max="31" value="<?= e((string)$data['giorno']) ?>" required></div>
    <div class="col-md-2"><label class="form-label">MESE</label><input class="form-control" name="mese" type="number" min="1" max="12" value="<?= e((string)$data['mese']) ?>" required></div>
    <div class="col-md-2"><label class="form-label">ANNO</label><input class="form-control" name="anno" type="number" min="2000" max="2100" value="<?= e((string)$data['anno']) ?>" required></div>
    <div class="col-md-2"><label class="form-label">ORA</label><input class="form-control" name="ora" type="number" min="0" max="23" value="<?= e((string)$data['ora']) ?>" required></div>
    <div class="col-md-2"><label class="form-label">MINUTO</label><input class="form-control" name="minuto" type="number" min="0" max="59" value="<?= e((string)$data['minuto']) ?>" required></div>
    <div class="col-md-2"><label class="form-label">DURATA</label><input class="form-control" name="durata" type="number" min="1" value="<?= e((string)$data['durata']) ?>" required></div>
    <div class="col-md-4"><label class="form-label">IDCORSO</label><select class="form-select" name="idcorso" required><?php foreach($corsi as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$data['idcorso']===(int)$c['id']?'selected':'' ?>><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><label class="form-label">STAGE</label><input class="form-control" name="stage" value="<?= e((string)$data['stage']) ?>" required></div>
    <div class="col-md-3"><label class="form-label">CF_Docente</label><input class="form-control" name="cf_docente" value="<?= e((string)$data['cf_docente']) ?>" maxlength="16" required></div>
    <div class="col-md-3"><label class="form-label">IdModulo</label><select class="form-select" name="idmodulo" required><?php foreach($moduli as $m): ?><option value="<?= (int)$m['id'] ?>" <?= (int)$data['idmodulo']===(int)$m['id']?'selected':'' ?>><?= e($m['nome']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><label class="form-label">idSede</label><select class="form-select" name="idsede" required><?php foreach($sedi as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$data['idsede']===(int)$s['id']?'selected':'' ?>><?= e($s['nome']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3"><label class="form-label">CF_Codocente</label><input class="form-control" name="cf_codocente" value="<?= e((string)$data['cf_codocente']) ?>" maxlength="16"></div>
    <div class="col-md-3"><label class="form-label">CF_Tutor</label><input class="form-control" name="cf_tutor" value="<?= e((string)$data['cf_tutor']) ?>" maxlength="16"></div>
</div></div>
<div class="card-footer d-flex justify-content-between"><a href="/admin/giornate-corso.php" class="btn btn-outline-secondary">Torna elenco</a><button class="btn btn-primary">Salva giornata</button></div>
</form>
