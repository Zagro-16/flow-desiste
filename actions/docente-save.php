<?php
require_once __DIR__ . '/../config/config.php'; requireRole(['admin']);
$id=(int)($_POST['id'] ?? 0);
$data=[
'nome'=>trim((string)($_POST['nome'] ?? '')),'cognome'=>trim((string)($_POST['cognome'] ?? '')),'email'=>strtolower(trim((string)($_POST['email'] ?? ''))),
'stato'=>trim((string)($_POST['stato'] ?? 'attivo')),'codice_fiscale'=>strtoupper(trim((string)($_POST['codice_fiscale'] ?? ''))),
'telefono'=>trim((string)($_POST['telefono'] ?? '')),'ore_assegnate'=>(float)($_POST['ore_assegnate'] ?? 0),'ore_svolte'=>(float)($_POST['ore_svolte'] ?? 0),'password'=>(string)($_POST['password'] ?? ''),
];
if($data['nome']===''||$data['cognome']===''||!filter_var($data['email'], FILTER_VALIDATE_EMAIL)||$data['codice_fiscale']===''){flash('danger','Dati docente non validi.');header('Location: '.($id?'/admin/docente-edit.php?id='.$id:'/admin/docente-new.php'));exit;}
try{
$pdo->beginTransaction();
if($id){
$sql='UPDATE users SET nome=:nome,cognome=:cognome,email=:email,stato=:stato'.($data['password']!==''?',password_hash=:password_hash':'').' WHERE id=:id AND role="docente"';
$params=['nome'=>$data['nome'],'cognome'=>$data['cognome'],'email'=>$data['email'],'stato'=>$data['stato'],'id'=>$id];
if($data['password']!=='') $params['password_hash']=password_hash($data['password'], PASSWORD_DEFAULT);
$stmt=$pdo->prepare($sql); $stmt->execute($params);
$stmt=$pdo->prepare('INSERT INTO teacher_profiles (user_id,codice_fiscale,telefono,ore_assegnate,ore_svolte) VALUES (:id,:cf,:tel,:oa,:os)
ON DUPLICATE KEY UPDATE codice_fiscale=VALUES(codice_fiscale),telefono=VALUES(telefono),ore_assegnate=VALUES(ore_assegnate),ore_svolte=VALUES(ore_svolte)');
$stmt->execute(['id'=>$id,'cf'=>$data['codice_fiscale'],'tel'=>$data['telefono']!==''?$data['telefono']:null,'oa'=>$data['ore_assegnate'],'os'=>$data['ore_svolte']]);
}else{
$pass = $data['password']!=='' ? $data['password'] : 'Password123!';
$stmt=$pdo->prepare('INSERT INTO users (nome,cognome,email,password_hash,role,stato) VALUES (:nome,:cognome,:email,:password_hash,"docente",:stato)');
$stmt->execute(['nome'=>$data['nome'],'cognome'=>$data['cognome'],'email'=>$data['email'],'password_hash'=>password_hash($pass,PASSWORD_DEFAULT),'stato'=>$data['stato']]);
$id=(int)$pdo->lastInsertId();
$stmt=$pdo->prepare('INSERT INTO teacher_profiles (user_id,codice_fiscale,telefono,ore_assegnate,ore_svolte) VALUES (:id,:cf,:tel,:oa,:os)');
$stmt->execute(['id'=>$id,'cf'=>$data['codice_fiscale'],'tel'=>$data['telefono']!==''?$data['telefono']:null,'oa'=>$data['ore_assegnate'],'os'=>$data['ore_svolte']]);
}
$pdo->commit(); flash('success','Docente salvato correttamente.'); header('Location: /admin/docente-edit.php?id='.$id);
}catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); flash('danger','Errore salvataggio docente: '.$e->getMessage()); header('Location: '.($id?'/admin/docente-edit.php?id='.$id:'/admin/docente-new.php')); }
exit;
