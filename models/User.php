<?php

namespace UserAuth\Models;

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
     * @var
     */
    protected $updated_at;


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
        $this->hasMany('id', 'UserAuth\models\UserPasswordChange', 'user_id', array(
            'alias' => 'password-changes',
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
        $this->hasMany('id', 'UserAuth\models\UserPasswordReset', 'user_id', array(
            'alias' => 'reset-password',
            'foreignKey' => array(
                'action' => Relation::ACTION_CASCADE,
            ),
        ));
    }


    /**
     * Encrypt user password before creating user
     */
    public function beforeCreate()
    {
        $this->password = Utils::encryptPassword($this->password);
    }

    /**
     * Set updated at date and time before updating a user
     */
    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    //todo declare setters and getters for all fields

    /**
     * Setter for email
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get a user's email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

}