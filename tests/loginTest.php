<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;
use \Phalcon\Exception;

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
        //login user without email and password. This should throw an invalid user exception
        $this->email = "";
        $this->password = "";
        $this->loginAndCatchInvalidUserException();

        //set a valid email, and a wrong password
        $this->email = $this->valid_test_email;
        $this->password = 'incorrect';
        $this->loginAndCatchInvalidUserException();

        //set an invalid email, and a valid password
        $this->email = 'invalid_email@yahoo.com';
        $this->password = $this->valid_test_password;
        $this->loginAndCatchInvalidUserException();

        //Use valid credentials
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;
        $response = $this->login();
        $this->assertNotFalse($response, "Test Login Assertion: Valid email and valid password");
    }


    public function loginAndCatchInvalidUserException()
    {
        try {
            $this->login();
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown on email " . $this->email . " and password " . $this->password);
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\InvalidUserCredentialsException',$e);
        }
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