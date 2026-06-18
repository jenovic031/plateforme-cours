<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$page_title = 'Connexion';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        setError('Veuillez remplir tous les champs');
    } else {
        $user = verifyLogin($email, $password);
        if ($user) {
            createSession($user);
            setSuccess('Connexion réussie !');
            redirect('/dashboard.php');
        } else {
            setError('Email ou mot de passe incorrect');
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
                <h1>🔐 Connexion</h1>
                <p>Accédez à votre compte</p>
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
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" id="password" name="password" required>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </div>
                        </form>

                        <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">

                        <p style="text-align: center; margin-top: 1.5rem;">
                            Pas encore de compte ? <a href="/register.php" style="color: var(--primary-color); font-weight: bold;">S'inscrire</a>
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
