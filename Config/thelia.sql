
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- content_associated_tab
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `content_associated_tab`;

CREATE TABLE `content_associated_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `content_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `visible` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_content_associated_tab_content_id` (`content_id`),
    CONSTRAINT `fk_content_associated_tab_content_id`
        FOREIGN KEY (`content_id`)
        REFERENCES `content` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_associated_tab
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product_associated_tab`;

CREATE TABLE `product_associated_tab`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `product_id` INTEGER NOT NULL,
    `position` INTEGER NOT NULL,
    `visible` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_product_associated_tab_product_id` (`product_id`),
    CONSTRAINT `fk_product_associated_tab_product_id`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- content_associated_tab_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `content_associated_tab_i18n`;

CREATE TABLE `content_associated_tab_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `content_associated_tab_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `content_associated_tab` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product_associated_tab_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product_associated_tab_i18n`;

CREATE TABLE `product_associated_tab_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255),
    `description` LONGTEXT,
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `product_associated_tab_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `product_associated_tab` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
