User Auth
=============
This library contains functions that manages the entire process of user creation, authentication, status update and password management.


Features
--------
* User registration
* Automatic Password Generation


Contributors
------------
Tega Oghenekohwo <tega@cottacush.com>


Requirements
------------
* [Phalcon 2.0.*](https://docs.phalconphp.com/en/latest/reference/install.html)
* [Composer](https://getcomposer.org/doc/00-intro.md#using-composer)



Installation
------------
Step 1
modify your composer.json

```json
    "require": {
        ...
        "user-auth": "dev-master"
        ...
    },
    "repositories": [
         ...
        {
            "type": "vcs",
            "url":  " git@bitbucket.org:cottacush/user-auth.git"
        },
        ...
    ]
```

run `composer update`


Step 2
Run the following SQL (This will be managed later using Phinx)

```
CREATE TABLE `user_credentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULLL,
  `password` varchar(100) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `user_password_changes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_changed` datetime NOT NULL,
  `previous_hash` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_upc_user_id_idx` (`user_id`),
  CONSTRAINT `FK_upc_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_credentials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_password_reset` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(200) NOT NULL,
  `date_requested` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_upc_user_id_idx` (`user_id`),
  CONSTRAINT `FK_upr_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_credentials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
```