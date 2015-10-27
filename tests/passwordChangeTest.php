<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;
use UserAuth\Models\UserPasswordChange;

/**
 * Test Class for password changes
 * Class PasswordChangeTest
 * @package Tests
 */
class PasswordChangeTest extends \UnitTestCase
{
    protected $user_id;

    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        //Create a new user
        $this->user_id = (new User())->createUser($this->valid_test_email, $this->valid_test_password);
        if (empty($this->user_id)) {
            die("Set up failed for Password Change Test");
        }
        parent::setUp(Di::getDefault());
    }


    /**
     * Delete all user's in the table
     */
    public function tearDown()
    {
        $this->clearTables();
    }

    /**
     * Change user's password the allowed maximum number of times
     * See if it allows the user to use a previous password
     * Also attempt to login with new password(s)
     */
    public function testPasswordChanges()
    {
        $passwordsHistory = [];
        //user already created from setUp's method

        $numPasswordChangesBeforeRepeat = UserPasswordChange::MAX_PASSWORD_CHANGES_BEFORE_REUSE;
        $previousPassword = $this->valid_test_password;
        $passwordsHistory[]  = $previousPassword;
        $user = new User();

        while ($numPasswordChangesBeforeRepeat > 0) {
            $newPassword = User::generateRandomPassword();
            $response = $user->changePassword($previousPassword, $newPassword);
            //check that password change was successful
            $this->assertTrue($response);

            //attempt to login with new password
            $response = $user->authenticate($this->valid_test_email, $newPassword);
            $this->assertTrue($response);

            //Set new password to previous password
            $previousPassword = $newPassword;
            $passwordsHistory[]  = $previousPassword;
        }

        //at this point password change should fail if we use any of the last $max passwords
        foreach ($passwordsHistory as $newPassword) {
            $response = $user->changePassword($previousPassword, $newPassword);
            $this->assertFalse($response);
        }

        //Change password one more time , then use the first used password and all should go well
        $newPassword = User::generateRandomPassword();
        $response = $user->changePassword($previousPassword, $newPassword);
        $this->assertTrue($response);

        $response = $user->authenticate($this->valid_test_email, $newPassword);
        $this->assertTrue($response);

        $previousPassword = $newPassword;
        $newPassword = $passwordsHistory[0];
        $response = $user->changePassword($previousPassword, $newPassword);
        $this->assertTrue($response);

        $response = $user->authenticate($this->valid_test_email, $newPassword);
        $this->assertTrue($response);
    }
}