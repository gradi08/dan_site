-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql109.byetcluster.com
-- Généré le :  jeu. 24 juil. 2025 à 12:21
-- Version du serveur :  11.4.7-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `if0_39233955_vide_grenier`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `statut` enum('publie','non_publie','offert') DEFAULT 'non_publie',
  `image` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `user_id`, `titre`, `description`, `statut`, `image`, `date_creation`) VALUES
(18, 8, 'TABLE', 'je suis la pour vous', 'publie', '1749913513_WhatsApp Image 2025-06-01 à 19.33.22_dbdf9b64.jpg', '2025-06-14 17:05:13'),
(15, 6, 'carton', 'yua', 'publie', '1749907756_software-developer-6521720_1280.jpg', '2025-06-14 15:29:16'),
(16, 7, 'hhhh', 'YGDGDKQHSKJDJKH', 'publie', '1749908910_ai-generated-8881144_1280.jpg', '2025-06-14 15:48:30'),
(23, 16, 'Développer', 'je suis developpeur web et mobile pour vos site internet', 'publie', '1750760624_formation.jpg', '2025-06-24 03:23:44'),
(19, 10, 'voiture', 'je suis une voiture', 'publie', '1749915657_WhatsApp Image 2025-06-06 à 18.05.53_b98aa41d.jpg', '2025-06-14 17:40:57'),
(21, 12, 'Chemise lourde', 'Une chemise que j’aimerais offrir', 'publie', '1749941833_IMG_9009.jpeg', '2025-06-14 15:57:13'),
(22, 11, 'Livre', 'Manuel d\'introduction au droit de l\'environnement', 'publie', '1749941840_1000488588.jpg', '2025-06-14 15:57:20');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `Tel` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `Tel`, `mot_de_passe`, `date_inscription`) VALUES
(6, 'bob', 'bob@gmail.com', '+243814168498', '$2y$10$tgh6YLYYEGZJnBxFWY7Mt.byOMUtHXZk1Gjp1veeqz2.FxIxkRHDa', '2025-06-14 13:25:37'),
(7, 'grace', 'grace@gmail.com', '0814168498', '$2y$10$XxgRVUTdeNeavMYsvSrBZehZI89WAjpXH40mZzhre6.GSOiI.Io7e', '2025-06-14 15:47:43'),
(8, 'marien', 'marein@gmail.com', '0827429136', '$2y$10$Nuw/SHTGyySIgJkaTQy0ienE8Txw2qNRiPUchDLWyx36q3n9Gus8y', '2025-06-14 16:26:11'),
(9, 'gradi', 'gradi@gmail.com', '0973439644', '$2y$10$jYqsnXE0Ir/Jo5qHczPgBefsB.KcSXsXjHN3K8dt3GX9A1q4OjpxW', '2025-06-14 17:07:10'),
(10, 'marien', 'marien@gmail.com', '+243827429136', '$2y$10$./qNdhR70zM6S6BkfvDBEuzWL/PCJsW7lNdgL1l03MPY0Dt/XQPiW', '2025-06-14 17:39:46'),
(11, 'Rosine', 'lesamborosine@gmail.com', '+243998381038', '$2y$10$s1qNOhybHsQw/RtPmi8O9.zyEWsYsKWTL.y.e7cAm9r7qrfONXWmi', '2025-06-14 19:44:41'),
(12, 'Daniel', 'daniel.shidi@icloud.com', '+243811086939', '$2y$10$IdrAedLT3WxjGXMx/epZpu/z9pIYG.SsMaxoL0VwUWtCoyub0qqP.', '2025-06-14 15:55:38'),
(13, 'Manima', 'manimamarien08@gmail.com', '+243827429136', '$2y$10$Own.vuIaWFsnNh/ej.YHjOD62BM56neDMxxvz7IgisFWyAdOZq91q', '2025-06-15 04:46:07'),
(14, 'Benjamin', 'bbbbb@gmail.com', '+243000000000', '$2y$10$vzMPZI9sX.W3VIxeFOJseuEcICs.znJ3FnBDxrh2UOyVSMxxYRjGi', '2025-06-16 05:29:28'),
(15, 'Lev', 'shekinahmpanya3@gmail.com', '+243824307504', '$2y$10$2BzOfNyR6VUsB2F/irQX4ehe5d3yaxs68BjI6IZ7/QlL4ae1lXauK', '2025-06-17 04:37:27'),
(16, 'beni', 'benikasu7@gmail.com', '+243847473745', '$2y$10$cmHQwGIo843AruEeScLAxeiNxFtC/pUC0MKVkdMthSdhWYs0nLNIa', '2025-06-24 03:21:38');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
