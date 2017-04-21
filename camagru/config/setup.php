<?php

session_start();

require_once("database.php");

try
{
    $DB = new PDO("mysql:host=$DB_DSN;charset:utf8", $DB_USER, $DB_PASSWORD);
	$DB->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$DB->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
	$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "
CREATE DATABASE IF NOT EXISTS camagru;
use camagru;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `login` varchar(255) UNIQUE NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `mail` varchar(255) UNIQUE NOT NULL,
  `confirmkey` varchar(15) NOT NULL,
  `confirmation` int(1) DEFAULT '0'
);
CREATE TABLE IF NOT EXISTS `pictures` (
  `picture_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11),
  `title` varchar(255),
  `publish` int(1) DEFAULT '1',
  `flip` int(1),
  CONSTRAINT fk_users_to_pictures FOREIGN KEY (user_id) REFERENCES users(user_id)
);
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11),
  `picture_id` int(11),
  `comments` varchar(500) NOT NULL,
  `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_to_comments FOREIGN KEY (user_id) REFERENCES users(user_id),
  CONSTRAINT fk_pictures_to_comments FOREIGN KEY (picture_id) REFERENCES pictures(picture_id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS `likes` (
  `likes_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11),
  `picture_id` int(11),
  CONSTRAINT fk_users_to_likes FOREIGN KEY (user_id) REFERENCES users(user_id),
  CONSTRAINT fk_pictures_to_likes FOREIGN KEY (picture_id) REFERENCES pictures(picture_id) ON DELETE CASCADE
);
			";
	$DB->exec($sql);
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}

$login = (isset($_SESSION['loggued_on_user'])) ? $_SESSION['loggued_on_user'] : NULL;

?>