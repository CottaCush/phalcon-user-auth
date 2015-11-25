<?php

namespace Tests;

use Phalcon\DI;
use Phalcon\Exception;
use UserAuth\Models\User;

/**
 * Test Class for User Registration
 * Class RegisterTest
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package Tests
 */
class RegisterTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();
        parent::setUp(Di::getDefault());
    }

    public function testRegistration()
    {
        //create user without email and password. This should return false
        $this->email = "";
        $this->password = "";
        $this->registerAndCatchUserCreationException();

        //set invalid email, and set a password
        $this->email = "abc@y";
        $this->registerAndCatchUserCreationException();

        //set a valid email, and a valid password but with a wrong user type id
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;
        $this->user_type_id = 100;
        $this->registerAndCatchUserCreationException();


        //reset the user type ID
        $this->user_type_id = null;
        $response = $this->register();
        $this->assertNotFalse($response);

        //create another user
        $this->email = 'ptega@mailinator.com';
        $response = $this->register();
        $this->assertNotFalse($response);

        //try to create the same user again, should fail because of email uniqueness validator
        $this->registerAndCatchUserCreationException();


        //check the number of users in the Database, this should equal to 2
        $users = User::find();
        $this->assertEquals(2, count($users->toArray()));
    }

    /**
     * Function to create a user
     * @return bool
     */
    private function register()
    {
        $user = new User();
        return $user->createUser($this->email, $this->password, false, $this->user_type_id);
    }

    public function registerAndCatchUserCreationException()
    {
        try {
            $this->register();
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown on registration for email " . $this->email . " and password " . $this->password);
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserCreationException', $e);
        }
    }
}