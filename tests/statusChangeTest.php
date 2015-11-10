<?php

namespace Tests;
use Phalcon\DI;
use UserAuth\Models\User;

/**
 * Test class for user status changes
 * Class statusChangeTest
 * @package Tests
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class statusChangeTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();

        $this->createUsers();

        parent::setUp(Di::getDefault());
    }

    public function testStatusChange()
    {
        $user = new User();
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_ACTIVE, $userInfo['status']);

        //test 1 change active to active. this should throw exception
        $this->setStatusAndCatchException($userInfo['status'], User::STATUS_ACTIVE);

        //test 2 change active to inactive. this should work
        $response = $user->setInactive($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_INACTIVE, $userInfo['status']);

        //test 3 change inactive to inactive. this should throw exception
        $this->setStatusAndCatchException($userInfo['status'], User::STATUS_INACTIVE);


        //test 4 change inactive to active. this should work
        $response = $user->setActive($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_ACTIVE, $userInfo['status']);

        //test 5 change active to disabled. this should work
        $response = $user->disableUser($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_DISABLED, $userInfo['status']);


        //test 6 change disabled to disabled. this should throw an exception
        $this->setStatusAndCatchException($userInfo['status'], User::STATUS_DISABLED);

        //test 7 change disabled to inactive. this should work
        $response = $user->setInactive($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_INACTIVE, $userInfo['status']);

        //test 8 change inactive to disabled. this should work
        $response = $user->disableUser($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_DISABLED, $userInfo['status']);

        //test 9, finally change disabled to active. this should work
        $response = $user->setActive($this->valid_test_email);
        $this->assertTrue($response);
        //check user's info
        $userInfo = $this->getUser($this->valid_test_email);
        $this->assertEquals(User::STATUS_ACTIVE, $userInfo['status']);

    }

    /**
     * Change user's status and expect it to throw an exception
     * @param int $currentStatus user's current status
     * @param string $newStatus status to change to
     */
    public function setStatusAndCatchException($currentStatus, $newStatus)
    {
        $user = new User();
        try {
            switch ($newStatus) {
                case User::STATUS_INACTIVE:
                    $user->setInactive($this->valid_test_email);
                    break;
                case User::STATUS_ACTIVE:
                    $user->setActive($this->valid_test_email);
                    break;
                case User::STATUS_DISABLED:
                    $user->disableUser($this->valid_test_email);
                    break;
                default:
                    break;
            }
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown when an account with status " .User::getStatusDescriptionFromCode($currentStatus).
                " was changed to "  .User::getStatusDescriptionFromCode($newStatus));
        } catch (\Phalcon\Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\StatusChangeException', $e);
        }
    }

    /**
     * Get user details
     * @param $email
     * @return array
     */
    public function getUser($email)
    {
        $user = new User();
        $userInfo = $user->getUserByEmail($email);
        return $userInfo->toArray();
    }
}