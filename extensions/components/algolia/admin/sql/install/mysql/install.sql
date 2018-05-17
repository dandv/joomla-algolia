SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;


-- -----------------------------------------------------
-- Table `#__algolia_index`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__algolia_index` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `extension_id` INT(11) NOT NULL,
  `application_id` VARCHAR(255) NOT NULL,
  `api_key` VARCHAR(255) NOT NULL,
  `search_key` VARCHAR(255) NOT NULL,
  `index_name` VARCHAR(255) NOT NULL,
  `asset_id` INT(10) NOT NULL DEFAULT 0,
  `last_execution` DATETIME NULL,
  `state` TINYINT(2) UNSIGNED NULL DEFAULT 1,
  `params` TEXT NULL,
  `created_by` INT(10) NULL,
  `created_date` DATETIME NULL,
  `modified_by` INT(10) NULL,
  `modified_date` DATETIME NULL,
  `checked_out` INT(10) NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_#__algolia_index_1_idx` (`extension_id` ASC),
  CONSTRAINT `fk_#__algolia_index_1`
    FOREIGN KEY (`extension_id`)
    REFERENCES `#__extensions` (`extension_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `#__algolia_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__algolia_item` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `index_id` INT(11) UNSIGNED NOT NULL,
  `object_id` VARCHAR(100) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `data` MEDIUMTEXT NOT NULL,
  `state` TINYINT(2) UNSIGNED NULL DEFAULT 1,
  `params` TEXT NULL,
  `created_by` INT(10) NULL,
  `created_date` DATETIME NULL,
  `modified_by` INT(10) NULL,
  `modified_date` DATETIME NULL,
  `checked_out` INT(10) NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `index2` (`index_id` ASC, `object_id` ASC),
  CONSTRAINT `fk_#__algolia_item_1`
    FOREIGN KEY (`index_id`)
    REFERENCES `#__algolia_index` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
