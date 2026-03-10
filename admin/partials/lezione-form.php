<form action="/actions/lezione-save.php" method="post" class="card shadow-sm">
<input type="hidden" name="id" value="<?= (int)$lezione['id'] ?>">
<div class="card-body"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Titolo lezione</label><input class="form-control" name="titolo" value="<?= e((string)$lezione['titolo']) ?>" required></div>
<div class="col-md-3"><label class="form-label">Corso</label><select class="form-select" name="course_id" required><?php foreach($corsi as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$lezione['course_id']===(int)$c['id']?'selected':'' ?>><?= e($c['titolo']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Docente</label><select class="form-select" name="docente_id" required><?php foreach($docenti as $d): ?><option value="<?= (int)$d['id'] ?>" <?= (int)$lezione['docente_id']===(int)$d['id']?'selected':'' ?>><?= e($d['cognome'].' '.$d['nome']) ?></option><?php endforeach; ?></select></div>
<div class="col-md-3"><label class="form-label">Data</label><input type="date" class="form-control" name="data_lezione" value="<?= e((string)$lezione['data_lezione']) ?>" required></div>
<div class="col-md-2"><label class="form-label">Ora inizio</label><input type="time" class="form-control" name="ora_inizio" value="<?= e(substr((string)$lezione['ora_inizio'],0,5)) ?>" required></div>
<div class="col-md-2"><label class="form-label">Ora fine</label><input type="time" class="form-control" name="ora_fine" value="<?= e(substr((string)$lezione['ora_fine'],0,5)) ?>" required></div>
<div class="col-md-5"><label class="form-label">Google Meet link</label><input type="url" class="form-control" name="google_meet_link" value="<?= e((string)$lezione['google_meet_link']) ?>"></div>
<div class="col-md-3"><label class="form-label">Stato</label><select class="form-select" name="stato" required><?php foreach(['programmata','svolta','annullata'] as $s): ?><option value="<?= e($s) ?>" <?= $lezione['stato']===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?></select></div>
</div></div>
<div class="card-footer d-flex justify-content-between"><a href="/admin/lezioni.php" class="btn btn-outline-secondary">Annulla</a><button class="btn btn-primary">Salva lezione</button></div>
</form>
