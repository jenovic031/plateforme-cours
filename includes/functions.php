<?php
/**
 * Fonctions utilitaires de la plateforme
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifier si l'utilisateur est administrateur
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Récupérer l'utilisateur actuel
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Hash un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Valider une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valider un nom d'utilisateur
 */
function isValidUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

/**
 * Nettoyer les entrées utilisateur
 */
function cleanInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Rediriger vers une page
 */
function redirect($location) {
    header('Location: ' . $location);
    exit();
}

/**
 * Obtenir un message d'erreur
 */
function setError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Obtenir un message de succès
 */
function setSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Afficher et effacer les erreurs
 */
function getError() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

/**
 * Afficher et effacer les succès
 */
function getSuccess() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}

/**
 * Formater la taille des fichiers
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Valider un fichier PDF
 */
function isValidPDF($file) {
    // Vérifier la taille
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['valid' => false, 'error' => 'Fichier trop volumineux. Taille max: ' . formatFileSize(MAX_FILE_SIZE)];
    }
    
    // Vérifier le type MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if ($mime !== ALLOWED_FILE_TYPE) {
        return ['valid' => false, 'error' => 'Seuls les fichiers PDF sont acceptés'];
    }
    
    // Vérifier l'extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return ['valid' => false, 'error' => 'Extension de fichier invalide'];
    }
    
    return ['valid' => true];
}

/**
 * Sauvegarder un fichier uploadé
 */
function savePDF($file) {
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Générer un nom de fichier unique
    $filename = uniqid('course_') . '_' . time() . '.pdf';
    $filepath = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filepath];
    }
    
    return ['success' => false, 'error' => 'Erreur lors du téléchargement du fichier'];
}

/**
 * Obtenir les catégories
 */
function getCategories() {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
    return $stmt->fetchAll();
}

/**
 * Obtenir une catégorie par ID
 */
function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Obtenir tous les cours approuvés
 */
function getApprovedCourses($limit = 12, $offset = 0) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT c.*, u.username, u.full_name, cat.name as category_name,
               (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating,
               (SELECT COUNT(*) FROM ratings WHERE course_id = c.id) as rating_count,
               (SELECT COUNT(*) FROM comments WHERE course_id = c.id) as comment_count
        FROM courses c
        JOIN users u ON c.user_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.status = "approved"
        ORDER BY c.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Obtenir les cours en attente (pour les admins)
 */
function getPendingCourses() {
    global $pdo;
    $stmt = $pdo->query('
        SELECT c.*, u.username, u.full_name, cat.name as category_name
        FROM courses c
        JOIN users u ON c.user_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.status = "pending"
        ORDER BY c.created_at ASC
    ');
    return $stmt->fetchAll();
}

/**
 * Obtenir un cours par ID
 */
function getCourseById($id) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT c.*, u.username, u.full_name, u.id as user_id, cat.name as category_name,
               (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating,
               (SELECT COUNT(*) FROM ratings WHERE course_id = c.id) as rating_count
        FROM courses c
        JOIN users u ON c.user_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.id = ?
    ');
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Chercher des cours
 */
function searchCourses($query, $category = null, $limit = 20) {
    global $pdo;
    
    $sql = '
        SELECT c.*, u.username, u.full_name, cat.name as category_name,
               (SELECT AVG(rating) FROM ratings WHERE course_id = c.id) as avg_rating,
               (SELECT COUNT(*) FROM ratings WHERE course_id = c.id) as rating_count
        FROM courses c
        JOIN users u ON c.user_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.status = "approved" AND (c.title LIKE ? OR c.description LIKE ?)
    ';
    
    $params = ['%' . $query . '%', '%' . $query . '%'];
    
    if ($category) {
        $sql .= ' AND c.category_id = ?';
        $params[] = $category;
    }
    
    $sql .= ' ORDER BY c.created_at DESC LIMIT ?';
    $params[] = $limit;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Obtenir les commentaires d'un cours
 */
function getCourseComments($courseId) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT com.*, u.username, u.full_name
        FROM comments com
        JOIN users u ON com.user_id = u.id
        WHERE com.course_id = ?
        ORDER BY com.created_at DESC
    ');
    $stmt->execute([$courseId]);
    return $stmt->fetchAll();
}

/**
 * Ajouter un commentaire
 */
function addComment($courseId, $userId, $text) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO comments (course_id, user_id, comment_text) VALUES (?, ?, ?)');
    return $stmt->execute([$courseId, $userId, $text]);
}

/**
 * Ajouter une note
 */
function addRating($courseId, $userId, $rating) {
    global $pdo;
    $stmt = $pdo->prepare('
        INSERT INTO ratings (course_id, user_id, rating) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE rating = ?
    ');
    return $stmt->execute([$courseId, $userId, $rating, $rating]);
}

/**
 * Obtenir la note de l'utilisateur pour un cours
 */
function getUserRating($courseId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT rating FROM ratings WHERE course_id = ? AND user_id = ?');
    $stmt->execute([$courseId, $userId]);
    $result = $stmt->fetch();
    return $result ? $result['rating'] : null;
}

/**
 * Approuver un cours
 */
function approveCourse($courseId) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE courses SET status = "approved" WHERE id = ?');
    return $stmt->execute([$courseId]);
}

/**
 * Rejeter un cours
 */
function rejectCourse($courseId) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE courses SET status = "rejected" WHERE id = ?');
    return $stmt->execute([$courseId]);
}

/**
 * Supprimer un cours
 */
function deleteCourse($courseId) {
    global $pdo;
    
    // Récupérer le chemin du fichier
    $course = getCourseById($courseId);
    if ($course) {
        // Supprimer le fichier
        if (file_exists(UPLOAD_DIR . $course['file_name'])) {
            unlink(UPLOAD_DIR . $course['file_name']);
        }
    }
    
    // Supprimer le cours de la base de données
    $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
    return $stmt->execute([$courseId]);
}

/**
 * Incrémenter les vues d'un cours
 */
function incrementViews($courseId) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE courses SET views = views + 1 WHERE id = ?');
    return $stmt->execute([$courseId]);
}

/**
 * Incrémenter les téléchargements d'un cours
 */
function incrementDownloads($courseId) {
    global $pdo;
    $stmt = $pdo->prepare('UPDATE courses SET downloads = downloads + 1 WHERE id = ?');
    return $stmt->execute([$courseId]);
}

/**
 * Enregistrer un téléchargement dans l'historique
 */
function recordDownload($courseId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO downloads (course_id, user_id) VALUES (?, ?)');
    return $stmt->execute([$courseId, $userId]);
}

/**
 * Obtenir les statistiques
 */
function getStats() {
    global $pdo;
    
    $stats = [];
    
    // Nombre d'utilisateurs
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
    $stats['users'] = $stmt->fetch()['count'];
    
    // Nombre de cours
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM courses WHERE status = "approved"');
    $stats['courses'] = $stmt->fetch()['count'];
    
    // Nombre de catégories
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM categories');
    $stats['categories'] = $stmt->fetch()['count'];
    
    // Total des téléchargements
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM downloads');
    $stats['downloads'] = $stmt->fetch()['count'];
    
    return $stats;
}

/**
 * Vérifier si un utilisateur existe
 */
function userExists($username, $email) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    return $stmt->fetch()['count'] > 0;
}

/**
 * Créer un nouvel utilisateur
 */
function createUser($username, $email, $password, $fullName) {
    global $pdo;
    $hashedPassword = hashPassword($password);
    $stmt = $pdo->prepare('
        INSERT INTO users (username, email, password, full_name, role)
        VALUES (?, ?, ?, ?, "etudiant")
    ');
    return $stmt->execute([$username, $email, $hashedPassword, $fullName]);
}

/**
 * Vérifier les credentials de connexion
 */
function verifyLogin($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && verifyPassword($password, $user['password'])) {
        return $user;
    }
    return null;
}

/**
 * Créer une session utilisateur
 */
function createSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
}

/**
 * Détruire la session
 */
function destroySession() {
    session_destroy();
}
?>
