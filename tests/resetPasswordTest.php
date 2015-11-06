<?php
/**
 * Created by PhpStorm.
 * User: tegaoghenekohwo
 * Date: 06/11/15
 * Time: 23:18
 */

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;
use \Phalcon\Exception;

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
        //test 1 check if generating token for an inactive user account will throw an exception
        $this->resetPasswordException();

        $this->assertEquals(1, 1);
    }

    /**
     * This method must throw and exception when called for the test to Pass
     */
    public function resetPasswordException()
    {
        $user = new User();
        try {
            $user->generateResetPasswordToken($this->valid_test_email_2);
            $this->fail("Token generated successfully even when the account is an inactive one");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
        }
    }
}