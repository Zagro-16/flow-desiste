# Asset grafici non versionati (binary-safe)

Per mantenere il repository aggiornabile anche su piattaforme che **non supportano commit di file binari**, i seguenti asset non sono obbligatoriamente versionati nel branch:

- `assets/img/logo.png`
- `assets/img/t.jpg`
- `assets/img/tf.jpg`

## Cosa fare in produzione / locale
Copia manualmente questi file in `assets/img/`.

## Fallback applicativi
- Se `logo.png` non esiste, la topbar mostra un fallback testuale "FF".
- Se `t.jpg` / `tf.jpg` non esistono, la generazione documenti/attestati continua con fallback testuale.
