SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

-- CREATE SCHEMA IF NOT EXISTS `Vente_SR_RM` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
-- USE `Vente_SR_RM` ;

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`roles`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`roles` (
--   `role_id` INT NOT NULL AUTO_INCREMENT ,
--   `role_name` VARCHAR(45) NULL ,
--   PRIMARY KEY (`role_id`) ,
--   UNIQUE INDEX `role_id_UNIQUE` (`role_id` ASC) ,
--   UNIQUE INDEX `role_name_UNIQUE` (`role_name` ASC) )
-- ENGINE = InnoDB;
-- 

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`utilisateurs`
-- -- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`utilisateurs` (
--   `nom` VARCHAR(30) NOT NULL DEFAULT '' ,
--   `prenom` VARCHAR(30) NOT NULL DEFAULT '' ,
--   `adresse1` VARCHAR(150) NOT NULL DEFAULT '' ,
--   `adresse2` VARCHAR(150) NOT NULL DEFAULT '' ,
--   `cp` VARCHAR(5) NOT NULL DEFAULT '' ,
--   `ville` VARCHAR(40) NOT NULL DEFAULT '' ,
--   `email` VARCHAR(70) NULL ,
--   `active` TINYINT(1) NULL DEFAULT 1 ,
--   `date_creation` DATETIME NULL    ,
--   `utilisateur_id` INT NOT NULL AUTO_INCREMENT ,
--   `role_id` INT NOT NULL ,
--   `password` VARCHAR(15) NULL ,
--   PRIMARY KEY (`utilisateur_id`) ,
--   UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
--   UNIQUE INDEX `utilisateur_id_UNIQUE` (`utilisateur_id` ASC) ,
--   INDEX `fk_utilisateurs_roles1` (`role_id` ASC) )
-- ENGINE = MyISAM;
-- 

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`statuts_articles`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`statuts_articles` (
--   `statut_id` INT NOT NULL ,
--   `statut` VARCHAR(45) NOT NULL DEFAULT '' ,
--   PRIMARY KEY (`statut_id`) )
-- ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`themes`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`themes` (
--   `theme_id` INT NOT NULL ,
--   `theme` VARCHAR(45) NOT NULL DEFAULT '' ,
--   `active` TINYINT(1) NULL DEFAULT 1 ,
--   `date_creation` DATETIME NULL  ,
--   PRIMARY KEY (`theme_id`) )
-- ENGINE = MyISAM;
-- 

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`formats`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`formats` (
--   `format_id` INT NOT NULL AUTO_INCREMENT ,
--   `format` VARCHAR(45) NOT NULL DEFAULT '' ,
--   PRIMARY KEY (`format_id`) )
-- ENGINE = MyISAM;
-- 
-- 
-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`articles`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`articles` (
--   `article_id` INT NOT NULL AUTO_INCREMENT ,
--   `titre` VARCHAR(100) NOT NULL DEFAULT '' ,
--   `description` TEXT NOT NULL   ,
--   `theme_id` INT NOT NULL ,
--   `format_id` INT NOT NULL ,
--   `prix` DECIMAL(5,2) NOT NULL ,
--   `date_publication` DATETIME NOT NULL  ,
--   `statut_id` INT NOT NULL ,
--   `date_modification` DATETIME NULL  ,
--   `stock` INT NULL DEFAULT 1 ,
--   `active` TINYINT(1) NULL DEFAULT '1' ,
--   PRIMARY KEY (`article_id`) ,
--   INDEX `fk_article_statut1` (`statut_id` ASC) ,
--   INDEX `fk_article_theme1` (`theme_id` ASC) ,
--   INDEX `fk_article_format1` (`format_id` ASC) )
-- ENGINE = MyISAM;
-- 

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`historique_consultation`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`historique_consultation` (
--   `session_id` INT NOT NULL ,
--   `date` DATETIME NOT NULL  ,
--   `article_id` INT NOT NULL ,
--   PRIMARY KEY (`session_id`, `article_id`, `date`) ,
--   INDEX `fk_historique_consultation_article1` (`article_id` ASC) )
-- ENGINE = MyISAM;
-- 

-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`statuts_panier`
-- -----------------------------------------------------
-- CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`statuts_panier` (
--   `statut_id` INT NOT NULL AUTO_INCREMENT ,
--   `statut` VARCHAR(45) NULL ,
--   `active` TINYINT(1) NULL DEFAULT 1 ,
--   `date_modification` DATETIME NULL   ,
--   PRIMARY KEY (`statut_id`) ,
--   UNIQUE INDEX `statut_id_UNIQUE` (`statut_id` ASC) )
-- ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`paniers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`paniers` (
  `panier_id` INT NOT NULL AUTO_INCREMENT ,
  `date_creation` INT NOT NULL ,
  `statut_id` INT NOT NULL ,
  `utilisateurs_utilisateur_id` INT NOT NULL ,
  PRIMARY KEY (`panier_id`) ,
  INDEX `fk_paniers_statuts_panier1` (`statut_id` ASC) ,
  INDEX `fk_paniers_utilisateurs1` (`utilisateurs_utilisateur_id` ASC) )
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `Vente_SR_RM`.`ligne_panier`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `Vente_SR_RM`.`ligne_panier` (
  `panier_id` INT NOT NULL ,
  `article_id` INT NOT NULL ,
  `prix` DECIMAL(5,2) NOT NULL  ,
  `date_creation` VARCHAR(45) NULL ,
  `quantite` INT NULL DEFAULT '1' ,
  PRIMARY KEY (`panier_id`, `article_id`) ,
  INDEX `fk_ligne_panier_panier1` (`panier_id` ASC) ,
  INDEX `fk_ligne_panier_article1` (`article_id` ASC) )
ENGINE = MyISAM;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
