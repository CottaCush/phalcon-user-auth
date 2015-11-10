<?php

namespace Tests;

use Phalcon\DI;
use \Phalcon\Exception;
use UserAuth\Models\User;
use UserAuth\Models\UserPasswordReset;
use UserAuth\Libraries\Utils;
/**
 * Class ResetPasswordTest
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package Tests
 */
class ResetPasswordTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();

        $this->createUsers();

        parent::setUp(Di::getDefault());
    }

    public function testTokenGeneration()
    {
        $user = new User();
        //check if generating token for an inactive user account will throw an exception
        $this->throwAuthenticationException();

        //check if generating a token for a valid account will work
        $token = $user->generateResetPasswordToken($this->valid_test_email);
        $this->assertEquals(strlen($token), UserPasswordReset::DEFAULT_TOKEN_LENGTH);

        //check if token generated is actually in database
        $tokenData = (new UserPasswordReset())->getTokenData($token);
        $this->assertNotEmpty($tokenData);
        $this->assertEquals($this->user_id, $tokenData->user_id);
        $this->assertEquals(1, $tokenData->expires);

        //generate a new token for the valid account and set it to not expire
        $token = $user->generateResetPasswordToken($this->valid_test_email, null, false);
        $tokenData = (new UserPasswordReset())->getTokenData($token);
        $this->assertNotEmpty($tokenData);
        $this->assertEquals($this->user_id, $tokenData->user_id);
        $this->assertEquals(0, $tokenData->expires);
    }

    public function testResetPassword()
    {
        $user = new User();
        //generate a token that expires
        $token = $user->generateResetPasswordToken($this->valid_test_email);
        //throw an exception when an invalid token is used
        $wrongToken = Utils::generateRandomString(20);
        $this->throwResetPasswordException($wrongToken);

        //generate a token that expires using a negative timestamp so that the tokens expiry date is before the current time
        $expiredToken = $user->generateResetPasswordToken($this->valid_test_email, null, true, - (4 * 24 * 3600));
        $this->throwResetPasswordException($expiredToken);

        //reset the password with a valid token
        $newPassword = User::generateRandomPassword();
        $response = $user->resetPassword($this->valid_test_email, $newPassword, $token);
        $this->assertTrue($response);

        //authenticate with new password
        $response = $user->authenticate($this->valid_test_email, $newPassword);
        $this->assertTrue($response);

        //try to use the same token again even when it has expired
        $this->throwResetPasswordException($token);
    }

    /**
     * This method must throw and exception when called for the test to Pass
     */
    public function throwAuthenticationException()
    {
        $user = new User();
        try {
            $user->generateResetPasswordToken($this->valid_test_email_2);
            $this->fail("Token generated successfully even when the account is an inactive one");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
        }
    }

    /**
     * This method must throw and exception when called for the test to Pass
     * @param $token
     */
    public function throwResetPasswordException($token)
    {
        $user = new User();
        try {
            $user->resetPassword($this->valid_test_email, User::generateRandomPassword(), $token);
            $this->fail("Password reset successfully even when token is invalid or has expired");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\ResetPasswordException', $e);
        }
    }
}