CREATE DATABASE IF NOT EXISTS vending_machine;

USE vending_machine;

CREATE TABLE IF NOT EXISTS `vending_machine` (
    `id` INT AUTO_INCREMENT NOT NULL,

    `state` VARCHAR(50) DEFAULT 'IDLE',
    `configuration` JSON NOT NULL,
    `slots` JSON NOT NULL,
    `change_inventory` JSON NOT NULL,
    `inserted_coins` JSON NOT NULL,
    `retained_cash` INT UNSIGNED DEFAULT 0,

    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;


INSERT IGNORE INTO `vending_machine` (`id`, `configuration`, `slots`, `change_inventory`, `inserted_coins`, `retained_cash`)
VALUES (1, '{"water": {"product": "water", "price": 65}, "soda":{"product": "soda", "price": 150}, "juice":{"product": "juice", "price": 100}}', '[]', '[]', '[]', 0);
