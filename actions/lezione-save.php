<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$id=(int)($_POST['id'] ?? 0);
$data=[
'course_id'=>(int)($_POST['course_id'] ?? 0),'docente_id'=>(int)($_POST['docente_id'] ?? 0),'titolo'=>trim((string)($_POST['titolo'] ?? '')),
'data_lezione'=>trim((string)($_POST['data_lezione'] ?? '')),'ora_inizio'=>trim((string)($_POST['ora_inizio'] ?? '')),'ora_fine'=>trim((string)($_POST['ora_fine'] ?? '')),
'google_meet_link'=>trim((string)($_POST['google_meet_link'] ?? '')),'stato'=>trim((string)($_POST['stato'] ?? 'programmata')),
];
if($data['course_id']<=0||$data['docente_id']<=0||$data['titolo']===''||$data['data_lezione']===''||$data['ora_inizio']===''||$data['ora_fine']===''){flash('danger','Compila i campi obbligatori.');header('Location: '.($id?'/admin/lezione-edit.php?id='.$id:'/admin/lezione-new.php'));exit;}
if($data['google_meet_link']!=='' && !filter_var($data['google_meet_link'], FILTER_VALIDATE_URL)){ flash('danger','Link Google Meet non valido.'); header('Location: '.($id?'/admin/lezione-edit.php?id='.$id:'/admin/lezione-new.php')); exit; }
try{
if($id){$stmt=$pdo->prepare('UPDATE lessons SET course_id=:course_id,docente_id=:docente_id,titolo=:titolo,data_lezione=:data_lezione,ora_inizio=:ora_inizio,ora_fine=:ora_fine,google_meet_link=:google_meet_link,stato=:stato WHERE id=:id');$stmt->execute($data+['id'=>$id]);flash('success','Lezione aggiornata.');header('Location: /admin/lezione-edit.php?id='.$id);} 
else {$stmt=$pdo->prepare('INSERT INTO lessons (course_id,docente_id,titolo,data_lezione,ora_inizio,ora_fine,google_meet_link,stato) VALUES (:course_id,:docente_id,:titolo,:data_lezione,:ora_inizio,:ora_fine,:google_meet_link,:stato)');$stmt->execute($data);$id=(int)$pdo->lastInsertId();flash('success','Lezione creata.');header('Location: /admin/lezione-edit.php?id='.$id);} 
}catch(Throwable $e){flash('danger','Errore salvataggio lezione: '.$e->getMessage());header('Location: '.($id?'/admin/lezione-edit.php?id='.$id:'/admin/lezione-new.php'));}
exit;
