<?php
require_once __DIR__ . '/../config/config.php';
requireRole(['admin']);
$pageTitle = 'Nuovo Corsista';

$corsista = [
    'id' => 0, 'nome' => '', 'cognome' => '', 'data_nascita' => '', 'nazione' => 'Italia', 'citta_nascita' => '', 'sesso' => '',
    'codice_fiscale' => '', 'residenza' => '', 'indirizzo' => '', 'cap' => '', 'provincia' => '', 'comune' => '', 'cellulare' => '',
    'mail' => '', 'titolo_studio' => '', 'iban' => '', 'data_inserimento_corsista' => date('Y-m-d'), 'data_eventuale_rinuncia' => '',
    'stato_corsista' => 'attivo', 'motivazione_rinuncia' => ''
];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/topbar.php';
?>
<div class="container-fluid"><div class="row">
<div class="col-lg-2 p-0"><?php require __DIR__ . '/../includes/sidebar-admin.php'; ?></div>
<main class="col-lg-10 p-4">
<?php require __DIR__ . '/../includes/page-header.php'; ?>
<?php require __DIR__ . '/../includes/alerts.php'; ?>
<?php require __DIR__ . '/partials/corsista-form.php'; ?>
</main></div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
