<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$headers = ['GIORNO','MESE','ANNO','ORA','MINUTO','DURATA','IDCORSO','STAGE','CF_Docente','IdModulo','idSede','CF_Codocente','CF_Tutor'];
$stmt = $pdo->query('SELECT giorno,mese,anno,ora,minuto,durata,idcorso,stage,cf_docente,idmodulo,idsede,cf_codocente,cf_tutor FROM course_days ORDER BY anno,mese,giorno,ora,minuto');
$rows = $stmt->fetchAll();

$filename = 'giornate-corso-' . date('Ymd-His') . '.xls';
exportHeaders($filename, 'application/vnd.ms-excel; charset=utf-8');
echo "<table border='1'><tr>";
foreach ($headers as $h) { echo '<th>'.e($h).'</th>'; }
echo '</tr>';
foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>'.e((string)$r['giorno']).'</td><td>'.e((string)$r['mese']).'</td><td>'.e((string)$r['anno']).'</td>';
    echo '<td>'.e((string)$r['ora']).'</td><td>'.e((string)$r['minuto']).'</td><td>'.e((string)$r['durata']).'</td>';
    echo '<td>'.e((string)$r['idcorso']).'</td><td>'.e((string)$r['stage']).'</td><td>'.e((string)$r['cf_docente']).'</td>';
    echo '<td>'.e((string)$r['idmodulo']).'</td><td>'.e((string)$r['idsede']).'</td><td>'.e((string)$r['cf_codocente']).'</td><td>'.e((string)$r['cf_tutor']).'</td>';
    echo '</tr>';
}
echo '</table>';
exit;
