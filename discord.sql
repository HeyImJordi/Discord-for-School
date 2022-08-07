CREATE DATABASE IF NOT EXISTS `discord` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `discord`;

CREATE TABLE IF NOT EXISTS `accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) NOT NULL,
    `activation_code` varchar(50) NOT NULL DEFAULT '',
    `rememberme` varchar(255) NOT NULL DEFAULT '',
    `role` enum('Member', 'Admin') NOT NULL DEFAULT 'Member',
    `registered` datetime NOT NULL,
    `last_seen` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
