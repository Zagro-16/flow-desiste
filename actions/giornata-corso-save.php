<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$id = (int)($_POST['id'] ?? 0);
$fields = ['giorno','mese','anno','ora','minuto','durata','idcorso','stage','cf_docente','idmodulo','idsede','cf_codocente','cf_tutor'];
$data = [];
foreach ($fields as $f) { $data[$f] = trim((string)($_POST[$f] ?? '')); }
$required = ['giorno','mese','anno','ora','minuto','durata','idcorso','stage','cf_docente','idmodulo','idsede'];
foreach ($required as $f) {
    if ($data[$f] === '') { flash('danger','Compila tutti i campi obbligatori giornata corso.'); header('Location: '.($id>0?'/admin/giornata-edit.php?id='.$id:'/admin/giornata-new.php')); exit; }
}
try {
    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE course_days SET giorno=:giorno,mese=:mese,anno=:anno,ora=:ora,minuto=:minuto,durata=:durata,idcorso=:idcorso,stage=:stage,cf_docente=:cf_docente,idmodulo=:idmodulo,idsede=:idsede,cf_codocente=:cf_codocente,cf_tutor=:cf_tutor WHERE id=:id');
        $stmt->execute(array_merge($data,['id'=>$id]));
    } else {
        $stmt = $pdo->prepare('INSERT INTO course_days (giorno,mese,anno,ora,minuto,durata,idcorso,stage,cf_docente,idmodulo,idsede,cf_codocente,cf_tutor) VALUES (:giorno,:mese,:anno,:ora,:minuto,:durata,:idcorso,:stage,:cf_docente,:idmodulo,:idsede,:cf_codocente,:cf_tutor)');
        $stmt->execute($data);
    }
    flash('success', 'Giornata corso salvata.');
} catch (Throwable $e) {
    flash('danger', 'Errore salvataggio giornata: '.$e->getMessage());
}
header('Location: /admin/giornate-corso.php');
exit;
