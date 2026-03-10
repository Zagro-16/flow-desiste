<?php
/** @var array<string,mixed> $corsista */
?>
<form method="post" action="/actions/corsista-save.php" class="card shadow-sm">
    <input type="hidden" name="id" value="<?= (int)$corsista['id'] ?>">
    <div class="card-body">
        <div class="row g-3">
            <?php
            $fields = [
                'nome' => 'Nome', 'cognome' => 'Cognome', 'data_nascita' => 'Data di nascita', 'nazione' => 'Nazione',
                'citta_nascita' => 'Città di nascita', 'codice_fiscale' => 'Codice fiscale', 'residenza' => 'Residenza',
                'indirizzo' => 'Indirizzo', 'cap' => 'CAP', 'provincia' => 'Provincia', 'comune' => 'Comune',
                'cellulare' => 'Cellulare', 'mail' => 'Mail', 'titolo_studio' => 'Titolo di studio', 'iban' => 'IBAN',
                'data_inserimento_corsista' => 'Data inserimento corsista', 'data_eventuale_rinuncia' => 'Data eventuale rinuncia'
            ];
            foreach ($fields as $key => $label):
                $type = str_contains($key, 'data_') ? 'date' : 'text';
                if ($key === 'mail') { $type = 'email'; }
                ?>
                <div class="col-md-4">
                    <label class="form-label" for="<?= e($key) ?>"><?= e($label) ?></label>
                    <input type="<?= e($type) ?>" class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e((string)$corsista[$key]) ?>" <?= $key === 'data_eventuale_rinuncia' ? '' : 'required' ?>>
                </div>
            <?php endforeach; ?>

            <div class="col-md-4">
                <label class="form-label" for="sesso">Sesso</label>
                <select class="form-select" id="sesso" name="sesso" required>
                    <option value="">Seleziona</option>
                    <?php foreach (['M','F','Altro'] as $sx): ?>
                        <option value="<?= e($sx) ?>" <?= $corsista['sesso'] === $sx ? 'selected' : '' ?>><?= e($sx) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="stato_corsista">Stato corsista</label>
                <select class="form-select" id="stato_corsista" name="stato_corsista" required>
                    <?php foreach (['attivo','rinunciatario','desistente','completato'] as $st): ?>
                        <option value="<?= e($st) ?>" <?= $corsista['stato_corsista'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="motivazione_rinuncia">Motivazione rinuncia/desistenza</label>
                <input type="text" class="form-control" id="motivazione_rinuncia" name="motivazione_rinuncia" value="<?= e((string)$corsista['motivazione_rinuncia']) ?>">
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="/admin/corsisti.php" class="btn btn-outline-secondary">Torna elenco</a>
        <button type="submit" class="btn btn-primary">Salva corsista</button>
    </div>
</form>
