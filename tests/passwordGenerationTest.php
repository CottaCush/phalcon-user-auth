<?php

namespace Tests;

use Phalcon\DI;
use UserAuth\Models\User;

/**
 * Test Class for User Registration
 * Class PasswordGenerationTest
 * @package Tests
 */
class PasswordGenerationTest extends \UnitTestCase
{
    /**
     * Test if method can generate 1000 unique passwords at a go
     */
    public function testPasswordGen()
    {
        $passwordsArray = [];
        $numOfPasswordsToGenerate = 1000;

        while ($numOfPasswordsToGenerate > 0) {
            $passwordsArray[] = User::generateRandomPassword();
            $numOfPasswordsToGenerate--;
        }
        $this->assertEquals($passwordsArray, array_unique($passwordsArray));
    }

    /**
     * Test that passwords must contain at least number
     */
    public function testPasswordContainsNumber()
    {
        for ($i=0; $i<1000; $i++){
            $password = User::generateRandomPassword();
            $this->assertTrue(preg_match('/\\d/', $password) > 0);
        }
    }
}