-- ---------------------------------------------------------------------
-- folder_associated_tab
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `folder_associated_tab`;

CREATE TABLE `folder_associated_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `folder_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `visible` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_folder_associated_tab_folder_id` (`folder_id`),
    CONSTRAINT `fk_folder_associated_tab_folder_id`
    FOREIGN KEY (`folder_id`)
    REFERENCES `folder` (`id`)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
    ) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- category_associated_tab
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category_associated_tab`;

CREATE TABLE `category_associated_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `category_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `visible` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_category_associated_tab_category_id` (`category_id`),
    CONSTRAINT `fk_category_associated_tab_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON UPDATE RESTRICT
    ON DELETE CASCADE
    ) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- folder_associated_tab_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `folder_associated_tab_i18n`;

CREATE TABLE `folder_associated_tab_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `folder_associated_tab_i18n_fk_995849`
    FOREIGN KEY (`id`)
    REFERENCES `folder_associated_tab` (`id`)
    ON DELETE CASCADE
    ) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- category_associated_tab_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category_associated_tab_i18n`;

CREATE TABLE `category_associated_tab_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `category_associated_tab_i18n_fk_1d8247`
    FOREIGN KEY (`id`)
    REFERENCES `category_associated_tab` (`id`)
    ON DELETE CASCADE
    ) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
