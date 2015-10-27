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
Create a testDB , e.g. myphalcondb_test

Step 2
Execute the SQL in the schema directory of the project's root

Running Tests
-------------
* Navigate to the tests directory on your command line

* type ```phpunit``` to execute all tests
* type ```phpunit testClassName``` to execute a single test, e.g ```phpunit PasswordGenerationTest```