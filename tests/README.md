User Auth Tests
=============
This is a suite of unit tests for the user auth library


Features
--------
* Test for User registration
* Test for Login
* Test for Automatic Password Generation


Contributors
------------
Tega Oghenekohwo <tega@cottacush.com>


Requirements
------------
* [PHPUNIT] (https://phpunit.de/manual/current/en/installation.html)

Installation
------------
Step 1
Create a new database named `user_auth_db`

Step 2
Update the phinx configuration file phinx.yml and set the development DB to be your test DB
Run DB migrations using Phinx from the root of your project like this like this
`php vendor/bin/phinx migrate`

Running Tests
-------------
* Navigate to the tests directory on your command line

* type ```phpunit``` to execute all tests
* type ```phpunit testClassName``` to execute a single test, e.g ```phpunit PasswordGenerationTest```