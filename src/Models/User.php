<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use UserAuth\Exceptions\InvalidUserCredentialsException;
use UserAuth\Exceptions\PasswordChangeException;
use UserAuth\Exceptions\UserCreationException;
use UserAuth\Libraries\Utils;


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
    protected $id;

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
        $this->hasMany('id', 'UserAuth\Models\UserPasswordChange', 'user_id', [
            'alias' => 'PasswordChanges',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);

        $this->hasMany('id', 'UserAuth\Models\UserPasswordReset', 'user_id', [
            'alias' => 'PasswordResets',
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
    }

    /**
     * Set updated at date and time before updating a user
     */
    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool|false $setActive
     * @return int the ID of the created user
     * @throws UserCreationException
     */
    public function createUser($email, $password, $setActive = false)
    {
        $this->email = $email;
        $this->password = $password;
        $this->created_at = date("Y-m-d H:i:s");
        $this->status = $setActive ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
        if (!$this->create()) {
            throw new UserCreationException($this->getMessages());
        }
        return $this->id;
    }

    /**
     * Generate random password
     *
     * @param int $length
     * @param bool|true $strict (if set to true, a symbol will be added to the password)
     * @return string
     */
    public static function generateRandomPassword($length = 8, $strict = true)
    {
        return Utils::generateRandomPassword($length, $strict);
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     * @throws InvalidUserCredentialsException
     */
    public function authenticate($email, $password)
    {
        $user = User::findFirst([
            "email = :email:",
            'bind' => ['email' => $email]
        ]);

        if ($user == false) {
            throw new InvalidUserCredentialsException(ErrorMessages::INVALID_AUTHENTICATION_DETAILS);
        }

        //validate password
        if (!Utils::verifyPassword($password, $user->password)) {
            throw new InvalidUserCredentialsException(ErrorMessages::INVALID_AUTHENTICATION_DETAILS);
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $previousPassword
     * @param string $newPassword
     * @param int $max the maximum number of changes before a password can be re-used
     * @return bool
     * @throws InvalidUserCredentialsException
     * @throws PasswordChangeException
     */
    public function changePassword($email, $previousPassword, $newPassword, $max = UserPasswordChange::MAX_PASSWORD_CHANGES_BEFORE_REUSE)
    {
        //first check that the user exists
        $user = User::findFirst([
            "email = :email:",
            'bind' => ['email' => $email]
        ]);

        if ($user == false) {
            throw new PasswordChangeException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }
        $this->id = $user->id;

        //validate password
        if (!Utils::verifyPassword($previousPassword, $user->password)) {
            throw new PasswordChangeException(ErrorMessages::OLD_PASSWORD_INVALID);
        }

        //check if new password matches user's current password
        if (Utils::verifyPassword($newPassword, $user->password)) {
            throw new PasswordChangeException("You cannot use any of your last {$max} passwords");
        }

        /*
         * check if the new password does not correspond to the previous max passwords
         * We use max-1 in the query because we are assuming that the user's current password is
         * inclusive of the last max passwords used and this has already been checked above
         */
        $recentPasswords = UserPasswordChange::query()
            ->where("user_id = :user_id:")
            ->bind(["user_id" => $this->id])
            ->orderBy("date_changed DESC")
            ->limit($max - 1)
            ->execute()
            ->toArray();

        foreach ($recentPasswords as $aRecentPassword) {
            if (Utils::verifyPassword($newPassword, $aRecentPassword['password_hash'])) {
                throw new PasswordChangeException("You cannot use any of your last {$max} passwords");
            }
        }

        //if all goes well, proceed to update the password
        return $this->updatePassword($previousPassword, $newPassword);
    }

    /**
     * @param string $previousPassword
     * @param string $newPassword
     * @return bool
     * @throws PasswordChangeException
     */
    public function updatePassword($previousPassword, $newPassword)
    {
        $transactionManager = new TransactionManager();
        try {
            //use a transaction as we would be updating more than one table
            $transaction = $transactionManager->get();
            $this->setTransaction($transaction);
            $user = User::findFirst($this->id);
            $user->password = Utils::encryptPassword($newPassword);
            if (!$user->save()) {
                $transaction->rollback(ErrorMessages::PASSWORD_UPDATE_FAILED);
            }

            $userPasswordChange = new UserPasswordChange();
            $userPasswordChange->setTransaction($transaction);
            $userPasswordChange->setDateChanged(date("Y-m-d H:i:s"));
            $userPasswordChange->setUserId($this->id);
            $userPasswordChange->setPasswordHash(Utils::encryptPassword($previousPassword));
            if (!$userPasswordChange->save()) {
                $transaction->rollback(ErrorMessages::PASSWORD_UPDATE_FAILED);
            }

            $transaction->commit();
            return true;
        } catch (TransactionFailed $e) {
            //return false;
            throw new PasswordChangeException($e->getMessage());
        }
    }


    public function generateResetPasswordToken($email, $tokenLength = UserPasswordReset::DEFAULT_TOKEN_LENGTH)
    {

    }

}