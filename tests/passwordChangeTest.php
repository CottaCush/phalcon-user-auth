<?php

namespace Tests;

use Phalcon\DI;
use Phalcon\Exception;
use UserAuth\Models\User;
use UserAuth\Models\UserPasswordChange;

/**
 * Test Class for password changes
 * Class PasswordChangeTest
 * @package Tests
 */
class PasswordChangeTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();

        $this->createUsers();

        parent::setUp(Di::getDefault());
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

        //use a wrong password
        try {
            $user->changePassword($this->valid_test_email, 'incorrect', User::generateRandomPassword());
            $this->fail("Password changed successfully even when previous password was wrong");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\PasswordChangeException', $e);
        }

        $i = 1;
        while ($numPasswordChangesBeforeRepeat > 1) {
            $newPassword = User::generateRandomPassword();
            $i++;
            $response = $user->changePassword($this->valid_test_email, $previousPassword, $newPassword);
            //just to ensure seconds value for timestamp is different
            sleep(1);
            //check that password change was successful
            $this->assertTrue($response);

            //attempt to login with new password
            $response = $user->authenticate($this->valid_test_email, $newPassword);
            $this->assertTrue($response);

            //Set new password to previous password
            $previousPassword = $newPassword;
            $passwordsHistory[]  = $previousPassword;
            $numPasswordChangesBeforeRepeat--;
        }

        //at this point password change should fail if we use any of the last $max passwords
        foreach ($passwordsHistory as $newPassword) {
            try {
                $user->changePassword($this->valid_test_email, $previousPassword, $newPassword);
                $this->fail("Password changed successfully even when a previously used password {$newPassword} was used");
            } catch (Exception $e) {
                $this->assertInstanceOf('UserAuth\Exceptions\PasswordChangeException', $e);
            }
        }

        //Change password one more time , then use the first used password and all should go well
        $newPassword = User::generateRandomPassword();
        $response = $user->changePassword($this->valid_test_email, $previousPassword, $newPassword);
        $this->assertTrue($response);

        $response = $user->authenticate($this->valid_test_email, $newPassword);
        $this->assertTrue($response);

        $previousPassword = $newPassword;
        $newPassword = $passwordsHistory[0];
        $response = $user->changePassword($this->valid_test_email, $previousPassword, $newPassword);
        $this->assertTrue($response);

        $response = $user->authenticate($this->valid_test_email, $newPassword);
        $this->assertTrue($response);

        //finally , try to change the password of a user that has an inactive account
        try {
            $user->changePassword($this->valid_test_email_2, $this->valid_test_password, User::generateRandomPassword());
            $this->fail("Password changed successfully even when it is an inactive account");
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
        }
    }
}