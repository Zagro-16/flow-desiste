$ErrorActionPreference = 'Stop'

$desktop = [Environment]::GetFolderPath('Desktop')
$root = Join-Path $desktop 'FormaFlow-By-Desiste'

$directories = @(
    'config','includes','assets/css','assets/js','assets/img','assets/stamps',
    'admin','docente','corsista','actions','ajax','cron',
    'uploads/materiali','uploads/attestati','uploads/loghi','uploads/temp',
    'uploads/anagrafica','uploads/protocolli','uploads/documenti','uploads/export',
    'templates','templates/attestati','templates/documenti','database','scripts'
)

$files = @(
    '.htaccess','index.php','dashboard.php','logout.php','README.md',
    'config/config.php','config/db.php','config/auth.php','config/functions.php',
    'database/schema.sql','database/seed.sql',
    'actions/login-action.php','actions/corso-save.php','actions/lezione-save.php','actions/docente-save.php','actions/corsista-save.php',
    'actions/iscrizione-save.php','actions/presenza-save.php','actions/quiz-save.php','actions/quiz-submit.php','actions/attestato-genera.php',
    'actions/materiale-upload.php','actions/settings-save.php','actions/invia-reminder-manuale.php','actions/protocollo-save.php',
    'actions/documento-genera.php','actions/corsista-pdf-upload.php','actions/giornata-corso-save.php',
    'actions/export-giornate-corso-csv.php','actions/export-giornate-corso-excel.php','actions/rinuncia-save.php',
    'ajax/get-calendar-events.php','ajax/get-dashboard-stats.php','ajax/qr-checkin.php','ajax/search-users.php',
    'ajax/get-course-lessons.php','ajax/get-teacher-hours.php','ajax/get-reports-data.php','ajax/get-protocollo-data.php',
    'ajax/get-course-days.php','ajax/get-corsista-documenti.php','cron/reminder-cron.php',
    'admin/dashboard.php','admin/corsi.php','admin/corso-new.php','admin/corso-edit.php','admin/lezioni.php','admin/lezione-new.php',
    'admin/lezione-edit.php','admin/calendario.php','admin/docenti.php','admin/docente-new.php','admin/docente-edit.php',
    'admin/corsisti.php','admin/corsista-new.php','admin/corsista-edit.php','admin/corsista-documenti.php','admin/iscrizioni.php',
    'admin/presenze.php','admin/qr.php','admin/materiali.php','admin/quiz.php','admin/quiz-new.php','admin/attestati.php',
    'admin/comunicazioni.php','admin/report.php','admin/impostazioni.php','admin/protocollo.php','admin/protocollo-new.php',
    'admin/protocollo-view.php','admin/giornate-corso.php','admin/giornata-new.php','admin/giornata-edit.php','admin/rinunce.php','admin/export.php',
    'docente/dashboard.php','docente/calendario.php','docente/corsi.php','docente/lezioni.php','docente/presenze.php','docente/materiali.php','docente/quiz.php','docente/monteore.php','docente/profilo.php',
    'corsista/dashboard.php','corsista/corsi.php','corsista/calendario.php','corsista/materiali.php','corsista/quiz.php','corsista/attestati.php','corsista/profilo.php',
    'includes/header.php','includes/footer.php','includes/topbar.php','includes/sidebar-admin.php','includes/sidebar-docente.php','includes/sidebar-corsista.php','includes/alerts.php','includes/page-header.php',
    'assets/css/style.css','assets/css/dashboard.css','assets/css/forms.css','assets/css/tables.css','assets/css/responsive.css',
    'assets/js/app.js','assets/js/calendar.js','assets/js/qrscan.js','assets/js/quiz.js','assets/js/charts.js','assets/js/validations.js','assets/js/reminders.js','assets/js/protocollo.js','assets/js/course-days.js','assets/js/documenti.js'
)

if (!(Test-Path $root)) {
    New-Item -Path $root -ItemType Directory | Out-Null
}

foreach ($dir in $directories) {
    $dirPath = Join-Path $root $dir
    if (!(Test-Path $dirPath)) {
        New-Item -Path $dirPath -ItemType Directory | Out-Null
    }
}

foreach ($file in $files) {
    $filePath = Join-Path $root $file
    if (!(Test-Path $filePath)) {
        New-Item -Path $filePath -ItemType File | Out-Null
    }
}

Write-Host "Struttura progetto creata in: $root" -ForegroundColor Green
