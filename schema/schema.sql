CREATE TABLE `user_credentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `user_password_changes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_changed` datetime NOT NULL,
  `password_hash` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_upc_user_id_idx` (`user_id`),
  CONSTRAINT `FK_upc_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_credentials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(200) NOT NULL,
  `date_requested` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_upc_user_id_idx` (`user_id`),
  CONSTRAINT `FK_upr_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_credentials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;