-- Création de la base de données
CREATE DATABASE IF NOT EXISTS plateforme_accompagnement_scolaire;
USE plateforme_accompagnement_scolaire;

-- Table Utilisateur
CREATE TABLE IF NOT EXISTS Utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table Abonnement
CREATE TABLE IF NOT EXISTS Abonnement (
    id_abonnement INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE
);

-- Table Matiere
CREATE TABLE IF NOT EXISTS Matiere (
    id_matiere INT AUTO_INCREMENT PRIMARY KEY,
    nom_matiere VARCHAR(100) NOT NULL,
    description TEXT
);

-- Table Chapitre
CREATE TABLE IF NOT EXISTS Chapitre (
    id_chapitre INT AUTO_INCREMENT PRIMARY KEY,
    id_matiere INT NOT NULL,
    nom_chapitre VARCHAR(100) NOT NULL,
    description TEXT,
    FOREIGN KEY (id_matiere) REFERENCES Matiere(id_matiere) ON DELETE CASCADE
);

-- Table SousChapitre
CREATE TABLE IF NOT EXISTS SousChapitre (
    id_souschapitre INT AUTO_INCREMENT PRIMARY KEY,
    id_chapitre INT NOT NULL,
    nom_souschapitre VARCHAR(100) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 1,
    FOREIGN KEY (id_chapitre) REFERENCES Chapitre(id_chapitre) ON DELETE CASCADE
);

-- Table ContenuChapitre
CREATE TABLE IF NOT EXISTS ContenuChapitre (
    id_contenu INT AUTO_INCREMENT PRIMARY KEY,
    id_chapitre INT NOT NULL,
    type_contenu ENUM('cours', 'exercice', 'quiz') NOT NULL,
    contenu TEXT,
    FOREIGN KEY (id_chapitre) REFERENCES Chapitre(id_chapitre) ON DELETE CASCADE
);

-- Table Progression
CREATE TABLE IF NOT EXISTS Progression (
    id_progression INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_chapitre INT NOT NULL,
    pourcentage_avancement INT DEFAULT 0,
    date_derniere_activite TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_chapitre) REFERENCES Chapitre(id_chapitre) ON DELETE CASCADE
);

-- Table Quiz
CREATE TABLE IF NOT EXISTS Quiz (
    id_quiz INT AUTO_INCREMENT PRIMARY KEY,
    id_chapitre INT NOT NULL,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    FOREIGN KEY (id_chapitre) REFERENCES Chapitre(id_chapitre) ON DELETE CASCADE
);

-- Table QuestionQuiz
CREATE TABLE IF NOT EXISTS QuestionQuiz (
    id_question INT AUTO_INCREMENT PRIMARY KEY,
    id_quiz INT NOT NULL,
    question TEXT NOT NULL,
    type ENUM('QCM', 'vrai/faux') NOT NULL,
    FOREIGN KEY (id_quiz) REFERENCES Quiz(id_quiz) ON DELETE CASCADE
);

-- Table Reponse
CREATE TABLE IF NOT EXISTS Reponse (
    id_reponse INT AUTO_INCREMENT PRIMARY KEY,
    id_question INT NOT NULL,
    reponse_text TEXT NOT NULL,
    correcte BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_question) REFERENCES QuestionQuiz(id_question) ON DELETE CASCADE
);
