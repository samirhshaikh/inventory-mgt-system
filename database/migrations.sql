ALTER TABLE `customer_sales` RENAME TO  `customers`;
ALTER TABLE `repair` RENAME TO  `tmp_repair`;
ALTER TABLE `colormaster` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `customers` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `handsetmaster` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `manufacturemaster` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `modelmaster` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `parts_supplier` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `phonestock` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `purchase` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `sales` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `salesstock` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stock_log` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `supplier` CHANGE COLUMN `Id` `id` INT(11) NOT NULL AUTO_INCREMENT;