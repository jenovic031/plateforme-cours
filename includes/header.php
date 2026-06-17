<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/functions.php';
}

$user = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Plateforme de Cours' : 'Plateforme de Cours'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/index.php" class="logo">📚 Plateforme de Cours</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="/index.php">Accueil</a></li>
                <li><a href="/search.php">Rechercher</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <li><a href="/dashboard.php">Mon Tableau de Bord</a></li>
                    <li><a href="/upload.php" class="btn-primary">+ Nouveau Cours</a></li>
                    
                    <?php if (isAdmin()): ?>
                        <li><a href="/admin-dashboard.php">Panel Admin</a></li>
                    <?php endif; ?>
                    
                    <li class="user-menu">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="/logout.php" class="btn-logout">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <li><a href="/login.php" class="btn-primary">Connexion</a></li>
                    <li><a href="/register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php
        $error = getError();
        $success = getSuccess();
        
        if ($error): ?>
            <div class="alert alert-error">
                <strong>Erreur !</strong> <?php echo htmlspecialchars($error); ?>
                <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif;
        
        if ($success): ?>
            <div class="alert alert-success">
                <strong>Succès !</strong> <?php echo htmlspecialchars($success); ?>
                <button class="close-alert" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
