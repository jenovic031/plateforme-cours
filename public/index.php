<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Accueil';
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
            <!-- Hero Section -->
            <div class="hero">
                <h1>📚 Plateforme de Cours</h1>
                <p>Accédez à une vaste collection de ressources académiques</p>
                <div class="hero-actions">
                    <?php if (!isLoggedIn()): ?>
                        <a href="/register.php" class="btn btn-primary">S'inscrire</a>
                        <a href="/login.php" class="btn btn-secondary">Se connecter</a>
                    <?php else: ?>
                        <a href="/upload.php" class="btn btn-primary">Partager un cours</a>
                        <a href="/search.php" class="btn btn-secondary">Rechercher</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistiques -->
            <?php $stats = getStats(); ?>
            <div class="stats">
                <div class="stat-box">
                    <h3><?php echo $stats['courses']; ?></h3>
                    <p>Cours disponibles</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Utilisateurs</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats['categories']; ?></h3>
                    <p>Catégories</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats['downloads']; ?></h3>
                    <p>Téléchargements</p>
                </div>
            </div>

            <!-- Cours récents -->
            <div style="margin-top: 3rem;">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">📖 Derniers cours publiés</h2>
                
                <?php
                $courses = getApprovedCourses(12, 0);
                if (count($courses) > 0):
                ?>
                    <div class="grid grid-3">
                        <?php foreach ($courses as $course): ?>
                            <div class="course-card">
                                <div class="course-header">
                                    <h3><?php echo htmlspecialchars(substr($course['title'], 0, 30)); ?></h3>
                                    <span class="course-category"><?php echo htmlspecialchars($course['category_name']); ?></span>
                                </div>
                                <div class="course-body">
                                    <div class="course-meta">
                                        <span class="course-author"><?php echo htmlspecialchars($course['full_name']); ?></span>
                                    </div>
                                    <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem;">
                                        <?php echo htmlspecialchars(substr($course['description'], 0, 80)) . '...'; ?>
                                    </p>
                                    <div class="course-stats">
                                        <div class="course-stat">
                                            👁️ <?php echo $course['views']; ?>
                                        </div>
                                        <div class="course-stat">
                                            ⬇️ <?php echo $course['downloads']; ?>
                                        </div>
                                        <?php if ($course['avg_rating']): ?>
                                            <div class="course-stat course-rating">
                                                ⭐ <?php echo number_format($course['avg_rating'], 1); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="course-footer">
                                    <a href="/view-course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Consulter</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <strong>Aucun cours disponible pour le moment.</strong> Revenez bientôt !
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
