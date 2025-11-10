-- Update script for extending cart and checkout workflow
-- Run this after importing dbforlab.sql to align the schema with the new checkout implementation

START TRANSACTION;

-- Ensure the cart table has a primary key and timestamps for easier management
ALTER TABLE `cart`
    ADD COLUMN `cart_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
    MODIFY `ip_add` VARCHAR(255) NOT NULL,
    MODIFY `qty` INT NOT NULL DEFAULT 1,
    ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `qty`,
    ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Capture unit prices in order details at the time of checkout
ALTER TABLE `orderdetails`
    ADD COLUMN `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `qty`;

-- Store total amounts and use alphanumeric invoice references
ALTER TABLE `orders`
    MODIFY `invoice_no` VARCHAR(50) NOT NULL,
    ADD COLUMN `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `order_status`;

-- Track simulated payment meta data
ALTER TABLE `payment`
    ADD COLUMN `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Simulated' AFTER `currency`,
    ADD COLUMN `payment_reference` VARCHAR(100) DEFAULT NULL AFTER `payment_method`;

COMMIT;

