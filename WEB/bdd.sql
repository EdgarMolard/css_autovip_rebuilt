--
-- Structure de la table `af_credits`
--

CREATE TABLE `af_credits` (
  `id` int(11) NOT NULL,
  `montant` int(11) NOT NULL,
  `prix` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `af_droits`
--

CREATE TABLE IF NOT EXISTS `af_droits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type_droit` int(1) NOT NULL,
  `date_start` int(11) NOT NULL,
  `date_fin` int(11) NOT NULL,
  `is_suspended` int(1) NOT NULL,
  `ip_serveur` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `port_serveur` int(11) NOT NULL,
  `steam_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_history_payments`
--

CREATE TABLE IF NOT EXISTS `af_history_payments` (
  `history_payment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `doc_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `awards` int(10) unsigned NOT NULL,
  `external_reference` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `promo_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`history_payment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_logs`
--

CREATE TABLE IF NOT EXISTS `af_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `action` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `membre` int(11) NOT NULL,
  `detail` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `detail2` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_news`
--

CREATE TABLE IF NOT EXISTS `af_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_title` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `news_date` int(11) NOT NULL,
  `news_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `news_auteur` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `news_edit` int(11) NOT NULL,
  `news_editauteur` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_paiements`
--

CREATE TABLE IF NOT EXISTS `af_paiements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `data` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tokens` int(1) NOT NULL,
  `date` int(11) NOT NULL,
  `paiement_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_server`
--

CREATE TABLE IF NOT EXISTS `af_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_port` int(11) NOT NULL,
  `vip_prix` int(11) NOT NULL,
  `admin_prix` int(11) NOT NULL,
  `server_added_by` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_type` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `server_manager` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rcon_password` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `af_users`
--

CREATE TABLE IF NOT EXISTS `af_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_register` int(11) NOT NULL,
  `ip_register` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lastseen` int(11) NOT NULL,
  `lastseen_ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `token` int(11) NOT NULL,
  `mini_token` int(11) NOT NULL,
  `is_suspended` int(1) NOT NULL,
  `admin_level` int(11) NOT NULL,
  `steam_id` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `suspend_reason` varchar(1028) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `suspend_admin` varchar(64) CHARACTER SET utf8 NOT NULL,
  `suspend_time` int(11) NOT NULL,
  `recovery_date` int(11) NOT NULL,
  `recovery_code` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;