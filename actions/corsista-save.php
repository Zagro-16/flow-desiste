<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);

$id = (int)($_POST['id'] ?? 0);

$data = [
    'nome' => trim((string)($_POST['nome'] ?? '')),
    'cognome' => trim((string)($_POST['cognome'] ?? '')),
    'data_nascita' => trim((string)($_POST['data_nascita'] ?? '')),
    'nazione' => trim((string)($_POST['nazione'] ?? '')),
    'citta_nascita' => trim((string)($_POST['citta_nascita'] ?? '')),
    'sesso' => trim((string)($_POST['sesso'] ?? '')),
    'codice_fiscale' => strtoupper(trim((string)($_POST['codice_fiscale'] ?? ''))),
    'residenza' => trim((string)($_POST['residenza'] ?? '')),
    'indirizzo' => trim((string)($_POST['indirizzo'] ?? '')),
    'cap' => trim((string)($_POST['cap'] ?? '')),
    'provincia' => strtoupper(trim((string)($_POST['provincia'] ?? ''))),
    'comune' => trim((string)($_POST['comune'] ?? '')),
    'cellulare' => trim((string)($_POST['cellulare'] ?? '')),
    'mail' => strtolower(trim((string)($_POST['mail'] ?? ''))),
    'titolo_studio' => trim((string)($_POST['titolo_studio'] ?? '')),
    'iban' => strtoupper(trim((string)($_POST['iban'] ?? ''))),
    'data_inserimento_corsista' => trim((string)($_POST['data_inserimento_corsista'] ?? '')),
    'data_eventuale_rinuncia' => trim((string)($_POST['data_eventuale_rinuncia'] ?? '')),
    'stato_corsista' => normalizeWithdrawalState((string)($_POST['stato_corsista'] ?? 'attivo')),
    'motivazione_rinuncia' => trim((string)($_POST['motivazione_rinuncia'] ?? '')),
];

$required = ['nome','cognome','data_nascita','nazione','citta_nascita','sesso','codice_fiscale','residenza','indirizzo','cap','provincia','comune','cellulare','mail','titolo_studio','iban','data_inserimento_corsista'];
foreach ($required as $field) {
    if ($data[$field] === '') {
        flash('danger', 'Compila tutti i campi obbligatori.');
        header('Location: ' . ($id > 0 ? '/admin/corsista-edit.php?id=' . $id : '/admin/corsista-new.php'));
        exit;
    }
}
if (!filter_var($data['mail'], FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Email non valida.');
    header('Location: ' . ($id > 0 ? '/admin/corsista-edit.php?id=' . $id : '/admin/corsista-new.php'));
    exit;
}

try {
    $pdo->beginTransaction();

    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT user_id FROM student_profiles WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            throw new RuntimeException('Corsista non trovato.');
        }
        $userId = (int)$existing['user_id'];

        $sql = 'UPDATE student_profiles SET
                nome=:nome,cognome=:cognome,data_nascita=:data_nascita,nazione=:nazione,citta_nascita=:citta_nascita,sesso=:sesso,
                codice_fiscale=:codice_fiscale,residenza=:residenza,indirizzo=:indirizzo,cap=:cap,provincia=:provincia,comune=:comune,
                cellulare=:cellulare,mail=:mail,titolo_studio=:titolo_studio,iban=:iban,data_inserimento_corsista=:data_ins,
                data_eventuale_rinuncia=:data_rin,stato_corsista=:stato,motivazione_rinuncia=:motivazione
                WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nome' => $data['nome'], 'cognome' => $data['cognome'], 'data_nascita' => $data['data_nascita'], 'nazione' => $data['nazione'],
            'citta_nascita' => $data['citta_nascita'], 'sesso' => $data['sesso'], 'codice_fiscale' => $data['codice_fiscale'], 'residenza' => $data['residenza'],
            'indirizzo' => $data['indirizzo'], 'cap' => $data['cap'], 'provincia' => $data['provincia'], 'comune' => $data['comune'], 'cellulare' => $data['cellulare'],
            'mail' => $data['mail'], 'titolo_studio' => $data['titolo_studio'], 'iban' => $data['iban'], 'data_ins' => $data['data_inserimento_corsista'],
            'data_rin' => $data['data_eventuale_rinuncia'] !== '' ? $data['data_eventuale_rinuncia'] : null,
            'stato' => $data['stato_corsista'], 'motivazione' => $data['motivazione_rinuncia'] !== '' ? $data['motivazione_rinuncia'] : null, 'id' => $id
        ]);

        $stmt = $pdo->prepare('UPDATE users SET nome=:nome, cognome=:cognome, email=:email WHERE id=:id');
        $stmt->execute(['nome' => $data['nome'], 'cognome' => $data['cognome'], 'email' => $data['mail'], 'id' => $userId]);
    } else {
        $passwordHash = password_hash('Password123!', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (nome,cognome,email,password_hash,role) VALUES (:nome,:cognome,:email,:password_hash,"corsista")');
        $stmt->execute(['nome' => $data['nome'], 'cognome' => $data['cognome'], 'email' => $data['mail'], 'password_hash' => $passwordHash]);
        $userId = (int)$pdo->lastInsertId();

        $sql = 'INSERT INTO student_profiles (
                user_id,nome,cognome,data_nascita,nazione,citta_nascita,sesso,codice_fiscale,residenza,indirizzo,cap,provincia,comune,cellulare,mail,titolo_studio,iban,
                data_inserimento_corsista,data_eventuale_rinuncia,stato_corsista,motivazione_rinuncia
            ) VALUES (
                :user_id,:nome,:cognome,:data_nascita,:nazione,:citta_nascita,:sesso,:codice_fiscale,:residenza,:indirizzo,:cap,:provincia,:comune,:cellulare,:mail,:titolo_studio,:iban,
                :data_ins,:data_rin,:stato,:motivazione
            )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId, 'nome' => $data['nome'], 'cognome' => $data['cognome'], 'data_nascita' => $data['data_nascita'], 'nazione' => $data['nazione'],
            'citta_nascita' => $data['citta_nascita'], 'sesso' => $data['sesso'], 'codice_fiscale' => $data['codice_fiscale'], 'residenza' => $data['residenza'],
            'indirizzo' => $data['indirizzo'], 'cap' => $data['cap'], 'provincia' => $data['provincia'], 'comune' => $data['comune'], 'cellulare' => $data['cellulare'],
            'mail' => $data['mail'], 'titolo_studio' => $data['titolo_studio'], 'iban' => $data['iban'], 'data_ins' => $data['data_inserimento_corsista'],
            'data_rin' => $data['data_eventuale_rinuncia'] !== '' ? $data['data_eventuale_rinuncia'] : null,
            'stato' => $data['stato_corsista'], 'motivazione' => $data['motivazione_rinuncia'] !== '' ? $data['motivazione_rinuncia'] : null,
        ]);

        $id = (int)$pdo->lastInsertId();
    }

    if (in_array($data['stato_corsista'], ['rinunciatario', 'desistente'], true)) {
        $tipo = $data['stato_corsista'] === 'rinunciatario' ? 'rinuncia' : 'desistenza';
        $stmt = $pdo->prepare('INSERT INTO withdrawals_or_renunciations (student_profile_id, tipo, data_evento, motivazione, registrato_da)
                               VALUES (:sid, :tipo, :data_evento, :motivazione, :uid)');
        $stmt->execute([
            'sid' => $id,
            'tipo' => $tipo,
            'data_evento' => $data['data_eventuale_rinuncia'] !== '' ? $data['data_eventuale_rinuncia'] : date('Y-m-d'),
            'motivazione' => $data['motivazione_rinuncia'] ?: 'Non specificata',
            'uid' => (int)$_SESSION['user_id'],
        ]);
    }

    $pdo->commit();
    flash('success', 'Corsista salvato correttamente.');
    header('Location: /admin/corsista-edit.php?id=' . $id);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('danger', 'Errore salvataggio corsista: ' . $e->getMessage());
    header('Location: ' . ($id > 0 ? '/admin/corsista-edit.php?id=' . $id : '/admin/corsista-new.php'));
}
exit;
