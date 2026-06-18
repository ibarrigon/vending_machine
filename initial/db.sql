USE vending_machine;

CREATE TABLE IF NOT EXISTS vending_machine (
    `id` INT AUTO_INCREMENT NOT NULL,

    `slots` JSON NOT NULL,
    `change_inventory` JSON NOT NULL,
    `inserted_coins` JSON NOT NULL,

    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
