<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;
use UserAuth\Exceptions\ResetPasswordException;
use UserAuth\Libraries\Utils;

/**
 * Class UserPasswordReset
 * @package UserAuth\Models
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class UserPasswordReset extends BaseModel
{
    /**
     * Primary key of the model
     * @var int
     */
    private $id;

    /**
     * User ID
     * @property
     * @var int
     */
    private $user_id;

    /**
     * Token
     * @var string
     */
    private $token;

    /**
     * Date the request to reset a password was made
     * @var string
     */
    private $date_requested;

    /**
     * The UNIX timestamp date at which the password will expire
     * @var int
     */
    private $date_of_expiry;

    /**
     * Value to check if a token should expire or not
     * @property
     * @var bool
     */
    private $expires;

    /**
     * The default length of a reset password token
     */
    const DEFAULT_TOKEN_LENGTH = 50;

    /**
     * The maximum length of a reset password token
     */
    const MAX_TOKEN_LENGTH = 200;

    /**
     * The minimum length of a reset password token
     */
    const MIN_TOKEN_LENGTH = 20;

    /**
     * The default token expiry time of a reset password token
     */
    const DEFAULT_TOKEN_EXPIRY_TIME = 259200; //3 * 24 * 3600

    /**
     * Set the object's ID
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the object's ID
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the object's User ID
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Get the object's User ID
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the token for this object
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Get the token for this object
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the date the password request was made for this object
     * @param string $date_requested
     */
    public function setDateRequested($date_requested)
    {
        $this->date_requested = $date_requested;
    }

    /**
     * Get the date the password request was made for this object
     * @return string
     */
    public function getDateRequested()
    {
        return $this->date_requested;
    }

    /**
     * Set the expiry timestamp for the token created when this reset password request was made
     * @param int $date_of_expiry
     */
    public function setDateOfExpiry($date_of_expiry)
    {
        $this->date_of_expiry = $date_of_expiry;
    }

    /**
     * Get the UNIX timestamp for which the token created when this reset password request was made will expire
     * @return int
     */
    public function getDateOfExpiry()
    {
        return $this->date_of_expiry;
    }

    /**
     * Set whether or not a token should expired
     * @param $expires
     */
    public function setExpires($expires)
    {
        $this->expires  = $expires;
    }

    /**
     * Get whether or not a token will expire
     * @return bool
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Table for managing model
     * @return string
     */
    public function getSource()
    {
        return "user_password_resets";
    }

    /**
     * @param int $user_id
     * @param int $tokenLength
     * @param int $expires
     * @param boolean $expiry
     * @return string
     * @throws ResetPasswordException
     */
    public function generateToken($user_id, $tokenLength, $expires, $expiry)
    {
        if ($tokenLength > self::MAX_TOKEN_LENGTH) {
            throw new ResetPasswordException(sprintf(ErrorMessages::RESET_PASSWORD_TOKEN_TOO_LONG, UserPasswordReset::MAX_TOKEN_LENGTH));
        }

        if ($tokenLength < self::MIN_TOKEN_LENGTH) {
            throw new ResetPasswordException(sprintf(ErrorMessages::RESET_PASSWORD_TOKEN_TOO_SHORT, UserPasswordReset::MIN_TOKEN_LENGTH));
        }

        $tokenLength = $tokenLength - 10; //append a timestamp
        $token = Utils::generateRandomString($tokenLength, false);
        if ($this->tokenExists($token)) {
            return $this->generateToken($user_id, $tokenLength, $expires, $expiry);
        }
        $token = $token . time();
        $this->setUserId($user_id);
        $this->setExpires((int) $expires);
        $this->setDateOfExpiry($expires ? time() + $expiry : null);
        $this->setToken($token);
        $this->setDateRequested(date("Y-m-d H:i:s"));

        if (!$this->create()) {
            throw new ResetPasswordException(ErrorMessages::RESET_PASSWORD_FAILED);
        }

        return $token;
    }

    /**
     * Get reset data associated with a token
     * @param $token
     * @return \UserAuth\Models\UserPasswordReset
     */
    public function getTokenData($token)
    {
        return $this->findFirst([
            "token = :token:",
            'bind' => ['token' => $token]
        ]);
    }

    /**
     * Check if a token already exists
     * @param string $token
     * @return bool
     */
    private function tokenExists($token)
    {
        $tokenData = $this->getTokenData($token);
        if ($tokenData == false) {
            return false;
        }

        return $tokenData == false ? true : false;
    }

    /**
     * Expire a token
     * @param string $token
     * @return bool
     */
    public function expireToken($token)
    {
        $tokenData = $this->getTokenData($token);
        if ($tokenData == false) {
            return false;
        }

        $tokenData->date_of_expiry = time() - 1;
        return $tokenData->save();
    }

}