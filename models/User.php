<?php

namespace UserAuth\Models;

use Phalcon\Exception as Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use UserAuth\Lib\Utils;


/**
 * Class User
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package UserAuth\models
 */
class User extends Model
{
    /**
     * Constant to show that a user's account is not yet active
     */
    const STATUS_INACTIVE = 0;

    /**
     * Constant to show that a user's account is active
     */
    const STATUS_ACTIVE = 1;

    /**
     * Constant to show that a user's account has been suspended/disabled
     */
    const STATUS_SUSPENDED = 2;


    static $statusMap = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_SUSPENDED => 'Suspended'
    ];

    /**
     * @var integer
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $created_at;

    /**
     * @var string
     */
    protected $updated_at;

    /**
     * @var integer
     */
    protected $status;


    /**
     * Returns the name of the table that holds the user's information
     * @return string
     */
    public function getSource()
    {
        return "user_credentials";
    }

    /**
     * Validate user's email
     * @return bool
     */
    public function validation()
    {
        $this->validate(new EmailValidator([
            'field' => 'email',
            'message' => 'Invalid email supplied'
        ]));

        $this->validate(new UniquenessValidator(array(
            'field' => 'email',
            'message' => 'Sorry, The email has been used by another user'
        )));


        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }


    /**
     * Define links/relationship with other tables
     */
    public function initialize()
    {
        $this->hasMany('user_cred_id', 'UserAuth\models\UserPasswordChange', 'user_id', [
            'alias' => 'password-changes',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);

        $this->hasMany('user_cred_id', 'UserAuth\models\UserPasswordReset', 'user_id', [
            'alias' => 'reset-password',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);
    }


    /**
     * Encrypt user password before creating user
     */
    public function beforeCreate()
    {
        $this->password = Utils::encryptPassword($this->password);
        $this->created_at = date("Y-m-d H:i:s");
    }

    /**
     * Set updated at date and time before updating a user
     */
    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    /**
     * Function to create a user's login credentials
     * @param $email
     * @param $password
     * @param bool|false $setActive
     * @return bool
     */
    public function createUser($email, $password, $setActive = false)
    {
        try {
            return $this->create([
                'email' => $email,
                'password' => $password,
                'active' => $setActive ? self::STATUS_ACTIVE : self::STATUS_INACTIVE
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Generate random password
     *
     * @param int $length
     * @param bool|false $strict (if set to , a symbol will be added to the password)
     * @return string
     */
    public function generateRandomPassword($length = 8, $strict = false)
    {
        return Utils::generateRandomPassword($length, $strict);
    }

}