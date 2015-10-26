<?php

namespace Tests;

use UserAuth\Models\User;

class RegisterTest extends \UnitTestCase
{

    private $email;
    private $password;

    /**
     *
     */
    public function testRegistration()
    {
        //create user without email and password. This should return false
        $this->email = "";
        $this->password = "";
        $response = $this->register();
        $this->assertFalse($response, "This has to be false");

        //set invalid email, and set a password
        $this->email = "abc@y";
        $response = $this->register();
        $this->assertFalse($response, "This has to be false");


        //set a valid email, and a password
        $this->email = 'test123@yahoo.com';
        $this->password = 'test';
        $response = $this->register();
        $this->assertInternalType("int", $response, 'Error asserting that an integer ID is returned after user creation');
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