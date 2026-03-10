CREATE DATABASE IF NOT EXISTS formaflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE formaflow;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','docente','corsista') NOT NULL,
    stato ENUM('attivo','disattivo') NOT NULL DEFAULT 'attivo',
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aggiornato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE teacher_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    codice_fiscale VARCHAR(16) NOT NULL,
    telefono VARCHAR(30) NULL,
    ore_assegnate DECIMAL(8,2) NOT NULL DEFAULT 0,
    ore_svolte DECIMAL(8,2) NOT NULL DEFAULT 0,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aggiornato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_teacher_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE student_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    data_nascita DATE NOT NULL,
    nazione VARCHAR(100) NOT NULL,
    citta_nascita VARCHAR(100) NOT NULL,
    sesso ENUM('M','F','Altro') NOT NULL,
    codice_fiscale VARCHAR(16) NOT NULL UNIQUE,
    residenza VARCHAR(255) NOT NULL,
    indirizzo VARCHAR(255) NOT NULL,
    cap VARCHAR(10) NOT NULL,
    provincia VARCHAR(10) NOT NULL,
    comune VARCHAR(100) NOT NULL,
    cellulare VARCHAR(30) NOT NULL,
    mail VARCHAR(190) NOT NULL,
    titolo_studio VARCHAR(150) NOT NULL,
    iban VARCHAR(34) NOT NULL,
    data_inserimento_corsista DATE NOT NULL,
    data_eventuale_rinuncia DATE NULL,
    stato_corsista ENUM('attivo','rinunciatario','desistente','completato') NOT NULL DEFAULT 'attivo',
    motivazione_rinuncia TEXT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aggiornato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codice VARCHAR(30) NOT NULL UNIQUE,
    titolo VARCHAR(200) NOT NULL,
    descrizione TEXT NULL,
    data_inizio DATE NOT NULL,
    data_fine DATE NOT NULL,
    stato ENUM('bozza','attivo','sospeso','chiuso') NOT NULL DEFAULT 'bozza',
    monte_ore_totali DECIMAL(8,2) NOT NULL DEFAULT 0,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    aggiornato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE course_modules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(200) NOT NULL,
    ore DECIMAL(8,2) NOT NULL DEFAULT 0,
    CONSTRAINT fk_module_course FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE course_locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    indirizzo VARCHAR(255) NOT NULL,
    comune VARCHAR(100) NOT NULL
);

CREATE TABLE tutors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    codice_fiscale VARCHAR(16) NOT NULL UNIQUE,
    email VARCHAR(190) NOT NULL
);

CREATE TABLE lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    docente_id BIGINT UNSIGNED NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    data_lezione DATE NOT NULL,
    ora_inizio TIME NOT NULL,
    ora_fine TIME NOT NULL,
    google_meet_link VARCHAR(255) NULL,
    stato ENUM('programmata','svolta','annullata') NOT NULL DEFAULT 'programmata',
    reminder_2d_sent TINYINT(1) NOT NULL DEFAULT 0,
    reminder_1d_sent TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT fk_lesson_course FOREIGN KEY (course_id) REFERENCES courses(id),
    CONSTRAINT fk_lesson_docente FOREIGN KEY (docente_id) REFERENCES users(id)
);

CREATE TABLE enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    stato ENUM('attivo','ritirato','completato') NOT NULL DEFAULT 'attivo',
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_enrollment (course_id, student_id),
    CONSTRAINT fk_enrollment_course FOREIGN KEY (course_id) REFERENCES courses(id),
    CONSTRAINT fk_enrollment_student FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE attendance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    presente TINYINT(1) NOT NULL DEFAULT 0,
    checkin_qr_at DATETIME NULL,
    CONSTRAINT fk_attendance_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id),
    CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE materials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_material_course FOREIGN KEY (course_id) REFERENCES courses(id),
    CONSTRAINT fk_material_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

CREATE TABLE quizzes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    punteggio_massimo INT NOT NULL DEFAULT 100,
    CONSTRAINT fk_quiz_course FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE quiz_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    domanda TEXT NOT NULL,
    opzioni_json JSON NOT NULL,
    risposta_corretta VARCHAR(255) NOT NULL,
    CONSTRAINT fk_question_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);

CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    punteggio INT NOT NULL,
    completato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attempt_quiz FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    CONSTRAINT fk_attempt_student FOREIGN KEY (student_id) REFERENCES users(id)
);

CREATE TABLE stamps_signatures (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    tipo ENUM('timbro','timbro_firma','firma') NOT NULL,
    attivo TINYINT(1) NOT NULL DEFAULT 1,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE certificates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codice_univoco VARCHAR(60) NOT NULL UNIQUE,
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    stamp_signature_id BIGINT UNSIGNED NULL,
    generato_da BIGINT UNSIGNED NOT NULL,
    generato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cert_student FOREIGN KEY (student_id) REFERENCES users(id),
    CONSTRAINT fk_cert_course FOREIGN KEY (course_id) REFERENCES courses(id),
    CONSTRAINT fk_cert_stamp FOREIGN KEY (stamp_signature_id) REFERENCES stamps_signatures(id),
    CONSTRAINT fk_cert_user FOREIGN KEY (generato_da) REFERENCES users(id)
);

CREATE TABLE protocollo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    anno INT NOT NULL,
    progressivo INT NOT NULL,
    numero_protocollo VARCHAR(30) NOT NULL UNIQUE,
    oggetto VARCHAR(255) NOT NULL,
    contenuto LONGTEXT NOT NULL,
    data_protocollo DATE NOT NULL,
    mittente_tipo ENUM('Admin','Segreteria','Docenti','Corsisti','Corso specifico','Utente specifico') NOT NULL,
    stato ENUM('bozza','inviato','archiviato') NOT NULL DEFAULT 'bozza',
    tipologia VARCHAR(100) NOT NULL,
    autore_id BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_protocollo_user FOREIGN KEY (autore_id) REFERENCES users(id)
);

CREATE TABLE protocolli_destinatari (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    protocollo_id BIGINT UNSIGNED NOT NULL,
    destinatario_tipo ENUM('Admin','Segreteria','Docenti','Corsisti','Corso specifico','Utente specifico') NOT NULL,
    destinatario_id BIGINT UNSIGNED NULL,
    destinatario_label VARCHAR(255) NULL,
    inviato_il DATETIME NULL,
    CONSTRAINT fk_pd_protocollo FOREIGN KEY (protocollo_id) REFERENCES protocollo(id)
);

CREATE TABLE protocol_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    protocollo_id BIGINT UNSIGNED NOT NULL,
    nome_originale VARCHAR(255) NOT NULL,
    nome_salvato VARCHAR(255) NOT NULL,
    percorso VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pa_protocollo FOREIGN KEY (protocollo_id) REFERENCES protocollo(id),
    CONSTRAINT fk_pa_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

CREATE TABLE document_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE generated_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id BIGINT UNSIGNED NOT NULL,
    protocollo_id BIGINT UNSIGNED NULL,
    student_profile_id BIGINT UNSIGNED NULL,
    titolo VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    stamp_signature_id BIGINT UNSIGNED NULL,
    creato_da BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_gd_category FOREIGN KEY (categoria_id) REFERENCES document_categories(id),
    CONSTRAINT fk_gd_protocollo FOREIGN KEY (protocollo_id) REFERENCES protocollo(id),
    CONSTRAINT fk_gd_student FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id),
    CONSTRAINT fk_gd_stamp FOREIGN KEY (stamp_signature_id) REFERENCES stamps_signatures(id),
    CONSTRAINT fk_gd_user FOREIGN KEY (creato_da) REFERENCES users(id)
);

CREATE TABLE student_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_profile_id BIGINT UNSIGNED NOT NULL,
    tipologia ENUM('Documento identità','Codice fiscale','Curriculum','Contratto','Domanda iscrizione','Privacy','Altro') NOT NULL,
    nome_originale VARCHAR(255) NOT NULL,
    nome_salvato VARCHAR(255) NOT NULL,
    percorso VARCHAR(255) NOT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    data_upload DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sd_student FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id),
    CONSTRAINT fk_sd_user FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

CREATE TABLE course_days (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    giorno TINYINT UNSIGNED NOT NULL,
    mese TINYINT UNSIGNED NOT NULL,
    anno SMALLINT UNSIGNED NOT NULL,
    ora TINYINT UNSIGNED NOT NULL,
    minuto TINYINT UNSIGNED NOT NULL,
    durata SMALLINT UNSIGNED NOT NULL,
    idcorso BIGINT UNSIGNED NOT NULL,
    stage VARCHAR(50) NOT NULL,
    cf_docente VARCHAR(16) NOT NULL,
    idmodulo BIGINT UNSIGNED NOT NULL,
    idsede BIGINT UNSIGNED NOT NULL,
    cf_codocente VARCHAR(16) NULL,
    cf_tutor VARCHAR(16) NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cd_course FOREIGN KEY (idcorso) REFERENCES courses(id),
    CONSTRAINT fk_cd_module FOREIGN KEY (idmodulo) REFERENCES course_modules(id),
    CONSTRAINT fk_cd_sede FOREIGN KEY (idsede) REFERENCES course_locations(id)
);

CREATE TABLE export_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('csv','excel') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    creato_da BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_export_user FOREIGN KEY (creato_da) REFERENCES users(id)
);

CREATE TABLE withdrawals_or_renunciations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_profile_id BIGINT UNSIGNED NOT NULL,
    tipo ENUM('rinuncia','desistenza') NOT NULL,
    data_evento DATE NOT NULL,
    motivazione TEXT NOT NULL,
    registrato_da BIGINT UNSIGNED NOT NULL,
    creato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wr_student FOREIGN KEY (student_profile_id) REFERENCES student_profiles(id),
    CONSTRAINT fk_wr_user FOREIGN KEY (registrato_da) REFERENCES users(id)
);

CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destinatario_email VARCHAR(190) NOT NULL,
    oggetto VARCHAR(255) NOT NULL,
    corpo TEXT NOT NULL,
    lesson_id BIGINT UNSIGNED NULL,
    tipo_reminder ENUM('2_giorni','1_giorno','manuale') NOT NULL,
    inviato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_email_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id)
);

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chiave VARCHAR(150) NOT NULL UNIQUE,
    valore TEXT NULL,
    aggiornato_il DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE communications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    target_type ENUM('Docenti','Corsisti','Corso','Utente','Tutti') NOT NULL,
    target_course_id BIGINT UNSIGNED NULL,
    target_user_id BIGINT UNSIGNED NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    total_recipients INT NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comm_course FOREIGN KEY (target_course_id) REFERENCES courses(id),
    CONSTRAINT fk_comm_user FOREIGN KEY (target_user_id) REFERENCES users(id),
    CONSTRAINT fk_comm_creator FOREIGN KEY (created_by) REFERENCES users(id)
);
