<?php
/**
 * Created by PhpStorm.
 * User: tegaoghenekohwo
 * Date: 26/10/15
 * Time: 02:00
 */

namespace UserAuth\Models;

use Phalcon\Mvc\Model;
use UserAuth\Exceptions\ResetPasswordException;
use UserAuth\Exceptions\UserCreationException;
use UserAuth\Libraries\Utils;

class UserPasswordReset extends Model
{
    /**
     * Primary key of the model
     * @var int
     */
    private $id;

    /**
     * User ID
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
     * The default length of a reset password token
     */
    const DEFAULT_TOKEN_LENGTH = 50;

    /**
     * The maximum length of a reset password token
     */
    const MAX_TOKEN_LENGTH = 200;

    /**
     * The default token expiry time of a reset password token
     */
    const DEFAULT_TOKEN_EXPIRY_TIME = 3 * 24 * 3600;

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
     * Table for managing model
     * @return string
     */
    public function getSource()
    {
        return "user_password_resets";
    }

    /**
     * @param $user_id
     * @param int $tokenLength
     * @param int $expiry
     * @return string
     * @throws ResetPasswordException
     */
    public function generateToken($user_id, $tokenLength = self::DEFAULT_TOKEN_LENGTH, $expiry = self::DEFAULT_TOKEN_EXPIRY_TIME)
    {
        if (strlen($tokenLength) > self::MAX_TOKEN_LENGTH) {
            throw new ResetPasswordException(ErrorMessages::RESET_PASSWORD_TOKEN_TOO_LONG);
        }

        $token = Utils::generateRandomString($tokenLength, false);
        $this->setUserId($user_id);
        $this->setDateOfExpiry(time() + $expiry);
        $this->setToken($token);
        $this->setDateRequested(date("Y-m-d H:i:s"));

        if (!$this->create()) {
            throw new ResetPasswordException(ErrorMessages::RESET_PASSWORD_FAILED);
        }

        return $token;
    }

}