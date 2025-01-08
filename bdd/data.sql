-- Utilisation de la base de données
-- \c gestion_rdv;

-- Suppression des données préexistantes
DELETE FROM rdv;
DELETE FROM medecin;
DELETE FROM patient;
DELETE FROM specialite;
DELETE FROM etablissement;

-- Insertion des données dans la table patient
INSERT INTO patient (nom, prenom, tel, email, mdp_hash) VALUES
('Lavigne', 'Oscar', '0649777963','oscar.lavigne@isen-ouest.yncrea.fr', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW'),
('Doe', 'John', '0123456789', 'doe.john@proton.me', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW'),
('White', 'Walter', '+1 5055034455', 'walter.white@icloud.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW');

-- Insertion des données dans la table specialite
INSERT INTO specialite (id, specialite) VALUES
(1, 'chirurgie'),
(2, 'podologue'),
(3, 'généraliste'),
(4, 'cardiologie'),
(5, 'soins palliatifs'),
(6, 'psychologie'),
(7, 'neurologie');

-- Insertion des données dans la table medecin
INSERT INTO medecin (id, nom, prenom, tel, code_postal, email, mdp_hash, specialite_id) VALUES
(1, 'Lemoine', 'Pierre', '0147258369', '13006', 'pierre.lemoine@email.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW', 7),
(2, 'Bernard', 'Claire', '0756348912', '33000', 'claire.bernard@email.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW', 2),
(3, 'Durand', 'Luc', '0213564897', '59000', 'luc.durand@email.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW', 1),
(4, 'Petit', 'Alice', '0612345678', '44000', 'alice.petit@email.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW', 4),
(5, 'Gauthier', 'Marc', '0698765432', '92000', 'marc.gauthier@email.com', '$2y$10$asbt1MS9W9quWCJJOFpd1ede4n4I5gpfWXjmjjCTA/2SZXHEH4lnW', 3);

-- Insertion des données dans la table etablissement
INSERT INTO etablissement (id, nom) VALUES
(1, 'Hôpital Saint-Martin'),
(2, 'Clinique du Parc Médical'),
(3, 'Centre Médical de la Santé'),
(4, 'Polyclinique Sainte-Marie'),
(5, 'Centre Hospitalier de la Riviera'),
(6, 'Maison de Santé La Providence'),
(7, 'Hôpital Universitaire de la Ville'),
(8, 'Centre de Soins Émeraude'),
(9, 'Hôpital des Grands Champs');

-- Insertion des données dans la table rdv
INSERT INTO rdv (id_patient, debut, fin, id_etablissement, id_medecin) VALUES
(1, '2024-12-15 09:00:00', '2024-12-15 09:30:00', 1, 3),
(3, '2024-12-15 10:00:00', '2024-12-15 10:45:00', 2, 5),
(NULL, '2024-12-16 14:30:00', '2024-12-16 15:00:00', 4, 2),
(NULL, '2024-12-17 08:30:00', '2024-12-17 09:00:00', 6, 1),
(NULL, '2024-12-18 11:00:00', '2024-12-18 11:45:00', 7, 3),
(3, '2024-12-18 15:00:00', '2024-12-18 15:30:00', 3, 3),
(2, '2024-12-19 09:30:00', '2024-12-19 10:00:00', 9, 1),
(3, '2024-12-19 13:00:00', '2024-12-19 13:45:00', 8, 2),
(NULL, '2025-01-06 11:00:00', '2025-01-06 11:45:00', 7, 3),
(NULL, '2025-01-09 14:30:00', '2025-01-09 14:45:00', 7, 3),
(NULL, '2025-01-09 14:45:00', '2025-01-09 15:45:00', 7, 3),
(NULL, '2025-01-09 08:00:00', '2025-01-09 09:15:00', 7, 3),
(NULL, '2025-01-10 11:00:00', '2025-01-10 11:15:00', 7, 3),
(NULL, '2025-01-13 09:30:00', '2025-01-13 09:45:00', 7, 3),
(NULL, '2025-01-15 09:30:00', '2025-01-15 10:00:00', 7, 3),
(NULL, '2025-01-16 09:30:00', '2025-01-16 10:00:00', 7, 3),
(NULL, '2025-01-16 10:00:00', '2025-01-16 10:45:00', 7, 3),
(NULL, '2025-01-17 15:30:00', '2025-01-17 16:15:00', 7, 3);