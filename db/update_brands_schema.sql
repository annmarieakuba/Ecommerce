-- Update brands table to include category relationship
ALTER TABLE `brands` ADD COLUMN `cat_id` int(11) NOT NULL AFTER `brand_name`;
ALTER TABLE `brands` ADD KEY `cat_id` (`cat_id`);
ALTER TABLE `brands` ADD CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE;
