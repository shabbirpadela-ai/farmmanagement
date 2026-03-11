-- Dove Haven Farms – MySQL Schema
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- Run once on a fresh database.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT          UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(64)           NOT NULL,
    `full_name`     VARCHAR(128)          NOT NULL,
    `email`         VARCHAR(255)          NOT NULL DEFAULT '',
    `role`          ENUM('admin','farm_manager','supervisor','sales_manager','employee') NOT NULL DEFAULT 'employee',
    `password_hash` VARCHAR(255)          NOT NULL,
    `status`        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`    TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── production_entries ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `production_entries` (
    `id`           INT          UNSIGNED NOT NULL AUTO_INCREMENT,
    `date`         DATE                  NOT NULL,
    `house_id`     VARCHAR(20)           NOT NULL,
    `crates`       SMALLINT    UNSIGNED  NOT NULL DEFAULT 0,
    `loose_eggs`   SMALLINT    UNSIGNED  NOT NULL DEFAULT 0,
    `total_eggs`   INT         UNSIGNED  NOT NULL DEFAULT 0,
    `grade_large`  INT         UNSIGNED  NOT NULL DEFAULT 0,
    `grade_medium` INT         UNSIGNED  NOT NULL DEFAULT 0,
    `grade_small`  INT         UNSIGNED  NOT NULL DEFAULT 0,
    `grade_pullet` INT         UNSIGNED  NOT NULL DEFAULT 0,
    `grade_broken` INT         UNSIGNED  NOT NULL DEFAULT 0,
    `feed_kg`      DECIMAL(8,2)          NOT NULL DEFAULT 0.00,
    `feed_type`    VARCHAR(30)           NOT NULL DEFAULT 'layer',
    `comments`     TEXT,
    `user_id`      INT         UNSIGNED,
    `created_at`   TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_prod_date`     (`date`),
    KEY `idx_prod_house`    (`house_id`),
    KEY `idx_prod_user`     (`user_id`),
    CONSTRAINT `fk_prod_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── inventory_items ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `inventory_items` (
    `id`        INT          UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`      VARCHAR(128)          NOT NULL,
    `quantity`  DECIMAL(12,2)         NOT NULL DEFAULT 0.00 COMMENT 'Current stock in kg',
    `purchased` DECIMAL(12,2)         NOT NULL DEFAULT 0.00 COMMENT 'Cumulative purchases',
    `used`      DECIMAL(12,2)         NOT NULL DEFAULT 0.00 COMMENT 'Cumulative usage',
    `supplier`  VARCHAR(128)          NOT NULL DEFAULT '',
    `unit_cost` DECIMAL(10,4)         NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_inv_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── inventory_transactions ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_id`    INT         UNSIGNED NOT NULL,
    `type`       ENUM('purchase','usage')      NOT NULL,
    `quantity`   DECIMAL(12,2)                 NOT NULL,
    `unit_cost`  DECIMAL(10,4)                 NOT NULL DEFAULT 0.00,
    `supplier`   VARCHAR(128)                  NOT NULL DEFAULT '',
    `notes`      TEXT,
    `created_at` TIMESTAMP                     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_inv_txn_item`    (`item_id`),
    KEY `idx_inv_txn_created` (`created_at`),
    CONSTRAINT `fk_inv_txn_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── customers ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `customers` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(128)         NOT NULL,
    `email`      VARCHAR(255)         NOT NULL DEFAULT '',
    `phone`      VARCHAR(30)          NOT NULL DEFAULT '',
    `address`    TEXT,
    `balance`    DECIMAL(12,2)        NOT NULL DEFAULT 0.00 COMMENT 'Outstanding amount owed',
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cust_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── orders ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
    `id`             INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id`    INT         UNSIGNED NOT NULL,
    `date`           DATE                 NOT NULL,
    `total`          DECIMAL(12,2)        NOT NULL DEFAULT 0.00,
    `paid`           DECIMAL(12,2)        NOT NULL DEFAULT 0.00,
    `balance`        DECIMAL(12,2)        NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(20)          NOT NULL DEFAULT 'cash',
    `payment_status` ENUM('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
    `notes`          TEXT,
    `created_at`     TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_date`     (`date`),
    KEY `idx_order_customer` (`customer_id`),
    KEY `idx_order_status`   (`payment_status`),
    CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── order_items ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`   INT         UNSIGNED NOT NULL,
    `product`    VARCHAR(50)          NOT NULL,
    `quantity`   INT         UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2)        NOT NULL DEFAULT 0.00,
    `line_total` DECIMAL(10,2)        NOT NULL DEFAULT 0.00,
    PRIMARY KEY (`id`),
    KEY `idx_oi_order` (`order_id`),
    CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── payments ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `payments` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`   INT         UNSIGNED NOT NULL,
    `amount`     DECIMAL(12,2)        NOT NULL,
    `method`     VARCHAR(20)          NOT NULL DEFAULT 'cash',
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pay_order` (`order_id`),
    CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── purchases ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `purchases` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `date`       DATE                 NOT NULL,
    `item_name`  VARCHAR(128)         NOT NULL,
    `category`   VARCHAR(50)          NOT NULL DEFAULT 'other',
    `quantity`   DECIMAL(12,2)        NOT NULL DEFAULT 0.00,
    `unit`       VARCHAR(20)          NOT NULL DEFAULT 'kg',
    `unit_cost`  DECIMAL(10,4)        NOT NULL DEFAULT 0.00,
    `total_cost` DECIMAL(12,2)        NOT NULL DEFAULT 0.00,
    `supplier`   VARCHAR(128)         NOT NULL DEFAULT '',
    `notes`      TEXT,
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_purchase_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── crate_stock ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `crate_stock` (
    `id`       INT         UNSIGNED NOT NULL DEFAULT 1,
    `quantity` INT         UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── crate_movements ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `crate_movements` (
    `id`         INT         UNSIGNED NOT NULL AUTO_INCREMENT,
    `type`       VARCHAR(30)          NOT NULL,
    `quantity`   INT                  NOT NULL COMMENT 'Positive=add, Negative=remove',
    `source`     VARCHAR(128)         NOT NULL DEFAULT '',
    `balance`    INT         UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cm_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Initial crate stock row ──────────────────────────────────────────────────
INSERT IGNORE INTO `crate_stock` (`id`, `quantity`) VALUES (1, 0);

SET FOREIGN_KEY_CHECKS = 1;
