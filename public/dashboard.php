<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

$page_title = 'Mon Tableau de Bord';
$user = getCurrentUser();
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
                <h1>📊 Mon Tableau de Bord</h1>
                <p>Bienvenue, <?php echo htmlspecialchars($user['full_name']); ?> !</p>
            </div>

            <div class="grid grid-2" style="margin-top: 2rem;">
                <div class="card">
                    <div class="card-header">
                        <h3>📤 Mes Uploads</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM courses WHERE user_id = ?');
                        $stmt->execute([$user['id']]);
                        $uploadCount = $stmt->fetch()['count'];
                        ?>
                        <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo $uploadCount; ?></p>
                        <p style="color: var(--text-light);">Cours téléchargés</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>⏳ En Attente</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM courses WHERE user_id = ? AND status = "pending"');
                        $stmt->execute([$user['id']]);
                        $pendingCount = $stmt->fetch()['count'];
                        ?>
                        <p style="font-size: 2rem; font-weight: bold; color: var(--warning-color);"><?php echo $pendingCount; ?></p>
                        <p style="color: var(--text-light);">Cours en attente de modération</p>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3>📚 Mes Cours</h3>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->prepare('
                        SELECT c.*, cat.name as category_name
                        FROM courses c
                        JOIN categories cat ON c.category_id = cat.id
                        WHERE c.user_id = ?
                        ORDER BY c.created_at DESC
                    ');
                    $stmt->execute([$user['id']]);
                    $courses = $stmt->fetchAll();

                    if (count($courses) > 0):
                    ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Statut</th>
                                    <th>Vues</th>
                                    <th>Téléchargements</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars(substr($course['title'], 0, 30)); ?></strong></td>
                                        <td><?php echo htmlspecialchars($course['category_name']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $course['status']; ?>">
                                                <?php echo ucfirst($course['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $course['views']; ?></td>
                                        <td><?php echo $course['downloads']; ?></td>
                                        <td>
                                            <a href="/view-course.php?id=<?php echo $course['id']; ?>" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Voir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-light);">Vous n'avez pas encore téléchargé de cours. <a href="/upload.php" style="color: var(--primary-color);">Commencez maintenant</a></p>
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
