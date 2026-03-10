<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$headers = ['GIORNO','MESE','ANNO','ORA','MINUTO','DURATA','IDCORSO','STAGE','CF_Docente','IdModulo','idSede','CF_Codocente','CF_Tutor'];
$stmt = $pdo->query('SELECT giorno,mese,anno,ora,minuto,durata,idcorso,stage,cf_docente,idmodulo,idsede,cf_codocente,cf_tutor FROM course_days ORDER BY anno,mese,giorno,ora,minuto');
$rows = $stmt->fetchAll();

$filename = 'giornate-corso-' . date('Ymd-His') . '.csv';
exportHeaders($filename, 'text/csv; charset=utf-8');
$out = fopen('php://output', 'wb');
fputcsv($out, $headers, ';');
foreach ($rows as $r) {
    fputcsv($out, [
        $r['giorno'], $r['mese'], $r['anno'], $r['ora'], $r['minuto'], $r['durata'], $r['idcorso'], $r['stage'],
        $r['cf_docente'], $r['idmodulo'], $r['idsede'], $r['cf_codocente'], $r['cf_tutor']
    ], ';');
}
fclose($out);
exit;
