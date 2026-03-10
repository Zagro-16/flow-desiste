<aside class="sidebar p-3 text-white">
    <div class="ff-sidebar-head mb-3">
        <div class="small text-uppercase text-white-50">Area</div>
        <div class="fw-bold">Corsista</div>
    </div>

    <div class="ff-sidebar-group mb-2">
        <button class="ff-sidebar-toggle" data-ff-toggle="didattica-corsista" type="button">Formazione</button>
        <ul class="nav flex-column gap-1 ff-sidebar-nav" data-ff-panel="didattica-corsista">
            <li><a class="nav-link <?= nav_is_active('/corsista/dashboard.php') ?>" href="/corsista/dashboard.php">Dashboard</a></li>
            <li><a class="nav-link <?= nav_is_active('/corsista/corsi.php') ?>" href="/corsista/corsi.php">Corso assegnato</a></li>
            <li><a class="nav-link <?= nav_is_active('/corsista/calendario.php') ?>" href="/corsista/calendario.php">Calendario</a></li>
            <li><a class="nav-link <?= nav_is_active('/corsista/materiali.php') ?>" href="/corsista/materiali.php">Materiali</a></li>
            <li><a class="nav-link <?= nav_is_active('/corsista/quiz.php') ?>" href="/corsista/quiz.php">Quiz</a></li>
            <li><a class="nav-link <?= nav_is_active('/corsista/attestati.php') ?>" href="/corsista/attestati.php">Attestati</a></li>
        </ul>
    </div>

    <div class="ff-sidebar-group mb-2">
        <button class="ff-sidebar-toggle" data-ff-toggle="profilo-corsista" type="button">Account</button>
        <ul class="nav flex-column gap-1 ff-sidebar-nav" data-ff-panel="profilo-corsista">
            <li><a class="nav-link <?= nav_is_active('/corsista/profilo.php') ?>" href="/corsista/profilo.php">Profilo</a></li>
        </ul>
    </div>
</aside>
