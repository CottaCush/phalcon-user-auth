<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Libraries\Utils;
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
        $response = Utils::validateArrayHasAllKeys($requiredKeys, $results[0]);
        $this->assertTrue($response);
    }

    /**
     * @depends testLoginHistory
     */
    public function testLoginHistoryPagination()
    {
        //Perform 20 more login
        $this->email = $this->valid_test_email;
        $this->password = $this->valid_test_password;

        $i = 1;
        while ($i <= 20) {
            $this->login();
            $i++;
        }

        //Fetch paginated records (page 1)
        $loginHistory = (new User())->getLoginHistory($this->email, 1, 10);
        $properties = ['first', 'before', 'items', 'current', 'last', 'next', 'total_pages', 'total_items', 'limit'];
        $response = Utils::validateObjectHasAllProperties($properties, $loginHistory);
        $this->assertTrue($response);

        $this->assertEquals(10, count($loginHistory->items));
        $this->assertEquals(1, $loginHistory->current);
        $this->assertEquals(1, $loginHistory->before);
        $this->assertEquals(2, $loginHistory->next);

        //fetch next set of records (page 2)
        $loginHistory = (new User())->getLoginHistory($this->email, 2, 10);
        $this->assertEquals(10, count($loginHistory->items));
        $this->assertEquals(2, $loginHistory->current);
        $this->assertEquals(1, $loginHistory->before);

        //page 3 should be empty
        $loginHistory = (new User())->getLoginHistory($this->email, 3, 10);
        $this->assertEquals(0, count($loginHistory->items));
    }
}