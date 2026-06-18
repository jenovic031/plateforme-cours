<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = 'Recherche';
$query = cleanInput($_GET['q'] ?? '');
$category = cleanInput($_GET['category'] ?? '');
$courses = [];

if (!empty($query)) {
    $courses = searchCourses($query, empty($category) ? null : $category, 50);
}

$categories = getCategories();
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
                <h1>🔍 Recherche de Cours</h1>
                <p>Trouvez les ressources que vous cherchez</p>
            </div>

            <div style="max-width: 700px; margin: 2rem auto;">
                <form method="GET" style="display: flex; gap: 1rem;">
                    <input type="text" name="q" placeholder="Rechercher un cours..." value="<?php echo htmlspecialchars($query); ?>" style="flex: 1; padding: 0.75rem;">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>

            <div style="margin-top: 2rem;">
                <h3 style="margin-bottom: 1rem;">Filtrer par catégorie :</h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
                    <a href="/search.php?q=<?php echo urlencode($query); ?>" class="btn" style="background-color: <?php echo empty($category) ? 'var(--primary-color)' : 'var(--light-bg)'; ?>; color: <?php echo empty($category) ? 'white' : 'var(--text-color)'; ?>;">Toutes</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/search.php?q=<?php echo urlencode($query); ?>&category=<?php echo $cat['id']; ?>" class="btn" style="background-color: <?php echo $category == $cat['id'] ? 'var(--primary-color)' : 'var(--light-bg)'; ?>; color: <?php echo $category == $cat['id'] ? 'white' : 'var(--text-color)'; ?>;"><?php echo htmlspecialchars($cat['name']); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($query)): ?>
                <div>
                    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">
                        Résultats pour "<?php echo htmlspecialchars($query); ?>" (<?php echo count($courses); ?> trouvé<?php echo count($courses) > 1 ? 's' : ''; ?>)
                    </h2>

                    <?php if (count($courses) > 0): ?>
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
                                            <div class="course-stat">👁️ <?php echo $course['views']; ?></div>
                                            <div class="course-stat">⬇️ <?php echo $course['downloads']; ?></div>
                                            <?php if ($course['avg_rating']): ?>
                                                <div class="course-stat course-rating">⭐ <?php echo number_format($course['avg_rating'], 1); ?></div>
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
                            <strong>Aucun résultat trouvé.</strong> Essayez une autre recherche.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
