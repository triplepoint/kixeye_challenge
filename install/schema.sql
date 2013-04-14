CREATE DATABASE IF NOT EXISTS `kixeye_challenge`
    DEFAULT CHARACTER SET = 'UTF8'
    DEFAULT COLLATE = 'utf8_general_ci';


use kixeye_challenge;


CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fb_id` VARCHAR(25) NOT NULL,
    `country` VARCHAR(2) NOT NULL,
    `locale` VARCHAR(5) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fb_id` (`fb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user_scores` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `score` INT UNSIGNED NOT NULL,
    `timestamp` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
