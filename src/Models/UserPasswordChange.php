<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;

/**
 * Model class for managing user password changes
 * Class UserPasswordChange
 * @package UserAuth\Models
 */
class UserPasswordChange extends Model
{
    const MAX_PASSWORD_CHANGES_BEFORE_REUSE = 5;

    /**
     * Primary key for model
     * @var int
     */
    protected $id;

    /**
     * Foreign key that refers to the user's ID
     * @var int
     */
    protected $user_id;

    /**
     * @var string
     */
    protected $date_changed;

    /**
     * New password
     * @var string
     */
    protected $password_hash;

    /**
     * Table for managing model
     * @return string
     */
    public function getSource()
    {
        return "user_password_changes";
    }

    /**
     * Get ID
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get user's user ID
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set user ID
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get the date password change was made
     * @return string
     */
    public function getDateChanged()
    {
        return $this->date_changed;
    }

    /**
     * Set date changed
     * @param $dateChanged
     */
    public function setDateChanged($dateChanged)
    {
        $this->date_changed = $dateChanged;
    }

    /**
     * Get password hash
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

    /**
     * Set Password Hash
     * @param $passwordHash
     */
    public function setPasswordHash($passwordHash)
    {
        $this->password_hash = $passwordHash;
    }

}