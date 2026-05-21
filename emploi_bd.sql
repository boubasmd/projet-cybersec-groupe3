-- ============================================
--  Base de données : emploi_bd
--  Projet Dev Web — Groupe 4
-- ============================================

DROP DATABASE IF EXISTS emploi_bd;
CREATE DATABASE emploi_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE emploi_bd;

-- --------------------------------------------
--  Table Utilisateurs
-- --------------------------------------------
CREATE TABLE Utilisateurs (
    id_utilisateurs INT AUTO_INCREMENT NOT NULL,
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe    VARCHAR(255) NOT NULL,
    role            ENUM('candidat', 'recruteur') NOT NULL DEFAULT 'candidat',
    competences     TEXT,
    description_cv  TEXT,
    CONSTRAINT pk_utilisateurs PRIMARY KEY (id_utilisateurs)
) ENGINE=InnoDB;

-- --------------------------------------------
--  Table Offre_emploi
-- --------------------------------------------
CREATE TABLE Offre_emploi (
    id_offre        INT AUTO_INCREMENT NOT NULL,
    titre           VARCHAR(200) NOT NULL,
    description     TEXT NOT NULL,
    salaire         DECIMAL(10,2),
    ville           VARCHAR(100) NOT NULL,
    type            ENUM('CDI','CDD','Interim','Stage','Alternance','Freelance') NOT NULL,
    image           VARCHAR(255),
    id_utilisateurs INT NOT NULL,
    CONSTRAINT pk_offre PRIMARY KEY (id_offre),
    CONSTRAINT fk_offre_utilisateur FOREIGN KEY (id_utilisateurs)
        REFERENCES Utilisateurs(id_utilisateurs)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------
--  Table Candidature
-- --------------------------------------------
CREATE TABLE Candidature (
    id_candidature  INT AUTO_INCREMENT NOT NULL,
    id_offre        INT NOT NULL,
    id_utilisateur  INT NOT NULL,
    date_candidature DATE NOT NULL DEFAULT (CURRENT_DATE),
    CONSTRAINT pk_candidature PRIMARY KEY (id_candidature),
    CONSTRAINT fk_candidature_offre FOREIGN KEY (id_offre)
        REFERENCES Offre_emploi(id_offre)
        ON DELETE CASCADE,
    CONSTRAINT fk_candidature_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES Utilisateurs(id_utilisateurs)
        ON DELETE CASCADE,
    UNIQUE (id_utilisateur, id_offre)
) ENGINE=InnoDB;

-- --------------------------------------------
--  Table Favoris
-- --------------------------------------------
CREATE TABLE Favoris (
    id_favoris      INT AUTO_INCREMENT NOT NULL,
    id_offre        INT NOT NULL,
    id_utilisateur  INT NOT NULL,
    CONSTRAINT pk_favoris PRIMARY KEY (id_favoris),
    CONSTRAINT fk_favoris_offre FOREIGN KEY (id_offre)
        REFERENCES Offre_emploi(id_offre)
        ON DELETE CASCADE,
    CONSTRAINT fk_favoris_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES Utilisateurs(id_utilisateurs)
        ON DELETE CASCADE,
    UNIQUE (id_utilisateur, id_offre)
) ENGINE=InnoDB;
