-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema matcha
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `matcha` ;

-- -----------------------------------------------------
-- Schema matcha
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `matcha` DEFAULT CHARACTER SET utf8mb4;
USE `matcha` ;

-- -----------------------------------------------------
-- Table `matcha`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  `firstname` VARCHAR(45) NOT NULL,
  `lastname` VARCHAR(45) NOT NULL,
  `password` VARCHAR(128) NOT NULL,
  `token` VARCHAR(128) NOT NULL,
  `age` INT UNSIGNED NULL,
  `gender` ENUM('male', 'female') NULL,
  `attraction` ENUM('heterosexual', 'bisexual', 'homosexual') NOT NULL DEFAULT 'bisexual',
  `bio` LONGTEXT NULL,
  `longitude` FLOAT NOT NULL,
  `latitude` FLOAT NOT NULL,
  `postal_code` VARCHAR(255) NOT NULL,
  `country` VARCHAR(255) NOT NULL,
  `locality` VARCHAR(255) NOT NULL,
  `popularity` FLOAT NOT NULL DEFAULT 0.0,
  `lastactivity` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isconnected` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));


-- -----------------------------------------------------
-- Table `matcha`.`likes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`likes` (
  `src_user_id` INT UNSIGNED NOT NULL,
  `dest_user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`src_user_id`, `dest_user_id`),
  INDEX `fk_like_user1_idx` (`src_user_id` ASC),
  INDEX `fk_like_user2_idx` (`dest_user_id` ASC),
  CONSTRAINT `fk_like_user1`
    FOREIGN KEY (`src_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_like_user2`
    FOREIGN KEY (`dest_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`user_has_tag`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`user_has_tag` (
  `user_id` INT UNSIGNED NOT NULL,
  `tag_id` INT NOT NULL,
  INDEX `fk_user_has_tag_user1_idx` (`user_id` ASC),
  PRIMARY KEY (`user_id`, `tag_id`),
  INDEX `fk_user_has_tag_tag1_idx` (`tag_id` ASC),
  CONSTRAINT `fk_user_has_tag_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_tag_tag1`
    FOREIGN KEY (`tag_id`)
    REFERENCES `matcha`.`tag` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`chat`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`chat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`chat_message`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`chat_message` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message` LONGTEXT NULL,
  `chat_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_chat_message_chat1_idx` (`chat_id` ASC),
  INDEX `fk_chat_message_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_chat_message_chat1`
    FOREIGN KEY (`chat_id`)
    REFERENCES `matcha`.`chat` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_message_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`notification`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`notification` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `message` MEDIUMTEXT NOT NULL,
  `type` ENUM('like', 'visit', 'message', 'match', 'nomatch') NOT NULL,
  `link` VARCHAR(255) NOT NULL,
  `isread` TINYINT(1) NOT NULL DEFAULT 0,
  `create_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_notification_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_notification_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`blacklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`blacklist` (
  `src_user_id` INT UNSIGNED NOT NULL,
  `dest_user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`src_user_id`, `dest_user_id`),
  INDEX `fk_blacklist_user1_idx` (`src_user_id` ASC),
  INDEX `fk_blacklist_user2_idx` (`dest_user_id` ASC),
  CONSTRAINT `fk_blacklist_user1`
    FOREIGN KEY (`src_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_blacklist_user2`
    FOREIGN KEY (`dest_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`visited`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`visited` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `src_user_id` INT UNSIGNED NOT NULL,
  `dest_user_id` INT UNSIGNED NOT NULL,
  `visited_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `fk_visited_user1_idx` (`src_user_id` ASC),
  INDEX `fk_visited_user2_idx` (`dest_user_id` ASC),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_visited_user1`
    FOREIGN KEY (`src_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_visited_user2`
    FOREIGN KEY (`dest_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`report`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`report` (
  `src_user_id` INT UNSIGNED NOT NULL,
  `dest_user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`src_user_id`, `dest_user_id`),
  INDEX `fk_report_user1_idx` (`src_user_id` ASC),
  INDEX `fk_report_user2_idx` (`dest_user_id` ASC),
  CONSTRAINT `fk_report_user1`
    FOREIGN KEY (`src_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_report_user2`
    FOREIGN KEY (`dest_user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`user_image`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`user_image` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(255) NOT NULL,
  `position` INT NOT NULL,
  `isactive` TINYINT(1) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_image_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_image_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `matcha`.`chat_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `matcha`.`chat_user` (
  `chat_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`chat_id`, `user_id`),
  INDEX `fk_chat_user_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_chat_user_chat1`
    FOREIGN KEY (`chat_id`)
    REFERENCES `matcha`.`chat` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_chat_user_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `matcha`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
