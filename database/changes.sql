CREATE TABLE App_Settings (
  UserName varchar(20) NOT NULL,
  State varchar(20) NOT NULL,
  Payload text,
  CreatedDate DATETIME DEFAULT NULL,
  CreatedBy varchar(20) DEFAULT NULL,
  UpdatedDate DATETIME DEFAULT NULL,
  PRIMARY KEY (UserName,State)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE User MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE User SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE User CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE User DROP COLUMN Id, DROP PRIMARY KEY;
ALTER TABLE User CHANGE COLUMN UserName UserName VARCHAR(250) NOT NULL, ADD PRIMARY KEY (UserName);
ALTER TABLE User CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
UPDATE User SET Password = '$2a$04$4NEJAkfmNBPZ4gDf7LDq3uz/02kQTnzhhXVEKtSyxl3lUWv1Fi8Vu' WHERE UserName IN ('admin', 'guest1', 'munir')

ALTER TABLE ColorMaster MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE ColorMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE ColorMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ColorMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ColorMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE ModelMaster MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE ModelMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE ModelMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ModelMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ModelMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE ManufactureMaster MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE ManufactureMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE ManufactureMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ManufactureMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ManufactureMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE HandsetMaster MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE HandsetMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE HandsetMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE HandsetMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE HandsetMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE Customer MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE Customer SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE Customer CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Customer CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Customer CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;

ALTER TABLE Customer_Sales MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE Customer_Sales SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE Customer_Sales CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Customer_Sales CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Customer_Sales CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;
ALTER TABLE Customer_Sales CHANGE COLUMN `CustomerName` `CustomerName` VARCHAR(350) NULL DEFAULT 'Unknown';
UPDATE Customer_Sales SET CustomerName = 'Unknown' where CustomerName = '';
Update Customer_Sales set CustomerName = trim(CustomerName) where CustomerName like ' %';


ALTER TABLE Supplier MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE Supplier SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE Supplier CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Supplier CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Supplier CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;

ALTER TABLE PhoneStock MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE PhoneStock SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE PhoneStock CHANGE COLUMN SupplyDate InvoiceDate DATETIME NULL DEFAULT NULL;
ALTER TABLE PhoneStock CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE PhoneStock CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE PhoneStock CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;
ALTER TABLE PhoneStock ADD COLUMN InvoiceId INT NULL AFTER Id;

CREATE TABLE Purchase (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `InvoiceNo` VARCHAR(50) NULL,
  `InvoiceDate` DATETIME NOT NULL,
  `SupplierId` INT NULL,
  `Comments` TEXT NULL,
  `IsActive` tinyint(1) NOT NULL,
  `CreatedDate` DATETIME NOT NULL,
  `CreatedBy` VARCHAR(20) NOT NULL,
  `UpdatedDate` DATETIME DEFAULT NULL,
  `UpdatedBy` VARCHAR(20) NULL,
  PRIMARY KEY (`Id`));
INSERT INTO Purchase SELECT Id, '', InvoiceDate, SupplierId, Comments, '1', CreatedDate, CreatedBy, UpdatedDate, UpdatedBy FROM PhoneStock ORDER BY Id;
UPDATE PhoneStock SET InvoiceId = Id;
ALTER TABLE PhoneStock DROP COLUMN `Comments`, DROP COLUMN `InvoiceDate`, DROP COLUMN `SupplierId`;

ALTER TABLE `Invoice` RENAME TO Sales;
ALTER TABLE Sales MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE Sales SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE Sales CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Sales CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Sales CHANGE COLUMN Remarks Remarks TEXT NULL DEFAULT NULL, CHANGE COLUMN AccessoriesDesc AccessoriesDesc TEXT NULL DEFAULT NULL;
ALTER TABLE Sales ADD COLUMN BusinessInvoice TINYINT NOT NULL DEFAULT 0 AFTER InvoiceDate;

ALTER TABLE `InvoiceDetails` RENAME TO salesstock;
ALTER TABLE salesstock MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE salesstock SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE salesstock CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE salesstock CHANGE COLUMN Item IMEI VARCHAR(50) NULL DEFAULT NULL;
ALTER TABLE salesstock CHANGE COLUMN UnitPrice Cost DOUBLE NULL DEFAULT NULL;
ALTER TABLE salesstock CHANGE COLUMN Description Description TEXT NULL DEFAULT NULL ;
ALTER TABLE salesstock DROP COLUMN IsActive;
ALTER TABLE salesstock DROP COLUMN SalesTax;
ALTER TABLE salesstock ADD COLUMN Returned BOOLEAN DEFAULT false AFTER Discount, ADD COLUMN ReturnedDate DATETIME DEFAULT NULL AFTER Returned;
ALTER TABLE salesstock CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;

CREATE TABLE stock_log (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `IMEI` VARCHAR(50) NOT NULL,
  `LogDate` DATETIME,
  `Comments` TEXT NULL,
  `Activity` VARCHAR(45) NOT NULL DEFAULT 'Sold',
  `CreatedDate` datetime,
  `CreatedBy` varchar(250),
  `UpdatedDate` datetime,
  `UpdatedBy` varchar(250),
  PRIMARY KEY (`Id`), INDEX (`IMEI` ASC));
INSERT INTO stock_log
(SELECT 0, IMEI, COALESCE(UpdatedDate, CreatedDate),  '', 'Sold', CreatedDate, CreatedBy, COALESCE(UpdatedDate, CreatedDate), COALESCE(UpdatedBy, CreatedBy) from salesstock Order By CreatedDate)
UNION
(SELECT 0, IMEI, COALESCE(UpdatedDate, CreatedDate),  '', Status, CreatedDate, CreatedBy, COALESCE(UpdatedDate, CreatedDate), COALESCE(UpdatedBy, CreatedBy) FROM phonestock where Status in ('Returned', 'Rejected') Order By UpdatedDate);
UPDATE stock_log SET UpdatedBy = CreatedBy where UpdatedBy = '';

ALTER TABLE AdHocReceipt MODIFY CreatedDate DATETIME, MODIFY UpdatedDate DATETIME;
-- UPDATE AdHocReceipt SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE AdHocReceipt CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE AdHocReceipt CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE AdHocReceipt CHANGE COLUMN CreatedDate CreatedDate DATETIME NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME NULL DEFAULT NULL;

DROP TABLE invoicelog;
DROP TABLE outstanding;
DROP TABLE partdetail;
DROP TABLE purchase_sales;
DROP TABLE sysdiagrams;
