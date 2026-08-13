-- Tabs 5.0.0 — the four per-type tables collapse into a single polymorphic one.
--
-- Deliberately destructive: 4.x could not create a tab at all (position was NOT NULL with
-- no default and no code ever set it), so there is no data worth carrying over.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `product_associated_tab_i18n`;
DROP TABLE IF EXISTS `product_associated_tab`;
DROP TABLE IF EXISTS `content_associated_tab_i18n`;
DROP TABLE IF EXISTS `content_associated_tab`;
DROP TABLE IF EXISTS `category_associated_tab_i18n`;
DROP TABLE IF EXISTS `category_associated_tab`;
DROP TABLE IF EXISTS `folder_associated_tab_i18n`;
DROP TABLE IF EXISTS `folder_associated_tab`;

DROP TABLE IF EXISTS `item_associated_tab_i18n`;
DROP TABLE IF EXISTS `item_associated_tab`;

CREATE TABLE `item_associated_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `item_type` VARCHAR(32) NOT NULL,
    `item_id` INTEGER NOT NULL,
    `position` INTEGER DEFAULT 0 NOT NULL,
    `visible` TINYINT DEFAULT 1 NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_item_associated_tab_item` (`item_type`, `item_id`)
) ENGINE=InnoDB;

CREATE TABLE `item_associated_tab_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `item_associated_tab_i18n_fk_5f6d13`
        FOREIGN KEY (`id`)
        REFERENCES `item_associated_tab` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
