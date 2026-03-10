# FormaFlow By Desiste

Piattaforma gestionale web professionale per enti di formazione (PHP 8+, MySQL/MariaDB, HTML5/CSS3/JS Vanilla).

## Avvio rapido
1. Crea il DB `formaflow` ed esegui `database/schema.sql`.
2. Esegui `database/seed.sql` per i dati demo.
3. Se hai già importato vecchi seed, esegui anche `database/fix-demo-passwords.sql`.
4. Configura credenziali DB in `config/db.php`.
5. Pubblica il progetto in root web (es. `htdocs/formaflow`).


## Installazione guidata (consigliata)
1. Pubblica il progetto in webroot.
2. Apri `/setup/index.php`.
3. Inserisci credenziali MySQL e avvia installazione.
4. Il setup importa automaticamente:
   - `database/schema.sql`
   - `database/seed.sql`
   - `database/fix-demo-passwords.sql`

## Credenziali demo ufficiali
- admin@formaflow.local / Password123!
- docente@formaflow.local / Password123!
- corsista@formaflow.local / Password123!

## Troubleshooting login
- Verifica che la tabella `users` abbia `stato='attivo'` per gli utenti demo.
- Verifica che l'hash password corrisponda a `Password123!` (usa `database/fix-demo-passwords.sql` se necessario).
- Verifica che `config/db.php` punti al DB corretto.

## Struttura
- `admin/`, `docente/`, `corsista/`: aree ruolo.
- `actions/`: endpoint POST (salvataggi/generazione/export).
- `ajax/`: endpoint JSON.
- `cron/reminder-cron.php`: reminder automatici.
- `uploads/`: storage documenti, attestati, protocolli, export.
