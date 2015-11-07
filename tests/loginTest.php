<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;
use \Phalcon\Exception;

/**
 * Test Class for User Login
 * Class loginTest
 * @package Tests
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class LoginTest extends \UnitTestCase
{
    private $email;
    private $password;

    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();
        //Create two new users one account active, one account inactive
        $response1 = (new User())->createUser($this->valid_test_email, $this->valid_test_password, true);
        $response2 = (new User())->createUser($this->valid_test_email_2, $this->valid_test_password, false);

        if (empty($response1) || empty($response2)) {
            die("Set up failed for login test");
        }
        parent::setUp(Di::getDefault());
    }

    public function testLogin()
    {
        //login user without email and password. This should throw an invalid user exception
        $this->email = "";
        $this->password = "";
        $this->loginAndCatchAuthenticationException();

        //set a valid email, and a wrong password
        $this->email = $this->valid_test_email;
        $this->password = 'incorrect';
        $this->loginAndCatchAuthenticationException();

        //set an invalid email, and a valid password
        $this->email = 'invalid_email@yahoo.com';
        $this->password = $this->valid_test_password;
        $this->loginAndCatchAuthenticationException();

        //Use valid credentials but an account that is inactive
        $this->email = $this->valid_test_email_2;
        $this->password = $this->valid_test_password;
        $this->loginAndCatchAuthenticationException();

        //Use valid credentials and an account that is active
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;
        $response = $this->login();
        $this->assertNotFalse($response, "Test Login Assertion: Valid email and valid password");
    }


    public function loginAndCatchAuthenticationException()
    {
        try {
            $this->login();
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown on email " . $this->email . " and password " . $this->password);
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
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