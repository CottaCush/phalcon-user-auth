<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;
use UserAuth\Models\UserLoginHistory;

/**
 * Class LoginHistoryTest
 * @package Tests
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class LoginHistoryTest extends \UnitTestCase
{

    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();

        $this->createUsers();

        parent::setUp(Di::getDefault());
    }

    public function testLoginHistory()
    {
        //perform six login attempts, three successes and two failures, and one with an invalid email
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;

        $this->login();
        $this->login();
        $this->login();

        $this->password = 'incorrect';
        $this->loginAndCatchAuthenticationException();
        $this->loginAndCatchAuthenticationException();

        //this should not be logged as email is invalid
        $this->email = 'wrong_email';
        $this->loginAndCatchAuthenticationException();


        //number of history logged for this user should be five
        $results = UserLoginHistory::query()
            ->where("user_id = :user_id:")
            ->bind(["user_id" => $this->user_id])
            ->execute()
            ->toArray();

        $this->assertEquals(5, count($results));

        //verify that the keys in the returned data are valid and complete
        $requiredKeys = ['id', 'user_id', 'ip_address', 'user_agent', 'date_logged', 'login_status'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $results[0]);
        }
    }
}