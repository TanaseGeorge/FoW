<?php
session_start();

// Distrugem sesiunea
session_destroy();

// Redirecționăm către pagina de login
header('Location: login_form.php');
exit;
?> 