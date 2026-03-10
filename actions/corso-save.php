<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$id = (int)($_POST['id'] ?? 0);
$data = [
'codice'=>trim((string)($_POST['codice'] ?? '')),'titolo'=>trim((string)($_POST['titolo'] ?? '')),'descrizione'=>trim((string)($_POST['descrizione'] ?? '')),
'data_inizio'=>trim((string)($_POST['data_inizio'] ?? '')),'data_fine'=>trim((string)($_POST['data_fine'] ?? '')),
'stato'=>trim((string)($_POST['stato'] ?? 'bozza')),'monte_ore_totali'=>(float)($_POST['monte_ore_totali'] ?? 0),
];
if ($data['codice']===''||$data['titolo']===''||$data['data_inizio']===''||$data['data_fine']==='') { flash('danger','Compila i campi obbligatori.'); header('Location: '.($id?'/admin/corso-edit.php?id='.$id:'/admin/corso-new.php')); exit; }
try {
if($id){
$stmt=$pdo->prepare('UPDATE courses SET codice=:codice,titolo=:titolo,descrizione=:descrizione,data_inizio=:data_inizio,data_fine=:data_fine,stato=:stato,monte_ore_totali=:monte_ore_totali WHERE id=:id');
$stmt->execute($data+['id'=>$id]);
flash('success','Corso aggiornato.');
header('Location: /admin/corso-edit.php?id='.$id);
}else{
$stmt=$pdo->prepare('INSERT INTO courses (codice,titolo,descrizione,data_inizio,data_fine,stato,monte_ore_totali) VALUES (:codice,:titolo,:descrizione,:data_inizio,:data_fine,:stato,:monte_ore_totali)');
$stmt->execute($data); $id=(int)$pdo->lastInsertId(); flash('success','Corso creato.'); header('Location: /admin/corso-edit.php?id='.$id);
}
} catch(Throwable $e){ flash('danger','Errore salvataggio corso: '.$e->getMessage()); header('Location: '.($id?'/admin/corso-edit.php?id='.$id:'/admin/corso-new.php')); }
exit;
