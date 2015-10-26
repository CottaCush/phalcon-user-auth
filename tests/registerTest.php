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

    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        parent::setUp(Di::getDefault());
    }

    /**
     * Delete all user's in the table
     */
    public function tearDown()
    {
        foreach (User::find() as $user) {
            if ($user->delete() == false) {
                echo "Sorry, we can't delete the user right now: \n";

                foreach ($user->getMessages() as $message) {
                    echo $message, "\n";
                }
            }
        }
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
        $this->email = 'test123@yahoo.com';
        $this->password = 'test';
        $response = $this->register();
        $this->assertNotFalse($response);

        //create another user
        $this->email = 'test1234@yahoo.com';
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