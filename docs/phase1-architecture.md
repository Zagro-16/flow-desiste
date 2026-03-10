# FormaFlow By Desiste — FASE 1

## Breve descrizione architettura

FormaFlow By Desiste è progettato come applicazione web modulare **PHP 8 + MySQL/MariaDB** con rendering server-side (PHP/HTML) e frontend progressivo in **JavaScript Vanilla**. L’architettura è organizzata a livelli:

- **Presentation Layer**: pagine per area `admin/`, `docente/`, `corsista/` con layout condiviso in `includes/` e asset in `assets/`.
- **Application Layer**: endpoint di processo in `actions/` (POST/redirect) e servizi asincroni JSON in `ajax/`.
- **Domain/Data Layer**: accesso DB via PDO, schema relazionale in `database/`, helper riusabili in `config/functions.php`.
- **Automation Layer**: job schedulati in `cron/` per reminder email e processi periodici.
- **Document Engine Layer**: template e motori per protocollo/attestati/documenti in `templates/` + `includes/document-engine.php`, storage in `uploads/`.
- **Security & Governance**: autenticazione/ruoli in `config/auth.php`, hardening directory via `.htaccess`, naming sicuro upload e logging operazioni.

---

## Albero completo cartelle e file

```text
FormaFlow-By-Desiste/
├── .htaccess
├── index.php
├── dashboard.php
├── logout.php
├── README.md
├── composer.json
├── composer.lock
├── config/
│   ├── config.php
│   ├── db.php
│   ├── auth.php
│   ├── functions.php
│   ├── routes.php
│   ├── constants.php
│   └── mail.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── topbar.php
│   ├── sidebar-admin.php
│   ├── sidebar-docente.php
│   ├── sidebar-corsista.php
│   ├── alerts.php
│   ├── page-header.php
│   ├── pagination.php
│   ├── csrf.php
│   ├── protocol-helpers.php
│   ├── attestati-helpers.php
│   ├── export-helpers.php
│   └── document-engine.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── dashboard.css
│   │   ├── forms.css
│   │   ├── tables.css
│   │   ├── responsive.css
│   │   └── print.css
│   ├── js/
│   │   ├── app.js
│   │   ├── calendar.js
│   │   ├── qrscan.js
│   │   ├── quiz.js
│   │   ├── charts.js
│   │   ├── validations.js
│   │   ├── reminders.js
│   │   ├── protocollo.js
│   │   ├── course-days.js
│   │   ├── documenti.js
│   │   ├── attestati.js
│   │   └── uploads.js
│   ├── img/
│   │   ├── logo.png
│   │   ├── logo1.png
│   │   ├── favicon.png
│   │   └── placeholders/
│   │       ├── avatar-default.png
│   │       └── empty-state.svg
│   └── stamps/
│       ├── Timbro Desiste.jpg
│       ├── Timbro con Firma Desiste.jpg
│       ├── firma-responsabile.png
│       └── timbro-standard.png
├── admin/
│   ├── dashboard.php
│   ├── corsi.php
│   ├── corso-new.php
│   ├── corso-edit.php
│   ├── lezioni.php
│   ├── lezione-new.php
│   ├── lezione-edit.php
│   ├── calendario.php
│   ├── docenti.php
│   ├── docente-new.php
│   ├── docente-edit.php
│   ├── corsisti.php
│   ├── corsista-new.php
│   ├── corsista-edit.php
│   ├── corsista-documenti.php
│   ├── iscrizioni.php
│   ├── presenze.php
│   ├── qr.php
│   ├── materiali.php
│   ├── quiz.php
│   ├── quiz-new.php
│   ├── attestati.php
│   ├── comunicazioni.php
│   ├── report.php
│   ├── impostazioni.php
│   ├── protocollo.php
│   ├── protocollo-new.php
│   ├── protocollo-view.php
│   ├── giornate-corso.php
│   ├── giornata-new.php
│   ├── giornata-edit.php
│   ├── rinunce.php
│   ├── export.php
│   └── logs.php
├── docente/
│   ├── dashboard.php
│   ├── calendario.php
│   ├── corsi.php
│   ├── lezioni.php
│   ├── presenze.php
│   ├── materiali.php
│   ├── quiz.php
│   ├── monteore.php
│   └── profilo.php
├── corsista/
│   ├── dashboard.php
│   ├── corsi.php
│   ├── calendario.php
│   ├── materiali.php
│   ├── quiz.php
│   ├── attestati.php
│   ├── profilo.php
│   └── protocollo.php
├── actions/
│   ├── login-action.php
│   ├── corso-save.php
│   ├── lezione-save.php
│   ├── docente-save.php
│   ├── corsista-save.php
│   ├── iscrizione-save.php
│   ├── presenza-save.php
│   ├── quiz-save.php
│   ├── quiz-submit.php
│   ├── attestato-genera.php
│   ├── materiale-upload.php
│   ├── settings-save.php
│   ├── invia-reminder-manuale.php
│   ├── protocollo-save.php
│   ├── documento-genera.php
│   ├── corsista-pdf-upload.php
│   ├── giornata-corso-save.php
│   ├── export-giornate-corso-csv.php
│   ├── export-giornate-corso-excel.php
│   ├── rinuncia-save.php
│   ├── protocollo-archivia.php
│   └── protocollo-invia.php
├── ajax/
│   ├── get-calendar-events.php
│   ├── get-dashboard-stats.php
│   ├── qr-checkin.php
│   ├── search-users.php
│   ├── get-course-lessons.php
│   ├── get-teacher-hours.php
│   ├── get-reports-data.php
│   ├── get-protocollo-data.php
│   ├── get-course-days.php
│   └── get-corsista-documenti.php
├── cron/
│   ├── reminder-cron.php
│   ├── protocollo-followup-cron.php
│   └── cleanup-temp-cron.php
├── templates/
│   ├── attestati/
│   │   ├── attestato-base.php
│   │   ├── attestato-professionale.php
│   │   ├── attestato-competenze.php
│   │   └── partials/
│   │       ├── intestazione.php
│   │       ├── dati-corsista.php
│   │       ├── moduli-competenze.php
│   │       └── firme-timbri.php
│   └── documenti/
│       ├── protocollo-standard.php
│       ├── rinuncia-documento.php
│       ├── comunicazione-admin.php
│       └── partials/
│           ├── metadata.php
│           ├── destinatari.php
│           └── footer-firma.php
├── uploads/
│   ├── materiali/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── attestati/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── loghi/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── temp/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── anagrafica/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── protocolli/
│   │   ├── index.html
│   │   └── .htaccess
│   ├── documenti/
│   │   ├── index.html
│   │   └── .htaccess
│   └── export/
│       ├── index.html
│       └── .htaccess
├── database/
│   ├── schema.sql
│   ├── seed.sql
│   ├── migrations/
│   │   ├── 001_initial_schema.sql
│   │   ├── 002_protocollo.sql
│   │   ├── 003_anagrafica_estesa.sql
│   │   ├── 004_course_days.sql
│   │   └── 005_withdrawals.sql
│   └── views/
│       ├── v_dashboard_admin.sql
│       ├── v_teacher_hours.sql
│       └── v_protocollo_overview.sql
├── scripts/
│   ├── create-project.ps1
│   ├── install-dependencies.sh
│   ├── setup-local.ps1
│   └── run-quality-checks.sh
├── storage/
│   ├── logs/
│   │   ├── app.log
│   │   ├── protocollo.log
│   │   └── reminder.log
│   └── cache/
│       └── .gitkeep
├── tests/
│   ├── smoke/
│   │   ├── auth-smoke.php
│   │   ├── protocollo-smoke.php
│   │   └── attestati-smoke.php
│   └── fixtures/
│       ├── sample-protocollo.json
│       └── sample-course-days.csv
└── vendor/
    └── (dipendenze composer: dompdf, phpmailer, phpspreadsheet)
```

---

## Breve spiegazione del ruolo di ogni cartella principale

- **config/**: configurazioni centrali (DB, auth, costanti, mail), bootstrap applicativo.
- **includes/**: componenti layout condivisi e helper comuni (UI + funzioni trasversali).
- **assets/**: risorse statiche frontend (CSS/JS/immagini/stamps).
- **admin/**: area operativa completa per amministrazione, segreteria, controllo processi.
- **docente/**: area docente con lezioni assegnate, presenze, materiali, monte ore.
- **corsista/**: area corsista con corsi, calendario, quiz, attestati e profilo anagrafico.
- **actions/**: endpoint backend per create/update/delete, upload, generazione documenti ed export.
- **ajax/**: endpoint JSON per caricamenti dinamici dashboard, calendario, report, ricerca.
- **cron/**: automazioni pianificate (reminder email, manutenzione, follow-up documentale).
- **templates/**: template documentali (attestati/protocolli) per motore PDF e layout multi-sezione.
- **uploads/**: repository file runtime (materiali, attestati, protocolli, allegati anagrafica, export).
- **database/**: schema, seed, migrazioni SQL e viste per analytics/reporting.
- **scripts/**: automazioni installazione/setup/provisioning ambiente e scaffolding progetto.
- **storage/**: log applicativi e cache runtime.
- **tests/**: smoke test funzionali e fixture dati.
- **vendor/**: librerie esterne installate via Composer.
