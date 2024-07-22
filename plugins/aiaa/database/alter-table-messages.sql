ALTER TABLE `messages` ADD COLUMN `receiverID` bigint UNSIGNED NOT  NULL AFTER `userID`;
ALTER TABLE `messages` ADD COLUMN `parentID` bigint UNSIGNED  NOT NULL AFTER `receiverID`;
ALTER TABLE `messages` RENAME COLUMN `userID` TO `senderID`;