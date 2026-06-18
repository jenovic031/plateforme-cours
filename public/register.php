<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$page_title = 'Inscription';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $fullName = cleanInput($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($fullName) || empty($password) || empty($confirmPassword)) {
        setError('Veuillez remplir tous les champs');
    } elseif (!isValidUsername($username)) {
        setError('Le nom d\'utilisateur doit contenir 3-20 caractères alphanumériques');
    } elseif (!isValidEmail($email)) {
        setError('Email invalide');
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        setError('Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères');
    } elseif ($password !== $confirmPassword) {
        setError('Les mots de passe ne correspondent pas');
    } elseif (userExists($username, $email)) {
        setError('Cet utilisateur ou email existe déjà');
    } else {
        if (createUser($username, $email, $password, $fullName)) {
            setSuccess('Inscription réussie ! Vous pouvez maintenant vous connecter.');
            redirect('/login.php');
        } else {
            setError('Erreur lors de l\'inscription. Veuillez réessayer.');
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
                <h1>📝 Inscription</h1>
                <p>Créez votre compte et commencez</p>
            </div>

            <div style="max-width: 500px; margin: 3rem auto;">
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

                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Nom d'utilisateur</label>
                                <input type="text" id="username" name="username" required>
                            </div>

                            <div class="form-group">
                                <label for="full_name">Nom complet</label>
                                <input type="text" id="full_name" name="full_name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" id="password" name="password" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">S'inscrire</button>
                            </div>
                        </form>

                        <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">

                        <p style="text-align: center; margin-top: 1.5rem;">
                            Vous avez déjà un compte ? <a href="/login.php" style="color: var(--primary-color); font-weight: bold;">Se connecter</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Plateforme de Cours. Tous droits réservés.</p>
    </footer>
</body>
</html>
