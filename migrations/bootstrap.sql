CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `authkey` varchar(255) DEFAULT NULL,
  `cookietoken` varchar(255) DEFAULT NULL,
  `authexpiry` int(11) NOT NULL DEFAULT '0',
  `created_on` int(11) SIGNED NOT NULL,
  `last_login` int(11) SIGNED DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_role` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_u` (`user_id`,`user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `filename` VARCHAR(500) NOT NULL,
  `status` INT NOT NULL DEFAULT 0,
  `last_update` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
