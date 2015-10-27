<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;


/**
 * Test Class for User Login
 * Class loginTest
 * @package Tests
 */
class LoginTest extends \UnitTestCase
{

    private $email;
    private $password;

    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        //Create a new user
        $response = (new User())->createUser($this->valid_test_email, $this->valid_test_password);
        if (empty($response)) {
            die("Set up failed for login test");
        }
        parent::setUp(Di::getDefault());
    }


    /**
     * Delete all user's in the table
     */
    public function tearDown()
    {
        $this->clearTables();
    }


    public function testLogin()
    {
        //login user without email and password. This should return false
        $this->email = "";
        $this->password = "";
        $response = $this->login();
        $this->assertFalse($response, "Test Login Assertion: empty email and password");


        //set a valid email, and a wrong password
        $this->email = $this->valid_test_email;
        $this->password = 'incorrect';
        $response = $this->login();
        $this->assertFalse($response, "Test Login Assertion: Valid email and invalid password");

        //set an invalid email, and a valid password
        $this->email = 'invalid_email@yahoo.com';
        $this->password = $this->valid_test_password;
        $response = $this->login();
        $this->assertFalse($response, "Test Login Assertion: Invalid email and valid password");

        //Use valid credentials
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;
        $response = $this->login();
        $this->assertNotFalse($response, "Test Login Assertion: Valid email and valid password");
    }

    /**
     * Authenticate a user
     * @return bool
     */
    private function login()
    {
        $user = new User();
        return $user->authenticate($this->email, $this->password);
    }
}