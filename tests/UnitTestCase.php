<?php

use Phalcon\DI;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use UserAuth\Models\User;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var string email used to create active user while running tests
     */
    protected $valid_test_email = 'tegap@mailinator.com';

    /**
     * @var string email used to create inactive user while running tests
     */
    protected $valid_test_email_2  = 'philippo@mailinator.com';

    /**
     * @var string password for creating both users
     */
    protected $valid_test_password = 'test';

    /**
     * @var integer variable to save the ID of the first created user
     */
    protected $user_id;

    /**
     * @var integer variable to save the ID of the first created user
     */
    protected $user_id_2;

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

    /**
     * Create two new users that will be used for tests
     * @throws \UserAuth\Exceptions\UserCreationException
     */
    public function createUsers()
    {
        //Create two new users, one account active and one account inactive
        $this->user_id = (new User())->createUser($this->valid_test_email, $this->valid_test_password, true);
        $this->user_id_2 = (new User())->createUser($this->valid_test_email_2, $this->valid_test_password, false);

        if (empty($this->user_id) || empty($this->user_id_2)) {
            die("Set up failed for Password Change Test");
        }
    }
}