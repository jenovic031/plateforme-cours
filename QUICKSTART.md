# Plateforme de Cours - Guide de Démarrage Rapide

## ⚡ Installation Ultra-Rapide (5 minutes)

### 1. Cloner et Accéder
```bash
git clone https://github.com/jenovic031/plateforme-cours.git && cd plateforme-cours
```

### 2. Importer la Base de Données
- Ouvrez **phpMyAdmin**
- Créez une base `plateforme_cours`
- Importez `sql/database.sql`

### 3. Configurer les Identifiants (si nécessaire)
Éditez `config/database.php` avec vos identifiants MySQL

### 4. Créer le Dossier Uploads
```bash
mkdir uploads && chmod 755 uploads
```

### 5. Lancer le Serveur
```bash
php -S localhost:8000
```

### 6. Accéder à la Plateforme
```
http://localhost:8000
```

## 🔑 Connexion de Test

**Admin** :
- Email: `admin@plateforme-cours.com`
- Mot de passe: `admin123`

**Étudiant** :
- Email: `etudiant@example.com`
- Mot de passe: `etudiant123`

## ❓ Besoin d'Aide ?

- Consultez [INSTALLATION.md](INSTALLATION.md) pour le guide complet
- Vérifiez la configuration avec `/test.php`
- Signalez les problèmes sur GitHub

---

**Bon développement ! 🚀**
