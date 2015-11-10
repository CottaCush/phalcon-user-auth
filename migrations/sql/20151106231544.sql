ALTER TABLE `user_password_resets`
CHANGE COLUMN `date_requested` `date_requested` DATETIME NOT NULL,
ADD COLUMN `date_of_expiry` INT(11) NOT NULL AFTER `date_requested`;