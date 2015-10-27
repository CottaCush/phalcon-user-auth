<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;

/**
 * Test Class for User Registration
 * Class RegisterTest
 * @package Tests
 */
class RegisterTest extends \UnitTestCase
{

    private $email;
    private $password;

    /**
     * Delete all user's in the table
     */
    public function tearDown()
    {
        $this->clearTables();
    }


    public function testRegistration()
    {
        //create user without email and password. This should return false
        $this->email = "";
        $this->password = "";
        $response = $this->register();
        $this->assertFalse($response);

        //set invalid email, and set a password
        $this->email = "abc@y";
        $response = $this->register();
        $this->assertFalse($response);


        //set a valid email, and a password
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;
        $response = $this->register();
        $this->assertNotFalse($response);

        //create another user
        $this->email = 'ptega@mailinator.com';
        $response = $this->register();
        $this->assertNotFalse($response);

        //try to create the same user again, should fail because of email uniqueness validator
        $response = $this->register();
        $this->assertFalse($response);


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
        return $user->createUser($this->email, $this->password);
    }
}