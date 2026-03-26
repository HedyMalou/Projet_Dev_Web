-- ============================================================
-- Plateforme de gestion de stages - CY Tech ING1 2025-2026
-- ANCELIN Titouan | OUERGHI Hedy | GARRA Jeremy
-- ============================================================

CREATE DATABASE IF NOT EXISTS stages_cytech
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE stages_cytech;

-- Tables Utilisateurs
CREATE TABLE UTILISATEUR (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  prenom VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('etudiant','tuteur','jury','entreprise','admin') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE AUTH_CODE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  code VARCHAR(6) NOT NULL,
  date_expiration DATETIME NOT NULL,
  utilise TINYINT(1) DEFAULT 0,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

CREATE TABLE ETUDIANT (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL UNIQUE,
  filiere VARCHAR(100) NOT NULL,
  promotion VARCHAR(10) NOT NULL,
  numero_etudiant VARCHAR(20) NOT NULL UNIQUE,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

CREATE TABLE TUTEUR (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL UNIQUE,
  departement VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

CREATE TABLE JURY (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL UNIQUE,
  specialite VARCHAR(100) NOT NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

CREATE TABLE ENTREPRISE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL UNIQUE,
  nom_entreprise VARCHAR(150) NOT NULL,
  secteur VARCHAR(100),
  adresse VARCHAR(255),
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

-- Processus Stages
CREATE TABLE OFFRE_STAGE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_entreprise INT NOT NULL,
  titre VARCHAR(200) NOT NULL,
  description TEXT,
  competences TEXT,
  duree VARCHAR(50),
  lieu VARCHAR(150),
  date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_entreprise) REFERENCES ENTREPRISE(id) ON DELETE CASCADE
);

CREATE TABLE CANDIDATURE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_etudiant INT NOT NULL,
  id_offre INT NOT NULL,
  statut ENUM('en_attente','validee','refusee') DEFAULT 'en_attente',
  date_candidature TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_etudiant) REFERENCES ETUDIANT(id) ON DELETE CASCADE,
  FOREIGN KEY (id_offre) REFERENCES OFFRE_STAGE(id) ON DELETE CASCADE
);

-- Documents et Suivi
CREATE TABLE CONVENTION (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_candidature INT NOT NULL UNIQUE,
  statut_etudiant ENUM('en_attente','signe') DEFAULT 'en_attente',
  statut_entreprise ENUM('en_attente','signe') DEFAULT 'en_attente',
  statut_tuteur ENUM('en_attente','signe') DEFAULT 'en_attente',
  FOREIGN KEY (id_candidature) REFERENCES CANDIDATURE(id) ON DELETE CASCADE
);

CREATE TABLE DOCUMENT (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_candidature INT NOT NULL,
  type ENUM('rapport','resume','fiche_evaluation','convention','autre') NOT NULL,
  chemin_fichier VARCHAR(255) NOT NULL,
  date_depot TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_candidature) REFERENCES CANDIDATURE(id) ON DELETE CASCADE
);

CREATE TABLE SUIVI (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_tuteur INT NOT NULL,
  id_candidature INT NOT NULL UNIQUE,
  note_finale DECIMAL(4,2),
  date_debut DATE,
  FOREIGN KEY (id_tuteur) REFERENCES TUTEUR(id) ON DELETE CASCADE,
  FOREIGN KEY (id_candidature) REFERENCES CANDIDATURE(id) ON DELETE CASCADE
);

CREATE TABLE COMMENTAIRE (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_candidature INT NOT NULL,
  id_utilisateur INT NOT NULL,
  contenu TEXT NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_candidature) REFERENCES CANDIDATURE(id) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);

-- Jeu de données (mdp par défaut : test1234)
INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, role) VALUES
('Ouerghi', 'Hedy', 'hedy.ouerghi@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Garra', 'Jeremy', 'jeremy.garra@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('Ancelin', 'Titouan', 'titouan.ancelin@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur'),
('Martin', 'Sophie', 'sophie.martin@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jury'),
('TechCorp', 'Admin', 'contact@techcorp.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise'),
('Admin', 'CYTech', 'admin@cytech.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO ETUDIANT (id_utilisateur, filiere, promotion, numero_etudiant) VALUES
(1, 'Informatique', 'ING1', 'ETU001'),
(2, 'Informatique', 'ING1', 'ETU002');

INSERT INTO TUTEUR (id_utilisateur, departement) VALUES
(3, 'Informatique');

INSERT INTO JURY (id_utilisateur, specialite) VALUES
(4, 'Génie Logiciel');

INSERT INTO ENTREPRISE (id_utilisateur, nom_entreprise, secteur, adresse) VALUES
(5, 'TechCorp', 'Informatique', '12 rue de la Tech, Paris');

INSERT INTO OFFRE_STAGE (id_entreprise, titre, description, competences, duree, lieu) VALUES
(1, "Développeur Web PHP", "Développement d'une plateforme interne", "PHP, MySQL, Bootstrap", "3 mois", "Paris"),
(1, "Développeur Front-end", "Intégration d'interfaces responsive", "HTML, CSS, JavaScript", "2 mois", "Remote");