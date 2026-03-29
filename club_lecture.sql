-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 29 mars 2026 à 18:04
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `club_lecture`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `livre_id` int NOT NULL,
  `membre_id` int NOT NULL,
  `note` int DEFAULT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `cree_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mis_a_jour_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_membre_livre_avis` (`livre_id`,`membre_id`),
  KEY `membre_id` (`membre_id`)
) ;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `livre_id`, `membre_id`, `note`, `commentaire`, `cree_le`, `mis_a_jour_le`) VALUES
(1, 3, 1, 4, 'Vraiment top et touchant comme histoire', '2026-03-27 10:34:36', '2026-03-27 10:34:36');

-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `livre_id` int NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_mime` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taille` int DEFAULT NULL,
  `ajoute_par` int DEFAULT NULL,
  `ajoute_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `livre_id` (`livre_id`),
  KEY `ajoute_par` (`ajoute_par`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions_sessions`
--

DROP TABLE IF EXISTS `inscriptions_sessions`;
CREATE TABLE IF NOT EXISTS `inscriptions_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `membre_id` int NOT NULL,
  `statut` enum('Inscrit','Présent','Absent') COLLATE utf8mb4_unicode_ci DEFAULT 'Inscrit',
  `cree_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_membre_session` (`session_id`,`membre_id`),
  KEY `membre_id` (`membre_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `inscriptions_sessions`
--

INSERT INTO `inscriptions_sessions` (`id`, `session_id`, `membre_id`, `statut`, `cree_le`) VALUES
(1, 1, 2, 'Inscrit', '2026-03-29 16:35:46');

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auteur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `chemin_couverture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `cree_par` int DEFAULT NULL,
  `cree_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cree_par` (`cree_par`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id`, `titre`, `auteur`, `description`, `chemin_couverture`, `date_debut`, `date_fin`, `cree_par`, `cree_le`) VALUES
(1, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Le narrateur est un aviateur qui, à la suite d’une panne de moteur, doit se poser en catastrophe dans le désert du Sahara et tente seul de réparer son avion.', NULL, '2025-12-26', NULL, 1, '2026-03-27 08:56:31'),
(3, 'Tant d\'années perdues', 'Sandra Field', '', 'uploads/image/69c655e2dda46_OIP (1).webp', '2026-01-28', NULL, 1, '2026-03-27 10:03:14');

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

DROP TABLE IF EXISTS `membres`;
CREATE TABLE IF NOT EXISTS `membres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('Admin','Moderateur','Membre') COLLATE utf8mb4_unicode_ci DEFAULT 'Membre',
  `statut` tinyint(1) DEFAULT '1',
  `cree_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `membres`
--

INSERT INTO `membres` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `statut`, `cree_le`) VALUES
(1, 'Nana', 'irnamilongo4@gmail.com', '$2y$10$afOPdQRek7HNmPpLH7HBIOZG2uEcqXQrJWc4ufQq6SbkVZnDp2HMm', 'Admin', 1, '2026-03-13 13:15:34'),
(2, 'Leo', 'leo@gmail.com', '$2y$10$drTUkPtyR9dQ54pMlkYJ4Ofm4nzs93peLtecC2iTWqp11gNMGqA6u', 'Membre', 1, '2026-03-27 11:12:20');

-- --------------------------------------------------------

--
-- Structure de la table `progression`
--

DROP TABLE IF EXISTS `progression`;
CREATE TABLE IF NOT EXISTS `progression` (
  `id` int NOT NULL AUTO_INCREMENT,
  `livre_id` int NOT NULL,
  `membre_id` int NOT NULL,
  `pourcentage` int DEFAULT '0',
  `mis_a_jour_le` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_membre_livre_progression` (`livre_id`,`membre_id`),
  KEY `membre_id` (`membre_id`)
) ;

--
-- Déchargement des données de la table `progression`
--

INSERT INTO `progression` (`id`, `livre_id`, `membre_id`, `pourcentage`, `mis_a_jour_le`) VALUES
(1, 3, 1, 65, '2026-03-27 11:27:01'),
(2, 1, 1, 74, '2026-03-29 17:59:46');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `livre_id` int DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_heure` datetime NOT NULL,
  `lien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cree_par` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livre_id` (`livre_id`),
  KEY `cree_par` (`cree_par`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `livre_id`, `titre`, `date_heure`, `lien`, `lieu`, `description`, `cree_par`) VALUES
(1, 1, 'Débat sur le Petit Prince', '2026-04-02 18:33:00', '', 'Jardin D\'aclimatation', 'On parlera de ce que nous inspire le petit Prince', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
