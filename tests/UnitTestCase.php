<?php

use Phalcon\DI;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use UserAuth\Models\User;

abstract class UnitTestCase extends PhalconTestCase
{
    protected $valid_test_email = 'test123@yahoo.com';
    protected $valid_test_password = 'test';

    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var bool
     */
    private $_loaded = false;

    public function setUp(Phalcon\DiInterface $di = NULL, Phalcon\Config $config = NULL)
    {
        // Load any additional services that might be required during testing
        $di = DI::getDefault();

        // Get any DI components here. If you have a config, be sure to pass it to the parent

        parent::setUp($di);

        $this->_loaded = true;
    }

    /**
     * Check if the test case is setup properly
     *
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct()
    {
        if (!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp().');
        }
    }

    /**
     * Clear test table(s)
     */
    public function clearTables()
    {
        $users = User::find();
        $users = $users->toArray();
        foreach ($users as $user) {
            $userToDelete = User::findFirst($user['id']);
            if (!$userToDelete->delete()) {
                echo "Sorry, we can't delete the user {$user['id']} right now: \n";
            }
        }
    }

    /**
     * Override the parent's tearDown function to avoid it from resetting the DI
     * if a Test Class does not provide its own tearDown method
     */
    public function tearDown()
    {
    }
}