<?php

use Phalcon\DI;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use UserAuth\Models\User;
use UserAuth\Models\UserType;

/**
 * This class serves as a base test case for the other test classes
 * Class UnitTestCase
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
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
     * @var string email to use for login
     */
    protected $email;

    /**
     * @var string password to use for login
     */
    protected $password;

    /**
     * @var null the user type id to use for user creation
     */
    protected  $user_type_id = null;

    /**
     * @var string sample user type name used for user creation
     */
    protected $sample_user_type_name = 'test';

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
        
        $this->setDI($di);

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
        //deleting records from user class deletes from the other tables except user type
        $classes = [User::class, UserType::class];

        foreach ($classes as $class) {
            $model = new $class();
            if ($model instanceof \UserAuth\Models\BaseModel) {
                $modelData = $model->find();
                foreach ($modelData as $row) {
                    $object = $model->findFirst($row->id);
                    if (!$object->delete()) {
                        echo "Sorry, object {$row->id} of class {$class} could not be deleted " . PHP_EOL;
                    }
                }
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
        //first create user types
        $userTypeTest = (new UserType())->createUserType($this->sample_user_type_name);
        if (empty($userTypeTest)) {
            die("Set up failed while creating user type");
        }

        //Create two new users, one account active and one account inactive
        $this->user_id = (new User())->createUser($this->valid_test_email, $this->valid_test_password, true, $userTypeTest);
        $this->user_id_2 = (new User())->createUser($this->valid_test_email_2, $this->valid_test_password, false);

        if (empty($this->user_id) || empty($this->user_id_2)) {
            die("Set up failed while creating users");
        }
    }


    /**
     * This method must throw and exception when called for the test to pass
     */
    public function loginAndCatchAuthenticationException()
    {
        try {
            $this->login();
            //if it executes this point, print a message to say that test has failed
            $this->fail("Exception was not thrown on email " . $this->email . " and password " . $this->password);
        } catch (Exception $e) {
            $this->assertInstanceOf('UserAuth\Exceptions\UserAuthenticationException', $e);
        }
    }

    /**
     * Authenticate a user
     * @return bool
     */
    public function login()
    {
        return (new User())->authenticate($this->email, $this->password);
    }
}