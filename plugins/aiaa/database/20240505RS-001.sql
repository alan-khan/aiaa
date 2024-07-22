ALTER TABLE `appraisals` ADD COLUMN `interiorAppraisalSummary` varchar(255) NULL DEFAULT NULL AFTER `interiorAppraisalAdditionalNotes`;
ALTER TABLE `appraisals` ADD COLUMN `priorNadaTradeInValueMSL` int NULL DEFAULT 0 AFTER `priorNadaTradeInValue`;
ALTER TABLE `appraisals` ADD COLUMN `priorNadaTradeInValueUnknown` int NULL DEFAULT 0 AFTER `priorNadaTradeInValueMSL`;
ALTER TABLE `appraisals` MODIFY COLUMN `priorNadaTradeInValue` varchar(255) NULL DEFAULT NULL;
ALTER TABLE `invoices` MODIFY COLUMN `amount` VARCHAR(255) DEFAULT '0.00';
