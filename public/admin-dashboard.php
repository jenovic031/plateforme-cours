<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    redirect('/index.php');
}

$page_title = 'Panel Admin';
$user = getCurrentUser();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'approve') {
            $courseId = (int)$_POST['course_id'];
            if (approveCourse($courseId)) {
                setSuccess('Cours approuvé !');
            }
        } elseif ($_POST['action'] === 'reject') {
            $courseId = (int)$_POST['course_id'];
            if (rejectCourse($courseId)) {
                setSuccess('Cours rejeté !');
            }
        } elseif ($_POST['action'] === 'delete') {
            $courseId = (int)$_POST['course_id'];
            if (deleteCourse($courseId)) {
                setSuccess('Cours supprimé !');
            }
        }
        redirect('/admin-dashboard.php');
    }
}

$pendingCourses = getPendingCourses();
$stats = getStats();
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
                <h1>🔧 Panel d'Administration</h1>
                <p>Gérez la plateforme</p>
            </div>

            <!-- Statistiques -->
            <div class="stats" style="margin-top: 2rem;">
                <div class="stat-box">
                    <h3><?php echo $stats['courses']; ?></h3>
                    <p>Cours approuvés</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo count($pendingCourses); ?></h3>
                    <p>En attente</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Utilisateurs</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $stats['downloads']; ?></h3>
                    <p>Téléchargements</p>
                </div>
            </div>

            <!-- Cours en attente de modération -->
            <div class="card" style="margin-top: 3rem;">
                <div class="card-header">
                    <h3>⏳ Cours en Attente de Modération</h3>
                </div>
                <div class="card-body">
                    <?php if (count($pendingCourses) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Catégorie</th>
                                    <th>Taille</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingCourses as $course): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars(substr($course['title'], 0, 40)); ?></strong></td>
                                        <td><?php echo htmlspecialchars($course['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['category_name']); ?></td>
                                        <td><?php echo formatFileSize($course['file_size']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($course['created_at'])); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background-color: #4CAF50; color: white;">✓ Approuver</button>
                                            </form>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background-color: #ff9800; color: white;">✗ Rejeter</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-light);">Aucun cours en attente de modération ✓</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gestion des catégories -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3>📂 Catégories</h3>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
                    $categories = $stmt->fetchAll();
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Nombre de cours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars(substr($cat['description'] ?? '', 0, 50)); ?></td>
                                    <td>
                                        <?php
                                        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM courses WHERE category_id = ? AND status = "approved"');
                                        $stmt->execute([$cat['id']]);
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
