<?php
require_once __DIR__ . '/../config/config.php';
requireAuth();
header('Content-Type: application/json; charset=utf-8');

$courseId = (int)($_GET['course_id'] ?? 0);
if ($courseId <= 0) {
    echo json_encode(['ok' => false, 'message' => 'course_id non valido']);
    exit;
}

$stmt = $pdo->prepare('SELECT giorno AS GIORNO, mese AS MESE, anno AS ANNO, ora AS ORA, minuto AS MINUTO, durata AS DURATA,
                       idcorso AS IDCORSO, stage AS STAGE, cf_docente AS CF_Docente, idmodulo AS IdModulo, idsede AS idSede,
                       cf_codocente AS CF_Codocente, cf_tutor AS CF_Tutor
                       FROM course_days WHERE idcorso=:id ORDER BY anno,mese,giorno,ora,minuto');
$stmt->execute(['id' => $courseId]);

echo json_encode(['ok' => true, 'data' => $stmt->fetchAll()]);
