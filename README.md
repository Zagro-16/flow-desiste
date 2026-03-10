# FormaFlow By Desiste

Piattaforma gestionale web professionale per enti di formazione (PHP 8+, MySQL/MariaDB, HTML5/CSS3/JS Vanilla).

## Avvio rapido
1. Importa `database/schema.sql` e `database/seed.sql` su MySQL/MariaDB.
2. Configura credenziali DB in `config/db.php`.
3. Pubblica il progetto in root web (es. `htdocs/formaflow`).
4. Accedi con:
   - admin@formaflow.local / Password123!
   - docente@formaflow.local / Password123!
   - corsista@formaflow.local / Password123!

## Struttura
- `admin/`, `docente/`, `corsista/`: aree ruolo.
- `actions/`: endpoint POST (salvataggi/generazione/export).
- `ajax/`: endpoint JSON.
- `cron/reminder-cron.php`: reminder automatici.
- `uploads/`: storage documenti, attestati, protocolli, export.
