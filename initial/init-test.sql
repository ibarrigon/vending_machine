CREATE DATABASE IF NOT EXISTS vending_machine_test;

USE vending_machine_test;

CREATE TABLE IF NOT EXISTS vending_machine (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slots JSON NOT NULL,
    change_inventory JSON NOT NULL,
    inserted_coins JSON NOT NULL,
    remain_credit INT UNSIGNED DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
