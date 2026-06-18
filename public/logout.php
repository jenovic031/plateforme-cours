<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

destroySession();
setSuccess('Vous avez été déconnecté');
redirect('/index.php');
?>
