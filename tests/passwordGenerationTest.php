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
}