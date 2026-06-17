-- Création de la base de données
CREATE DATABASE IF NOT EXISTS plateforme_cours;
USE plateforme_cours;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('etudiant', 'admin') DEFAULT 'etudiant',
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des cours
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category_id INT NOT NULL,
    user_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT,
    file_name VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    views INT DEFAULT 0,
    downloads INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (status),
    INDEX (category_id),
    INDEX (user_id),
    INDEX (created_at)
);

-- Table des commentaires
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (course_id),
    INDEX (user_id)
);

-- Table des notes/ratings
CREATE TABLE IF NOT EXISTS ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_rating (course_id, user_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (course_id)
);

-- Table des téléchargements (historique)
CREATE TABLE IF NOT EXISTS downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    user_id INT NOT NULL,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (course_id),
    INDEX (user_id)
);

-- Insertion de catégories par défaut
INSERT INTO categories (name, description, icon) VALUES
('Droit', 'Cours de droit et de justice', '⚖️'),
('Économie', 'Cours d\'économie et de gestion', '💰'),
('Informatique', 'Cours de programmation et informatique', '💻'),
('Mathématiques', 'Cours de mathématiques', '🔢'),
('Sciences', 'Cours de physique, chimie, biologie', '🔬'),
('Histoire', 'Cours d\'histoire et géographie', '📖'),
('Langues', 'Cours de langues étrangères', '🌐'),
('Littérature', 'Cours de littérature', '📚'),
('Médecine', 'Cours de médecine et santé', '⚕️'),
('Autres', 'Autres catégories', '📋');

-- Insertion d'un utilisateur admin par défaut
-- Username: admin | Password: admin123 (bcrypt hash)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@plateforme-cours.com', '$2y$10$YIjlrDfl2StVVZH0IemK2OPST9/PgBkqquzi.Ae8IezMYVtyStoFm', 'Administrateur', 'admin');

-- Insertion d'un utilisateur de test
-- Username: etudiant | Password: etudiant123 (bcrypt hash)
INSERT INTO users (username, email, password, full_name, role) VALUES
('etudiant', 'etudiant@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMye8Hx2xjnrXE5QJuOh8h8R2EaImZ3LBWe', 'Jean Dupont', 'etudiant');

-- Création des index pour les performances
CREATE INDEX idx_courses_status_created ON courses(status, created_at);
CREATE INDEX idx_courses_category_status ON courses(category_id, status);
CREATE INDEX idx_ratings_avg ON ratings(course_id);
CREATE INDEX idx_comments_course ON comments(course_id, created_at);
