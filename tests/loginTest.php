<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Libraries\Utils;
use UserAuth\Models\UserLoginHistory;
use UserAuth\Models\UserPasswordChange;
use UserAuth\Models\UserPasswordReset;
use UserAuth\Models\UserType;

/**
 * Test Class for User Login
 * Class loginTest
 * @package Tests
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class LoginTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();

        $this->createUsers();

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
        $this->assertNotEmpty($response, "Test Login Assertion: Valid email and valid password");

        $relationShips = [
            'PasswordChanges',
            'PasswordResets',
            'LoginHistory',
            'UserType'
        ];

        //check that all of the following relationships are valid
        foreach ($relationShips as $aRelationShip) {
            $response->{$aRelationShip};
        }

        $requiredAttributes = ['id', 'email', 'password', 'status', 'created_at', 'updated_at', 'user_type_id'];
        $validate = Utils::validateObjectHasAllProperties($requiredAttributes, $response);
        $this->assertTrue($validate);
    }
}