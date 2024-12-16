-- Création de la base de données
-- CREATE DATABASE gestion_rdv;

-- -- Connexion à la base de données
-- \c gestion_rdv;

-- Création de la table patient
DROP TABLE IF EXISTS rdv CASCADE;
DROP TABLE IF EXISTS medecin CASCADE;
DROP TABLE IF EXISTS patient CASCADE;
DROP TABLE IF EXISTS specialite CASCADE;
DROP TABLE IF EXISTS etablissement CASCADE;


CREATE TABLE patient (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    prenom VARCHAR(150) NOT NULL,
    tel VARCHAR(15),
    email VARCHAR(255) NOT NULL,
    mdp_hash CHAR(60) NOT NULL
);

-- Création de la table specialite
CREATE TABLE specialite (
    id SERIAL PRIMARY KEY,
    specialite VARCHAR(150) NOT NULL
);

-- Création de la table medecin
CREATE TABLE medecin (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    prenom VARCHAR(150) NOT NULL,
    tel VARCHAR(15),
    code_postal CHAR(5) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mdp_hash CHAR(60) NOT NULL,
    specialite_id INT REFERENCES specialite(id) ON DELETE CASCADE
);

-- Création de la table etablissement
CREATE TABLE etablissement (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(150) NOT NULL
);

-- Création de la table rdv
CREATE TABLE rdv (
    id SERIAL PRIMARY KEY,
    id_patient INT REFERENCES patient(id) NULL,
    debut TIMESTAMP NOT NULL,
    fin TIMESTAMP NOT NULL,
    id_etablissement INT REFERENCES etablissement(id) NOT NULL,
    id_medecin INT REFERENCES medecin(id) NOT NULL
);