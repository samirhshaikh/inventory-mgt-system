CREATE TABLE AppSettings (
  UserName varchar(20) NOT NULL,
  State varchar(20) NOT NULL,
  Payload text,
  CreatedDate DATETIME DEFAULT NULL,
  CreatedBy varchar(20) DEFAULT NULL,
  UpdatedDate DATETIME DEFAULT NULL,
  PRIMARY KEY (UserName,State)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ALTER TABLE `user` RENAME TO User;
-- UPDATE User SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE User SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE User SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE User CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE User CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE User CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;

-- ALTER TABLE `colormaster` RENAME TO ColorMaster;
-- UPDATE ColorMaster SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE ColorMaster SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE ColorMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE ColorMaster CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE ColorMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ColorMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ColorMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

-- ALTER TABLE `modelmaster` RENAME TO ModelMaster;
-- UPDATE ModelMaster SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE ModelMaster SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE ModelMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE ModelMaster CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE ModelMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ModelMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ModelMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

-- ALTER TABLE `manufacturemaster` RENAME TO ManufactureMaster;
-- UPDATE ManufactureMaster SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE ManufactureMaster SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE ManufactureMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE ManufactureMaster CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE ManufactureMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE ManufactureMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE ManufactureMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

-- ALTER TABLE `handsetmaster` RENAME TO HandsetMaster;
-- UPDATE HandsetMaster SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE HandsetMaster SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE HandsetMaster SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE HandsetMaster CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE HandsetMaster CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE HandsetMaster CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE HandsetMaster CHANGE COLUMN Name Name VARCHAR(255) NULL DEFAULT NULL;

-- ALTER TABLE `customer` RENAME TO Customer;
-- UPDATE Customer SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE Customer SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE Customer SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE Customer CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE Customer CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Customer CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Customer CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;

-- ALTER TABLE `supplier` RENAME TO Supplier;
-- UPDATE Supplier SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE Supplier SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
UPDATE Supplier SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE Supplier CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
ALTER TABLE Supplier CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Supplier CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Supplier CHANGE COLUMN Comments Comments TEXT NULL DEFAULT NULL;

-- ALTER TABLE `phonestock` RENAME TO PhoneStock;
-- UPDATE PhoneStock SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE PhoneStock SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
-- UPDATE PhoneStock SET SupplyDate = UNIX_TIMESTAMP(SupplyDate);
UPDATE PhoneStock SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE PhoneStock CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL;
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
-- UPDATE Sales SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE Sales SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
-- UPDATE Sales SET InvoiceDate = UNIX_TIMESTAMP(InvoiceDate);
UPDATE Sales SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
-- ALTER TABLE Sales CHANGE COLUMN CreatedDate CreatedDate DATETIME DEFAULT NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME DEFAULT NULL, CHANGE COLUMN InvoiceDate InvoiceDate DATETIME DEFAULT NULL;
ALTER TABLE Sales CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Sales CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE Sales CHANGE COLUMN Remarks Remarks TEXT NULL DEFAULT NULL, CHANGE COLUMN AccessoriesDesc AccessoriesDesc TEXT NULL DEFAULT NULL;
ALTER TABLE Sales ADD COLUMN BusinessInvoice TINYINT NOT NULL DEFAULT 0 AFTER InvoiceDate;

CREATE TABLE stock_log (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `IMEI` VARCHAR(50) NOT NULL,
  `LogDate` DATETIME NOT NULL,
  `Comments` TEXT NULL,
  `Activity` VARCHAR(45) NOT NULL DEFAULT 'Sold',
  `CreatedDate` datetime NOT NULL,
  `CreatedBy` varchar(250) NOT NULL,
  `UpdatedDate` datetime NOT NULL,
  `UpdatedBy` varchar(250) NOT NULL,
  PRIMARY KEY (`Id`), INDEX (`IMEI` ASC));
INSERT INTO stock_log
(SELECT '', IMEI, UpdatedDate, 'Sold', CreatedDate, CreatedBy, UpdatedDate, UpdatedBy from sales Order By CreatedDate)
UNION
(SELECT '', IMEI, UpdatedDate, Status, UpdatedDate, UpdatedBy, UpdatedDate, UpdatedBy FROM phonestock where Status in ('Returned', 'Rejected') Order By UpdatedDate);
UPDATE stock_log SET UpdatedBy = CreatedBy where UpdatedBy = '';

CREATE TABLE salesstock (
    `Id` INT NOT NULL AUTO_INCREMENT,
    `InvoiceId` INT(11) NOT NULL,
    `IMEI` VARCHAR(50) NOT NULL,
    `Cost` DOUBLE NOT NULL,
    `Discount` DOUBLE NULL DEFAULT 0,
    `CreatedDate` DATETIME NOT NULL,
    `CreatedBy` VARCHAR(250) NOT NULL,
    `UpdatedDate` DATETIME NULL,
    `UpdatedBy` VARCHAR(250) NULL,
PRIMARY KEY (`Id`), INDEX (`IMEI` ASC), INDEX (`InvoiceId` ASC));
INSERT INTO salesstock SELECT '', Id, IMEI, TotalAmount, 0, CreatedDate, CreatedBy, UpdatedDate, UpdatedBy FROM Sales ORDER BY Id;
ALTER TABLE Sales DROP COLUMN `IMEI`, DROP COLUMN `TotalAmount`, DROP COLUMN `Discount`;
ALTER TABLE Sales CHANGE COLUMN `Remarks` `Comments` TEXT NULL DEFAULT NULL;
ALTER TABLE Sales CHANGE COLUMN `SalesTax` `Comments` DOUBLE NULL DEFAULT NULL;

-- ALTER TABLE `AdHocReceipt` RENAME TO AdHocReceipt;
-- UPDATE AdHocReceipt SET CreatedDate = UNIX_TIMESTAMP(CreatedDate);
-- UPDATE AdHocReceipt SET UpdatedDate = UNIX_TIMESTAMP(UpdatedDate);
-- UPDATE AdHocReceipt SET InvoiceDate = UNIX_TIMESTAMP(InvoiceDate);
UPDATE AdHocReceipt SET UpdatedDate = CreatedDate WHERE UpdatedDate = 0 OR UpdatedDate = '' OR UpdatedDate IS NULL;
ALTER TABLE AdHocReceipt CHANGE COLUMN Id Id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE AdHocReceipt CHANGE COLUMN CreatedBy CreatedBy VARCHAR(250) NULL DEFAULT NULL, CHANGE COLUMN UpdatedBy UpdatedBy VARCHAR(250) NULL DEFAULT NULL;
ALTER TABLE AdHocReceipt CHANGE COLUMN CreatedDate CreatedDate DATETIME NULL, CHANGE COLUMN UpdatedDate UpdatedDate DATETIME NULL DEFAULT NULL;

