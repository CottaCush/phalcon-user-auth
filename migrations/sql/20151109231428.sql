ALTER TABLE `user_password_resets`
CHANGE COLUMN `date_of_expiry` `date_of_expiry` INT(11) NULL COMMENT '' ,
ADD COLUMN `expires` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'shows whether or not this token expires' AFTER `date_requested`;
