# 🚀 Guide d'Installation Complet - Plateforme de Cours

## Prérequis

- **PHP 7.4+**
- **MySQL 5.7+** ou **MariaDB 10.2+**
- **Apache** avec mod_rewrite activé (ou serveur PHP built-in)
- **Composer** (optionnel)

## 📋 Étapes d'Installation

### 1️⃣ Cloner le Dépôt

```bash
git clone https://github.com/jenovic031/plateforme-cours.git
cd plateforme-cours
```

### 2️⃣ Configurer la Base de Données

#### Option A : Avec phpMyAdmin

1. Ouvrez **phpMyAdmin** (généralement `http://localhost/phpmyadmin`)
2. Cliquez sur **"Nouvelle base de données"**
3. Entrez le nom : `plateforme_cours`
4. Cliquez sur **"Créer"**
5. Sélectionnez la base `plateforme_cours`
6. Allez à l'onglet **"Importer"**
7. Choisissez le fichier `sql/database.sql`
8. Cliquez sur **"Exécuter"**

#### Option B : Avec la ligne de commande

```bash
mysql -u root -p < sql/database.sql
```

### 3️⃣ Configurer la Connexion à la Base de Données

Éditez le fichier `config/database.php` :

```php
define('DB_HOST', 'localhost');        // Votre hôte
define('DB_USER', 'root');            // Votre utilisateur MySQL
define('DB_PASS', 'votre_mot_de_passe'); // Votre mot de passe
define('DB_NAME', 'plateforme_cours');
```

### 4️⃣ Créer le Dossier des Uploads

```bash
mkdir uploads
chmod 755 uploads
```

### 5️⃣ Lancer le Serveur

#### Option A : Avec PHP Built-in (Recommandé pour le développement)

```bash
php -S localhost:8000
```

Accédez ensuite à : `http://localhost:8000`

#### Option B : Avec XAMPP/Apache

1. Placez le dossier `plateforme-cours` dans `htdocs` (XAMPP)
2. Démarrez Apache via le panneau de contrôle XAMPP
3. Accédez à : `http://localhost/plateforme-cours`

### 6️⃣ Vérifier la Configuration

Accédez à : `http://localhost:8000/test.php`

Cette page affichera les résultats des tests de configuration.

## 🔐 Identifiants par Défaut

### Administrateur
- **Email** : `admin@plateforme-cours.com`
- **Mot de passe** : `admin123`

### Utilisateur Test
- **Email** : `etudiant@example.com`
- **Mot de passe** : `etudiant123`

**⚠️ IMPORTANT** : Changez ces mots de passe après la première connexion !

## 📁 Structure des Dossiers

```
plateforme-cours/
├── config/
│   └── database.php              # Configuration BD
├── includes/
│   ├── functions.php             # Fonctions utilitaires
│   └── header.php                # Composant en-tête
├── public/
│   ├── index.php                 # Page d'accueil
│   ├── login.php                 # Connexion
│   ├── register.php              # Inscription
│   ├── dashboard.php             # Tableau de bord utilisateur
│   ├── upload.php                # Upload de cours
│   ├── search.php                # Recherche
│   ├── view-course.php           # Consultation d'un cours
│   ├── admin-dashboard.php       # Panel d'administration
│   ├── logout.php                # Déconnexion
│   ├── css/
│   │   └── style.css             # Feuille de styles
│   └── js/
│       └── script.js             # Scripts JavaScript
├── api/
│   ├── download.php              # Téléchargement de PDF
│   ├── comments.php              # Gestion des commentaires
│   └── ratings.php               # Gestion des notes
├── sql/
│   └── database.sql              # Schéma de la base de données
├── uploads/                       # Dossier des fichiers uploadés
├── .htaccess                      # Configuration Apache
├── test.php                       # Page de test
└── README.md                      # Documentation
```

## 🔧 Configuration Serveur Apache

Pour que les URLs soient propres, activez `mod_rewrite` :

```bash
# Sur Linux
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## 🐛 Dépannage

### Erreur : "Impossible de se connecter à la base de données"
- Vérifiez que MySQL est en cours d'exécution
- Vérifiez vos identifiants dans `config/database.php`
- Assurez-vous que la base de données existe

### Erreur : "Dossier uploads non accessible"
```bash
chmod 755 uploads
chown www-data:www-data uploads  # Sur Linux
```

### Les fichiers ne s'uploadent pas
- Vérifiez que `php.ini` autorise les uploads
- Vérifiez les permissions du dossier `uploads`
- Vérifiez `MAX_FILE_SIZE` dans `config/database.php`

### Erreur 404 sur les pages
- Vérifiez que `mod_rewrite` est activé
- Vérifiez que le `.htaccess` est présent
- Assurez-vous que vous accédez via le bon chemin

## 📊 Utilisation de la Plateforme

### Pour les Étudiants

1. **S'inscrire** : Cliquez sur "Inscription"
2. **Se connecter** : Utilisez vos identifiants
3. **Télécharger un cours** : Cliquez sur "+ Nouveau Cours"
4. **Rechercher des cours** : Utilisez la barre de recherche
5. **Noter et commenter** : Accédez à la page du cours

### Pour les Administrateurs

1. Se connecter avec un compte admin
2. Cliquer sur "Panel Admin" dans la navigation
3. **Modérer les uploads** : Approuver ou rejeter les cours
4. **Gérer les catégories** : Voir les stats par catégorie
5. Consulter les **statistiques globales**

## 🔒 Sécurité

### Points de Sécurité Intégrés

✅ Mots de passe hashés avec bcrypt  
✅ Protection contre les injections SQL (Prepared Statements)  
✅ Validation et nettoyage des entrées utilisateur  
✅ Sessions sécurisées avec timeout  
✅ Vérification des permissions  
✅ Validation des fichiers PDF  
✅ Limite de taille des fichiers  

### Recommandations Supplémentaires

1. **Changez les mots de passe par défaut** immédiatement
2. **Configurez HTTPS** en production
3. **Mettez à jour PHP et MySQL** régulièrement
4. **Sauvegardez votre base de données** régulièrement
5. **Limitez les permissions des fichiers** au strict nécessaire

## 📧 Support et Contributions

- Signalez les bugs via [GitHub Issues](https://github.com/jenovic031/plateforme-cours/issues)
- Proposez des améliorations via Pull Requests
- Consultez la [documentation principale](README.md)

## 📝 Licence

MIT License - Libre d'utilisation

---

**Créé par**: jenovic031  
**Dernière mise à jour**: 2026
