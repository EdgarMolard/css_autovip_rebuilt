--
-- Structure de la table `Time_Players`
--

CREATE TABLE `Time_Players` (
  `id` int(11) NOT NULL,
  `Pseudo` varchar(64) NOT NULL,
  `Time` int(11) NOT NULL,
  `SteamId` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
