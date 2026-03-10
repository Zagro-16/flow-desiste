<aside class="sidebar p-3 text-white">
    <div class="ff-sidebar-head mb-3">
        <div class="small text-uppercase text-white-50">Area</div>
        <div class="fw-bold">Docente</div>
    </div>

    <div class="ff-sidebar-group mb-2">
        <button class="ff-sidebar-toggle" data-ff-toggle="didattica-docente" type="button">Didattica</button>
        <ul class="nav flex-column gap-1 ff-sidebar-nav" data-ff-panel="didattica-docente">
            <li><a class="nav-link <?= nav_is_active('/docente/dashboard.php') ?>" href="/docente/dashboard.php">Dashboard</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/corsi.php') ?>" href="/docente/corsi.php">Corsi assegnati</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/lezioni.php') ?>" href="/docente/lezioni.php">Lezioni</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/calendario.php') ?>" href="/docente/calendario.php">Calendario</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/presenze.php') ?>" href="/docente/presenze.php">Presenze</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/materiali.php') ?>" href="/docente/materiali.php">Materiali</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/quiz.php') ?>" href="/docente/quiz.php">Quiz</a></li>
        </ul>
    </div>

    <div class="ff-sidebar-group mb-2">
        <button class="ff-sidebar-toggle" data-ff-toggle="profilo-docente" type="button">Profilo e ore</button>
        <ul class="nav flex-column gap-1 ff-sidebar-nav" data-ff-panel="profilo-docente">
            <li><a class="nav-link <?= nav_is_active('/docente/monteore.php') ?>" href="/docente/monteore.php">Monte ore</a></li>
            <li><a class="nav-link <?= nav_is_active('/docente/profilo.php') ?>" href="/docente/profilo.php">Profilo</a></li>
        </ul>
    </div>
</aside>
