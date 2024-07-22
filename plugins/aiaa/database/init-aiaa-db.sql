CREATE TABLE IF NOT EXISTS `company`
(
    `companyID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name`      varchar(255)    NOT NULL,
    `phone`     varchar(255)    NOT NULL,
    `email`     varchar(255)    NOT NULL,
    `createdAt` datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt` datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`companyID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `user_company`
(
    `userCompanyID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `userID`        bigint unsigned NOT NULL,
    `companyID`     bigint unsigned NOT NULL,
    `createdAt`     datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`     datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`userCompanyID`),
    FOREIGN KEY (`userID`) REFERENCES `wp_users` (`ID`),
    FOREIGN KEY (`companyID`) REFERENCES `company` (`companyID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `locations`
(
    `locationID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `companyID`  bigint unsigned NOT NULL,
    `name`       varchar(255)    NOT NULL,
    `address`     varchar(255)    NOT NULL,
    `city`       varchar(255)    NOT NULL,
    `state`      varchar(255)    NOT NULL,
    `zip`        varchar(255)    NOT NULL,
    `createdAt`  datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`  datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`locationID`),
    FOREIGN KEY (`companyID`) REFERENCES `company` (`companyID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `invoices`
(
    `invoiceID`      bigint unsigned NOT NULL AUTO_INCREMENT,
    `name`           varchar(255)    NOT NULL,
    `dueDate`        datetime        NOT NULL,
    `amount`         decimal(10, 2)  NOT NULL,
    `status`         varchar(255)    NOT NULL,
    `quickbooksLink` varchar(255)    NOT NULL,
    `createdAt`      datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`      datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`invoiceID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `appraisals`
(
    `appraisalID`                       bigint unsigned NOT NULL AUTO_INCREMENT,
    `companyID`                         bigint unsigned NOT NULL,
    `locationID`                        bigint unsigned NOT NULL,
    `make`                              varchar(255)    NOT NULL,
    `model`                             varchar(255)    NOT NULL,
    `year`                              varchar(255)    NOT NULL,
    `vin`                               varchar(255)    NOT NULL,
    `odometer`                          varchar(255)    NOT NULL,
    `color`                             varchar(255)    NOT NULL,
    `bodyType`                          varchar(255)    NOT NULL,
    `priorNadaTradeInValue`             decimal(10, 2)  NOT NULL,
    `diminishedValueEstimate`           decimal(10, 2)  NOT NULL,
    `appraisedValue`                    decimal(10, 2)  NOT NULL,
    `overview`                          text            NOT NULL,
    `exteriorAppraisal1`                text            NOT NULL,
    `exteriorAppraisal2`                text            NOT NULL,
    `exteriorAppraisal3`                text            NOT NULL,
    `exteriorAppraisal4`                text            NOT NULL,
    `exteriorAppraisal5`                text            NOT NULL,
    `exteriorAppraisalSummary`          text            NOT NULL,
    `interiorAppraisalInstruments`      text            NOT NULL,
    `interiorAppraisalInstrumentsNotes` text            NOT NULL,
    `interiorAppraisalUpholstery`       text            NOT NULL,
    `interiorAppraisalTrim`             text            NOT NULL,
    `interiorAppraisalCarpets`          text            NOT NULL,
    `interiorAppraisalAdditionalNotes`  text            NOT NULL,
    `disclaimer`                        text            NOT NULL,
    `createdBy`                         bigint unsigned NOT NULL,
    `updatedBy`                         bigint unsigned NOT NULL,
    `approvedBy`                        bigint unsigned NOT NULL,
    `status`                            varchar(255)    NOT NULL,
    `createdAt`                         datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`                         datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`appraisalID`),
    FOREIGN KEY (`companyID`) REFERENCES `company` (`companyID`),
    FOREIGN KEY (`locationID`) REFERENCES `locations` (`locationID`),
    FOREIGN KEY (`createdBy`) REFERENCES `wp_users` (`ID`),
    FOREIGN KEY (`updatedBy`) REFERENCES `wp_users` (`ID`),
    FOREIGN KEY (`approvedBy`) REFERENCES `wp_users` (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `appraisal_invoice`
(
    `appraisalInvoiceID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `appraisalID`        bigint unsigned NOT NULL,
    `invoiceID`          bigint unsigned NOT NULL,
    `createdAt`          datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`          datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`appraisalInvoiceID`),
    FOREIGN KEY (`appraisalID`) REFERENCES `appraisals` (`appraisalID`),
    FOREIGN KEY (`invoiceID`) REFERENCES `invoices` (`invoiceID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `catalog`
(
    `catalogID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name`      varchar(255)    NOT NULL,
    `value`     text            NOT NULL,
    `createdAt` datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt` datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`catalogID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `messages`
(
    `messageID`   bigint unsigned NOT NULL AUTO_INCREMENT,
    `appraisalID` bigint unsigned NOT NULL,
    `userID`      bigint unsigned NOT NULL,
    `companyID`   bigint unsigned NOT NULL,
    `locationID`  bigint unsigned NOT NULL,
    `content`     text            NOT NULL,
    `status`      varchar(255)    NOT NULL,
    `createdAt`   datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`   datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`messageID`),
    FOREIGN KEY (`appraisalID`) REFERENCES `appraisals` (`appraisalID`),
    FOREIGN KEY (`userID`) REFERENCES `wp_users` (`ID`),
    FOREIGN KEY (`companyID`) REFERENCES `company` (`companyID`),
    FOREIGN KEY (`locationID`) REFERENCES `locations` (`locationID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `appraisals_catalog`
(
    `appraisalCatalogID` bigint unsigned NOT NULL AUTO_INCREMENT,
    `appraisalID`        bigint unsigned NOT NULL,
    `catalogID`          bigint unsigned NOT NULL,
    `createdAt`          datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`          datetime        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`appraisalCatalogID`),
    FOREIGN KEY (`appraisalID`) REFERENCES `appraisals` (`appraisalID`),
    FOREIGN KEY (`catalogID`) REFERENCES `catalog` (`catalogID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  AUTO_INCREMENT = 1;