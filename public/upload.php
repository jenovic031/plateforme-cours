<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$page_title = 'Télécharger un Cours';
$user = getCurrentUser();
$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = cleanInput($_POST['title'] ?? '');
    $description = cleanInput($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    // Validation
    if (empty($title) || empty($description) || empty($categoryId)) {
        setError('Veuillez remplir tous les champs');
    } elseif (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        setError('Veuillez sélectionner un fichier PDF');
    } else {
        // Valider le PDF
        $validation = isValidPDF($_FILES['pdf_file']);
        if (!$validation['valid']) {
            setError($validation['error']);
        } else {
            // Sauvegarder le fichier
            $fileResult = savePDF($_FILES['pdf_file']);
            if ($fileResult['success']) {
                // Insérer dans la base de données
                $stmt = $pdo->prepare('
                    INSERT INTO courses (title, description, category_id, user_id, file_path, file_name, file_size, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, "pending")
                ');
                
                if ($stmt->execute([
                    $title,
                    $description,
                    $categoryId,
                    $user['id'],
                    $fileResult['path'],
                    $fileResult['filename'],
                    $_FILES['pdf_file']['size']
                ])) {
                    setSuccess('Cours téléchargé avec succès ! Il est actuellement en attente de modération.');
                    redirect('/dashboard.php');
                } else {
                    setError('Erreur lors de l\'enregistrement du cours');
                }
            } else {
                setError($fileResult['error']);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Plateforme de Cours</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="page-container">
        <div class="container">
            <div class="page-header">
                <h1>📤 Télécharger un Cours</h1>
                <p>Partagez vos ressources avec la communauté</p>
            </div>

            <div style="max-width: 700px; margin: 3rem auto;">
                <div class="card">
                    <div class="card-body">
                        <?php
                        $error = getError();
                        if ($error):
                        ?>
                            <div class="alert alert-error">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Titre du Cours *</label>
                                <input type="text" id="title" name="title" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea id="description" name="description" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Catégorie *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">-- Sélectionnez une catégorie --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="pdf_file">Fichier PDF *</label>
                                <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required>
                                <small style="color: var(--text-light);">Taille max: <?php echo formatFileSize(MAX_FILE_SIZE); ?></small>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Télécharger</button>
                                <a href="/dashboard.php" class="btn" style="background-color: var(--border-color); color: var(--text-color);">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="alert alert-warning" style="margin-top: 2rem;">
                    <strong>ℹ️ Important :</strong>
                    <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
                        <li>Votre cours sera d'abord modéré par un administrateur</li>
                        <li>Seuls les fichiers PDF sont acceptés</li>
                        <li>Taille maximale: <?php echo formatFileSize(MAX_FILE_SIZE); ?></li>
                        <li>Respectez les droits d'auteur</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
