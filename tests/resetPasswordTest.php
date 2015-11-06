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
        echo $this->user_id . PHP_EOL;
        $this->assertEquals(1, 1);
    }

    /**
     * This method must throw and exception when called for the test to Pass
     */
    public function resetPasswordException()
    {
        $user = new User();
        try {
            $user->changePassword($this->valid_test_email_2, $this->valid_test_password, User::generateRandomPassword());
            $this->fail("Password changed successfully even when it is an inactive account");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
        }
    }
}