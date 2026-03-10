USE formaflow;

INSERT INTO users (nome, cognome, email, password_hash, role) VALUES
('Admin', 'Demo', 'admin@formaflow.local', '$2y$12$eMBbhnLmYxQBLOGRoiDWbeFM/MaUhr30mlinRgELpDm4svo3gjVmG', 'admin'),
('Mario', 'Docente', 'docente@formaflow.local', '$2y$12$eMBbhnLmYxQBLOGRoiDWbeFM/MaUhr30mlinRgELpDm4svo3gjVmG', 'docente'),
('Luca', 'Corsista', 'corsista@formaflow.local', '$2y$12$eMBbhnLmYxQBLOGRoiDWbeFM/MaUhr30mlinRgELpDm4svo3gjVmG', 'corsista');

INSERT INTO teacher_profiles (user_id, codice_fiscale, telefono, ore_assegnate, ore_svolte)
VALUES (2, 'RSSMRA80A01H501U', '+39000111222', 120, 24);

INSERT INTO student_profiles (
    user_id, nome, cognome, data_nascita, nazione, citta_nascita, sesso, codice_fiscale, residenza,
    indirizzo, cap, provincia, comune, cellulare, mail, titolo_studio, iban, data_inserimento_corsista
) VALUES (
    3, 'Luca', 'Corsista', '1998-05-10', 'Italia', 'Roma', 'M', 'CRSLCU98E10H501Q', 'Roma',
    'Via Formazione 100', '00100', 'RM', 'Roma', '+393331112233', 'corsista@formaflow.local',
    'Diploma', 'IT60X0542811101000000123456', CURDATE()
);

INSERT INTO courses (codice, titolo, descrizione, data_inizio, data_fine, stato, monte_ore_totali)
VALUES ('FF-001', 'Operatore Segretariale', 'Percorso professionalizzante', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'attivo', 120);

INSERT INTO course_modules (course_id, nome, ore) VALUES (1, 'Informatica di base', 30), (1, 'Comunicazione', 20);
INSERT INTO course_locations (nome, indirizzo, comune) VALUES ('Sede Centrale', 'Via Roma 1', 'Roma');
INSERT INTO tutors (nome, cognome, codice_fiscale, email) VALUES ('Giulia', 'Tutor', 'TTRGLI85A01H501L', 'tutor@formaflow.local');

INSERT INTO lessons (course_id, docente_id, titolo, data_lezione, ora_inizio, ora_fine, google_meet_link)
VALUES (1, 2, 'Introduzione al corso', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '09:00:00', '13:00:00', 'https://meet.google.com/demo-formaflow');

INSERT INTO enrollments (course_id, student_id) VALUES (1, 3);

INSERT INTO protocollo (anno, progressivo, numero_protocollo, oggetto, contenuto, data_protocollo, mittente_tipo, stato, tipologia, autore_id)
VALUES (YEAR(CURDATE()), 1, CONCAT(YEAR(CURDATE()), '/00001'), 'Comunicazione avvio corso', 'Si comunica l''avvio del corso FF-001.', CURDATE(), 'Segreteria', 'inviato', 'Comunicazione', 1);

INSERT INTO protocolli_destinatari (protocollo_id, destinatario_tipo, destinatario_id, destinatario_label, inviato_il)
VALUES (1, 'Corsisti', 3, 'Luca Corsista', NOW());

INSERT INTO stamps_signatures (nome, file_path, tipo, attivo) VALUES
('Timbro Standard Desiste', 'assets/stamps/Timbro Desiste.jpg', 'timbro', 1),
('Timbro con Firma Desiste', 'assets/stamps/Timbro con Firma Desiste.jpg', 'timbro_firma', 1);

INSERT INTO course_days (
    giorno, mese, anno, ora, minuto, durata, idcorso, stage, cf_docente, idmodulo, idsede, cf_codocente, cf_tutor
) VALUES (
    DAY(CURDATE()), MONTH(CURDATE()), YEAR(CURDATE()), 9, 0, 240, 1, 'A', 'RSSMRA80A01H501U', 1, 1, NULL, 'TTRGLI85A01H501L'
);

INSERT INTO document_categories (nome) VALUES ('Protocollo'), ('Attestato'), ('Rinuncia'), ('Desistenza');

INSERT INTO settings (chiave, valore) VALUES
('ente_nome', 'Desiste Formazione'),
('ente_email', 'info@desisteformazione.it'),
('smtp_host', 'smtp.example.com');
