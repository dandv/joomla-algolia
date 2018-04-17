SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;


-- -----------------------------------------------------
-- Table `#__algolia_indexer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__algolia_indexer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `extension_id` INT(11) NOT NULL,
  `application_id` VARCHAR(255) NOT NULL,
  `api_key` VARCHAR(255) NOT NULL,
  `search_key` VARCHAR(255) NOT NULL,
  `index_name` VARCHAR(100) NOT NULL,
  `asset_id` INT(10) NOT NULL DEFAULT 0,
  `state` TINYINT(2) UNSIGNED NULL DEFAULT 1,
  `params` TEXT NULL,
  `created_by` INT(10) NULL,
  `created_date` DATETIME NULL,
  `modified_by` INT(10) NULL,
  `modified_date` DATETIME NULL,
  `checked_out` INT(10) NULL,
  `checked_out_time` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_#__algolia_indexer_1_idx` (`extension_id` ASC),
  CONSTRAINT `fk_#__algolia_indexer_1`
    FOREIGN KEY (`extension_id`)
    REFERENCES `#__extensions` (`extension_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


-- -----------------------------------------------------
-- Table `#__algolia_indexer_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__algolia_indexer_item` (
  `indexer_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` INT(11) NOT NULL,
  `item_name` VARCHAR(255) NOT NULL,
  `state` TINYINT(2) UNSIGNED NULL DEFAULT 1,
  `params` TEXT NULL,
  `created_by` INT(10) NULL,
  `created_date` DATETIME NULL,
  `modified_by` INT(10) NULL,
  `modified_date` DATETIME NULL,
  `checked_out` INT(10) NULL,
  `checked_out_time` DATETIME NULL,
  PRIMARY KEY (`indexer_id`, `item_id`),
  CONSTRAINT `fk_#__algolia_indexer_item_1`
    FOREIGN KEY (`indexer_id`)
    REFERENCES `#__algolia_indexer` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);


SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
