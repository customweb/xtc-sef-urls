ALTER TABLE `admin_access` ADD `sef_urls` INT( 1 ) NOT NULL DEFAULT '0';
UPDATE `admin_access` SET `sef_urls` = '1' WHERE `admin_access`.`customers_id` = '1'