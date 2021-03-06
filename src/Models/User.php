<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;
use Phalcon\Mvc\Model\Transaction\Failed as TransactionFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Uniqueness;
use UserAuth\Exceptions\StatusChangeException;
use UserAuth\Exceptions\UserAuthenticationException;
use UserAuth\Exceptions\PasswordChangeException;
use UserAuth\Exceptions\ResetPasswordException;
use UserAuth\Exceptions\UserCreationException;
use UserAuth\Libraries\Utils;

/**
 * Class User
 * @property string password
 * @property string updated_at
 * @property string created_at
 * @property int id
 * @property string email
 * @property int status
 * @property int/null user_type_id
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package UserAuth\models
 */
class User extends BaseModel
{
    /**
     * Constant to show that a user's account is not yet active (status after registration)
     */
    const STATUS_INACTIVE = 0;

    /**
     * Constant to show that a user's account is active
     */
    const STATUS_ACTIVE = 1;

    /**
     * Constant to show that a user's account has been suspended/disabled
     * This is a status that is triggered by an administrator
     */
    const STATUS_DISABLED = 2;

    static $statusMap = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DISABLED => 'Disabled'
    ];

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
        $validator = new Validation();
        
        $validator->add('email', new Email([
            'message' => 'Invalid email supplied'
        ]));

        $validator->add('email', new Uniqueness(array(
            'message' => 'Sorry, The email has been used by another user'
        )));

        return $this->validate($validator);
    }


    /**
     * Define links/relationship with other tables
     */
    public function initialize()
    {
        $this->hasMany('id', UserPasswordChange::class, 'user_id', [
            'alias' => 'PasswordChanges',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);

        $this->hasMany('id', UserPasswordReset::class, 'user_id', [
            'alias' => 'PasswordResets',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);

        $this->hasMany('id', UserLoginHistory::class, 'user_id', [
            'alias' => 'LoginHistory',
            'foreignKey' => [
                'action' => Relation::ACTION_CASCADE,
            ],
        ]);

        $this->belongsTo("user_type_id", UserType::class, "id", [
            "alias" => "UserType",
            "foreignKey" => [
                "allowNulls" => true,
                "message" => "The user type specified is invalid!"
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
     * @param int/null $userTypeId
     * @return int the ID of the created user
     * @throws UserCreationException
     */
    public function createUser($email, $password, $setActive = false, $userTypeId = null)
    {
        $this->email = $email;
        $this->password = $password;
        $this->created_at = date("Y-m-d H:i:s");
        $this->status = $setActive ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
        $this->user_type_id = $userTypeId;
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
     * @param array $options ..other options e.g. user_agent, ip_address.
     * @return $this
     * @throws UserAuthenticationException
     */
    public function authenticate($email, $password, $options = [])
    {
        /* @var $this */
        $user = User::findFirst([
            "email = :email:",
            'bind' => ['email' => $email]
        ]);

        if ($user == false) {
            throw new UserAuthenticationException(ErrorMessages::INVALID_AUTHENTICATION_DETAILS);
        }

        $options['user_id'] = $user->id;

        //validate password
        if (!Utils::verifyPassword($password, $user->password)) {
            //log status of login
            $options['login_status'] = UserLoginHistory::LOGIN_STATUS_FAILED;
            UserLoginHistory::getInstance()->addLog($options);
            throw new UserAuthenticationException(ErrorMessages::INVALID_AUTHENTICATION_DETAILS);
        }

        $this->validateStatus($user->status);

        //log status of login
        $options['login_status'] = UserLoginHistory::LOGIN_STATUS_SUCCESS;
        UserLoginHistory::getInstance()->addLog($options);

        return $user;
    }

    /**
     * @param string $email
     * @param string $previousPassword
     * @param string $newPassword
     * @param int $max the maximum number of changes before a password can be re-used
     * @return bool
     * @throws UserAuthenticationException
     * @throws PasswordChangeException
     */
    public function changePassword($email, $previousPassword, $newPassword, $max = UserPasswordChange::MAX_PASSWORD_CHANGES_BEFORE_REUSE)
    {
        $user = $this->getUserByEmail($email);
        if ($user == false) {
            throw new PasswordChangeException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }

        //validate password
        if (!Utils::verifyPassword($previousPassword, $user->password)) {
            throw new PasswordChangeException(ErrorMessages::OLD_PASSWORD_INVALID);
        }

        //check if new password matches user's current password
        if (Utils::verifyPassword($newPassword, $user->password)) {
            throw new PasswordChangeException("You cannot use any of your last {$max} passwords");
        }

        //check user's status
        $this->validateStatus($user->status);

        //Validate new password
        UserPasswordChange::validateNewPassword((int)$user->id, $newPassword, $max);

        //if all goes well, proceed to update the password
        return $this->updatePassword((int)$user->id, $newPassword);
    }

    /**
     * @param int $userId
     * @param string $newPassword
     * @param null $resetPasswordToken token to expire if call is from password reset
     * @return bool
     * @throws PasswordChangeException
     */
    public function updatePassword($userId, $newPassword, $resetPasswordToken = null)
    {
        $transactionManager = new TransactionManager();
        try {
            //use a transaction as we would be updating more than one table
            $transaction = $transactionManager->get();
            $this->setTransaction($transaction);
            $user = User::findFirst($userId);
            if ($user == false) {
                $transaction->rollback(ErrorMessages::PASSWORD_UPDATE_FAILED);
            }
            $previousPassword = $user->password;
            $user->password = Utils::encryptPassword($newPassword);
            if (!$user->save()) {
                $transaction->rollback(ErrorMessages::PASSWORD_UPDATE_FAILED);
            }

            $userPasswordChange = new UserPasswordChange();
            $userPasswordChange->setTransaction($transaction);
            $userPasswordChange->setDateChanged(date("Y-m-d H:i:s"));
            $userPasswordChange->setUserId($userId);
            $userPasswordChange->setPasswordHash($previousPassword);
            if (!$userPasswordChange->save()) {
                $transaction->rollback(ErrorMessages::PASSWORD_UPDATE_FAILED);
            }

            if (!empty($resetPasswordToken) && !(new UserPasswordReset())->expireToken($resetPasswordToken)) {
                $transaction->rollback(ErrorMessages::TOKEN_EXPIRY_FAILED);
            }

            $transaction->commit();
            return true;
        } catch (TransactionFailed $e) {
            throw new PasswordChangeException($e->getMessage());
        }
    }

    /**
     * Set a user's account status to active
     * @param string $email
     * @return bool
     * @throws StatusChangeException
     */
    public function setActive($email)
    {
        return $this->changeUserStatus($email, self::STATUS_ACTIVE);
    }

    /**
     * Set a user's account status to inactive
     * @param string $email
     * @return bool
     * @throws StatusChangeException
     */
    public function setInactive($email)
    {
        return $this->changeUserStatus($email, self::STATUS_INACTIVE);
    }

    /**
     * Set a user's account status to disabled
     * @param string $email
     * @return bool
     * @throws StatusChangeException
     */
    public function disableUser($email)
    {
        return $this->changeUserStatus($email, self::STATUS_DISABLED);
    }

    /**
     * Change a user's status
     * @param string $email
     * @param int $newStatus
     * @return bool
     * @throws StatusChangeException
     */
    public function changeUserStatus($email, $newStatus)
    {
        /* @var \UserAuth\Models\User */
        $user = $this->getUserByEmail($email);
        if (empty($user)) {
            throw new StatusChangeException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }

        if (!array_key_exists($newStatus, self::$statusMap)) {
            throw new StatusChangeException(ErrorMessages::INVALID_STATUS);
        }

        if ($user->status == $newStatus) {
            throw new StatusChangeException("User status is already set to " . self::getStatusDescriptionFromCode($newStatus));
        }

        //all is fine
        $user->status = (int)$newStatus;
        if (!$user->save()) {
            throw new StatusChangeException(ErrorMessages::STATUS_UPDATE_FAILED);
        }

        return true;
    }

    /**
     * Get user details
     * @param string $email
     * @return \UserAuth\Models\User
     */

    public function getUserByEmail($email)
    {
        return User::findFirst([
            "email = :email:",
            'bind' => ['email' => $email]
        ]);
    }

    /**
     * Get a meaningful textual description of a user's status using the status code
     * @param $statusCode
     * @return string
     */
    public static function getStatusDescriptionFromCode($statusCode)
    {
        if (empty(self::$statusMap[$statusCode])) {
            return "Unknown";
        }

        return self::$statusMap[$statusCode];
    }

    /**
     * @param $email
     * @param null $tokenLength
     * @param bool|true $expires whether the token expires or not
     * @param null $expiry amount of time in seconds for the token to expire
     * @return string
     * @throws ResetPasswordException
     * @throws UserAuthenticationException
     */
    public function generateResetPasswordToken($email, $tokenLength = null, $expires = true, $expiry = null)
    {
        $user = $this->getUserByEmail($email);

        if (empty($user)) {
            throw new ResetPasswordException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }

        $this->validateStatus($user->status);

        $tokenLength = is_null($tokenLength) ? UserPasswordReset::DEFAULT_TOKEN_LENGTH : (int)$tokenLength;
        $expiry = is_null($expiry) ? UserPasswordReset::DEFAULT_TOKEN_EXPIRY_TIME : (int)$expiry;

        return (new UserPasswordReset())->generateToken($user->id, $tokenLength, $expires, $expiry);
    }

    /**
     * Validates a user's status before taking further actions e.g login, password change, password reset
     * @param string $status
     * @throws UserAuthenticationException
     */
    public function validateStatus($status)
    {
        if ($status == self::STATUS_INACTIVE) {
            throw new UserAuthenticationException(ErrorMessages::INACTIVE_ACCOUNT);
        }

        if ($status == self::STATUS_DISABLED) {
            throw new UserAuthenticationException(ErrorMessages::DISABLED_ACCOUNT);
        }
    }

    /**
     * Returns the User ID associated with a reset password token
     * @param $token
     * @return null|int
     */
    public function getUserIdByResetPasswordToken($token)
    {
        $tokenData = (new UserPasswordReset())->getTokenData($token);
        return $tokenData == false ? null : $tokenData->user_id;
    }

    /**
     * @param $email
     * @param $newPassword
     * @param $token
     * @return bool
     * @throws PasswordChangeException
     * @throws ResetPasswordException
     * @throws UserAuthenticationException
     */
    public function resetPassword($email, $newPassword, $token)
    {
        $user = $this->getUserByEmail($email);
        if ($user == false) {
            throw new ResetPasswordException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }

        $this->validateStatus($user->status);

        //check if token is valid
        /* @var \UserAuth\Models\UserPasswordReset */
        $tokenData = (new UserPasswordReset())->getTokenData($token);
        if (empty($tokenData)) {
            throw new ResetPasswordException(ErrorMessages::INVALID_RESET_PASSWORD_TOKEN);
        }

        $expires = (int)$tokenData->expires;
        if ($expires === 1 && time() > $tokenData->date_of_expiry) {
            throw new ResetPasswordException(ErrorMessages::EXPIRED_RESET_PASSWORD_TOKEN);
        }

        //proceed to update password
        if ($expires === 0) {
            return $this->updatePassword((int)$user->id, $newPassword);
        }

        return $this->updatePassword((int)$user->id, $newPassword, $token);
    }

    /**
     * @param $email
     * @param $page
     * @param $limit
     * @return \stdClass
     * @throws UserAuthenticationException
     */
    public function getLoginHistory($email, $page, $limit)
    {
        $user = $this->getUserByEmail($email);
        if ($user == false) {
            throw new UserAuthenticationException(ErrorMessages::EMAIL_DOES_NOT_EXIST);
        }

        return UserLoginHistory::getInstance()->setUserId($user->id)->fetchLoginHistory($page, $limit);
    }

}