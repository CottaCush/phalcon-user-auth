<?php

namespace Tests;

use Phalcon\DI;
use Phalcon\Exception;
use UserAuth\Libraries\Utils;
use UserAuth\Models\UserType;
use UserAuth\Exceptions\UserTypeException;

/**
 * Test Class for User Type Creation / Retrieval
 * Class UserTypeTest
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package Tests
 */
class UserTypeTest extends \UnitTestCase
{
    public function setUp(\Phalcon\DiInterface $di = NULL, \Phalcon\Config $config = NULL)
    {
        $this->clearTables();
        parent::setUp(Di::getDefault());
    }

    /**
     * Test for user type/role creation
     */
    public function testUserTypeCreation()
    {
        //Create a user type without a name (exception expected)
        $this->exception('', UserTypeException::class);

        //create two user types
        $admin = $this->createUserType('admin');
        $this->assertNotEmpty($admin);

        $user = $this->createUserType('user');
        $this->assertNotEmpty($user);

        //create another user type with the same name (exception expected)
        $this->exception('user', UserTypeException::class);

        //fetch all user types
        $userTypes = (new UserType())->getUserTypes();
        $this->assertNotEmpty($userTypes);
        $this->assertEquals(2, count($userTypes->toArray()));

        $requiredProperties = ['id', 'name', 'created_at', 'updated_at'];

        foreach ($userTypes as $type) {
            $response = Utils::validateObjectHasAllProperties($requiredProperties, $type);
            $this->assertTrue($response);
        }
    }


    private function createUserType($name)
    {
        return  (new UserType())->createUserType($name);
    }

    private function exception($name, $exceptionClass)
    {
        try {
            $this->createUserType($name);
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown on registration for email " . $this->email . " and password " . $this->password);
        } catch (Exception $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }
    }
}