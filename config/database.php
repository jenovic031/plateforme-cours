<?php
/**
 * Configuration de la base de données
 * Modifiez ces valeurs selon votre environnement
 */

// Paramètres de connexion
define('DB_HOST', 'localhost');          // Hôte du serveur
define('DB_USER', 'root');               // Utilisateur MySQL
define('DB_PASS', '');                   // Mot de passe MySQL
define('DB_NAME', 'plateforme_cours');   // Nom de la base de données

// Configuration du serveur
define('UPLOAD_DIR', __DIR__ . '/../uploads/');  // Répertoire des uploads
define('MAX_FILE_SIZE', 50 * 1024 * 1024);       // Taille max 50MB
define('ALLOWED_FILE_TYPE', 'application/pdf');   // Type de fichier accepté

// Configuration sécurité
define('SESSION_TIMEOUT', 3600);         // Timeout session (1h)
define('PASSWORD_MIN_LENGTH', 6);        // Longueur minimale du mot de passe

// Connexion à la base de données
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données: ' . $e->getMessage());
}

// Démarrage de la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    
    // Vérification du timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    $_SESSION['last_activity'] = time();
}
?>
