<?php
/**
 * Script de test de configuration
 * Accédez à: http://localhost:8000/test.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Configuration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { color: green; padding: 1rem; background: #e8f5e9; border-radius: 4px; margin-bottom: 1rem; }
        .error { color: red; padding: 1rem; background: #ffebee; border-radius: 4px; margin-bottom: 1rem; }
        .warning { color: orange; padding: 1rem; background: #fff3e0; border-radius: 4px; margin-bottom: 1rem; }
        h2 { border-bottom: 2px solid #ccc; padding-bottom: 0.5rem; }
    </style>
</head>
<body>
    <h1>✅ Test de Configuration - Plateforme de Cours</h1>

    <?php
    $tests = [];

    // Test 1: Connexion à la base de données
    try {
        $tests[] = [
            'name' => 'Connexion à la base de données',
            'status' => 'success',
            'message' => 'Connecté à la base de données avec succès'
        ];
    } catch (Exception $e) {
        $tests[] = [
            'name' => 'Connexion à la base de données',
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    // Test 2: Dossier uploads
    if (is_dir(UPLOAD_DIR)) {
        $tests[] = [
            'name' => 'Dossier uploads',
            'status' => 'success',
            'message' => 'Le dossier uploads existe et est accessible'
        ];
    } else {
        $tests[] = [
            'name' => 'Dossier uploads',
            'status' => 'warning',
            'message' => 'Le dossier uploads n\'existe pas. Créez-le manuellement.'
        ];
    }

    // Test 3: Permissons uploads
    if (is_writable(UPLOAD_DIR)) {
        $tests[] = [
            'name' => 'Permissions d\'écriture',
            'status' => 'success',
            'message' => 'Les permissions d\'écriture sont correctes'
        ];
    } else {
        $tests[] = [
            'name' => 'Permissions d\'écriture',
            'status' => 'warning',
            'message' => 'Le dossier uploads n\'est pas accessible en écriture. Changez les permissions.'
        ];
    }

    // Test 4: Tables de la base de données
    $requiredTables = ['users', 'courses', 'categories', 'comments', 'ratings', 'downloads'];
    $existingTables = [];
    
    try {
        $result = $pdo->query('SHOW TABLES FROM ' . DB_NAME);
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $existingTables[] = $row[0];
        }
        
        $missingTables = array_diff($requiredTables, $existingTables);
        
        if (empty($missingTables)) {
            $tests[] = [
                'name' => 'Tables de la base de données',
                'status' => 'success',
                'message' => 'Toutes les tables requises existent'
            ];
        } else {
            $tests[] = [
                'name' => 'Tables de la base de données',
                'status' => 'error',
                'message' => 'Tables manquantes: ' . implode(', ', $missingTables) . '. Importez sql/database.sql'
            ];
        }
    } catch (Exception $e) {
        $tests[] = [
            'name' => 'Tables de la base de données',
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    // Test 5: Utilisateur admin
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users WHERE role = "admin"');
    $adminCount = $stmt->fetch()['count'];
    
    if ($adminCount > 0) {
        $tests[] = [
            'name' => 'Compte administrateur',
            'status' => 'success',
            'message' => 'Un compte administrateur existe'
        ];
    } else {
        $tests[] = [
            'name' => 'Compte administrateur',
            'status' => 'warning',
            'message' => 'Aucun compte administrateur trouvé. Importez sql/database.sql'
        ];
    }

    // Affichage des résultats
    foreach ($tests as $test) {
        $class = $test['status'];
        echo "<div class=\"$class\">";
        echo "<strong>" . $test['name'] . ":</strong> " . $test['message'];
        echo "</div>";
    }

    echo "<h2>📋 Informations de Configuration</h2>";
    echo "<p><strong>Base de données:</strong> " . DB_NAME . "</p>";
    echo "<p><strong>Hôte:</strong> " . DB_HOST . "</p>";
    echo "<p><strong>Dossier uploads:</strong> " . UPLOAD_DIR . "</p>";
    echo "<p><strong>Taille max fichier:</strong> " . formatFileSize(MAX_FILE_SIZE) . "</p>";

    echo "<h2>🔑 Accès Administrateur (Test)</h2>";
    echo "<p><strong>Email:</strong> admin@plateforme-cours.com</p>";
    echo "<p><strong>Mot de passe:</strong> admin123</p>";
    echo "<p style='color: red;'><strong>⚠️ Changez ce mot de passe après la première connexion !</strong></p>";
    ?>

    <hr style="margin: 2rem 0;">
    <p><a href="/index.php">← Retour à l'accueil</a></p>
</body>
</html>
