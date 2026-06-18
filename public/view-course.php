<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$courseId = (int)($_GET['id'] ?? 0);

if ($courseId <= 0) {
    redirect('/index.php');
}

$course = getCourseById($courseId);

if (!$course || $course['status'] !== 'approved') {
    redirect('/index.php');
}

// Incrémenter les vues
incrementViews($courseId);

$page_title = htmlspecialchars($course['title']);
$comments = getCourseComments($courseId);
$userRating = isLoggedIn() ? getUserRating($courseId, $_SESSION['user_id']) : null;

// Traitement des commentaires
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {
        $commentText = cleanInput($_POST['comment_text']);
        if (addComment($courseId, $_SESSION['user_id'], $commentText)) {
            setSuccess('Commentaire ajouté !');
            redirect('/view-course.php?id=' . $courseId);
        }
    }
    
    if (isset($_POST['rating']) && !empty($_POST['rating'])) {
        $rating = (int)$_POST['rating'];
        if ($rating >= 1 && $rating <= 5) {
            addRating($courseId, $_SESSION['user_id'], $rating);
            setSuccess('Note enregistrée !');
            redirect('/view-course.php?id=' . $courseId);
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
                <h1><?php echo htmlspecialchars($course['title']); ?></h1>
                <p>Par <strong><?php echo htmlspecialchars($course['full_name']); ?></strong></p>
            </div>

            <div class="grid grid-2" style="margin-top: 2rem;">
                <div>
                    <div class="card">
                        <div class="card-body">
                            <h3>📖 Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

                            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">

                            <div class="course-stats">
                                <div style="margin-bottom: 1rem;">
                                    <strong>Catégorie :</strong> <?php echo htmlspecialchars($course['category_name']); ?>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <strong>Auteur :</strong> <?php echo htmlspecialchars($course['full_name']); ?>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <strong>Taille :</strong> <?php echo formatFileSize($course['file_size']); ?>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <strong>👁️ Vues :</strong> <?php echo $course['views']; ?>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <strong>⬇️ Téléchargements :</strong> <?php echo $course['downloads']; ?>
                                </div>
                                <?php if ($course['avg_rating']): ?>
                                    <div style="margin-bottom: 1rem;">
                                        <strong>⭐ Note moyenne :</strong> <?php echo number_format($course['avg_rating'], 1); ?>/5 (<?php echo $course['rating_count']; ?> votes)
                                    </div>
                                <?php endif; ?>
                            </div>

                            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">

                            <a href="/api/download.php?id=<?php echo $courseId; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                                ⬇️ Télécharger le PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div>
                    <?php if (isLoggedIn()): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3>⭐ Évaluer ce cours</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <label for="rating">Votre note :</label>
                                        <select id="rating" name="rating">
                                            <option value="">-- Sélectionnez une note --</option>
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <option value="<?php echo $i; ?>" <?php echo $userRating == $i ? 'selected' : ''; ?>>⭐ <?php echo $i; ?>/5</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Valider</button>
                                </form>
                            </div>
                        </div>

                        <div class="card" style="margin-top: 2rem;">
                            <div class="card-header">
                                <h3>💬 Ajouter un commentaire</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="form-group">
                                        <textarea name="comment_text" placeholder="Votre commentaire..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Commenter</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <strong>Connectez-vous</strong> pour noter et commenter ce cours.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card" style="margin-top: 3rem;">
                <div class="card-header">
                    <h3>💬 Commentaires (<?php echo count($comments); ?>)</h3>
                </div>
                <div class="card-body">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div style="border-bottom: 1px solid var(--border-color); padding: 1rem 0;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong><?php echo htmlspecialchars($comment['full_name']); ?></strong>
                                    <small style="color: var(--text-light);"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></small>
                                </div>
                                <p style="color: var(--text-color);"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-light);">Aucun commentaire pour le moment. Soyez le premier ! 🎉</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
