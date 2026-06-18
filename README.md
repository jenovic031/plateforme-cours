# 📚 Plateforme de Cours - Partage de Documents Académiques

Une plateforme web permettant aux étudiants de partager, télécharger et consulter des documents de cours (PDF) de manière centralisée.

## 🎯 Fonctionnalités

✅ **Authentification** - Inscription/Connexion sécurisée
✅ **Téléchargement de fichiers** - Les étudiants peuvent uploader leurs cours (PDF)
✅ **Modération** - Validation admin avant publication
✅ **Catégories** - Organisation par matières/disciplines
✅ **Recherche** - Trouver rapidement des documents
✅ **Notation & Commentaires** - Évaluer et commenter les ressources
✅ **Espace Admin** - Gestion complète de la plateforme
✅ **Sécurité** - Validation des fichiers, limite de taille

## 📋 Stack Technologique

- **Frontend** : HTML5, CSS3, JavaScript
- **Backend** : PHP 7+
- **Base de données** : MySQL 5.7+
- **Serveur** : Apache/XAMPP

## 📁 Structure du Projet

```
plateforme-cours/
├── config/
│   └── database.php          # Configuration de la base de données
├── public/
│   ├── index.php             # Page d'accueil
│   ├── login.php             # Connexion
│   ├── register.php          # Inscription
│   ├── dashboard.php         # Tableau de bord utilisateur
│   ├── upload.php            # Upload de cours
│   ├── search.php            # Recherche de documents
│   ├── view-course.php       # Consultation d'un cours
│   ├── admin-dashboard.php   # Tableau de bord admin
│   ├── css/
│   │   └── style.css         # Feuille de style
│   └── js/
│       └── script.js         # Scripts JavaScript
├── api/
│   ├── auth.php              # Authentification
│   ├── courses.php           # Gestion des cours
│   ├── upload.php            # Traitement des uploads
│   ├── comments.php          # Gestion des commentaires
│   └── ratings.php           # Gestion des notes
├── includes/
│   ├── functions.php         # Fonctions utilitaires
│   └── header.php            # En-tête/navigation
├── uploads/                  # Dossier des fichiers uploadés
├── sql/
│   └── database.sql          # Script de création BDD
└── README.md                 # Ce fichier
```

## 🚀 Installation

### 1. Cloner le dépôt
```bash
git clone https://github.com/jenovic031/plateforme-cours.git
cd plateforme-cours
```

### 2. Configurer la base de données
- Ouvrir phpMyAdmin
- Créer une nouvelle base de données : `plateforme_cours`
- Importer le fichier `sql/database.sql`

### 3. Configurer la connexion BDD
Éditer `config/database.php` avec vos identifiants :
```php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'plateforme_cours';
```

### 4. Créer le dossier uploads
```bash
mkdir uploads
chmod 755 uploads
```

### 5. Lancer le serveur
Avec XAMPP/Apache ou PHP built-in :
```bash
php -S localhost:8000
```

Accédez à : `http://localhost:8000`

## 📖 Guide d'utilisation

### Pour les étudiants
1. **S'inscrire** → Créer un compte
2. **Se connecter** → Accéder au dashboard
3. **Télécharger un cours** → Aller dans "Nouveau cours"
4. **Rechercher** → Utiliser la barre de recherche
5. **Noter/Commenter** → Évaluer les ressources

### Pour les administrateurs
1. Se connecter avec un compte admin
2. Accéder au "Panel Admin"
3. Valider les fichiers en attente
4. Gérer les catégories
5. Modérer les commentaires si nécessaire

## 🔐 Sécurité

- Mots de passe hashés (bcrypt)
- Validation des fichiers (type, taille)
- Protection contre les injections SQL
- Sessions sécurisées
- Vérification des permissions

## 📝 Limites

- **Taille max des fichiers** : 50 MB
- **Formats acceptés** : PDF uniquement
- **Modération requise** : Avant publication

## 🤝 Contribution

Les contributions sont bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Créer des pull requests

## 📧 Contact & Support

Pour toute question, créez une issue sur le dépôt GitHub.

---

**Créé par** : jenovic031  
**Licence** : MIT  
**Dernière mise à jour** : 2026
