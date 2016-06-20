User Auth
=============
This library contains functions that manages the entire process of user creation, authentication, status update and password management.


Features
--------
* User registration
* User authentication
* Automatic Password Generation


Contributors
------------
Tega Oghenekohwo <tega@cottacush.com>  
Adeyemi Olaoye <yemi@cottacush.com>


Requirements
------------
* [Phalcon 2.0.*](https://docs.phalconphp.com/en/latest/reference/install.html)
* [Composer](https://getcomposer.org/doc/00-intro.md#using-composer)



Installation
------------
Step 1  
`composer require cottacush/phalcon-user-auth`
run `composer install`

Step 2  
Run DB migrations using Phinx from the root of your project like this like this
`php vendor/bin/phinx migrate`