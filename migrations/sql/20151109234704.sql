ALTER TABLE `user_password_resets`
ADD UNIQUE INDEX `token_UNIQUE` (`token` ASC)  COMMENT '';