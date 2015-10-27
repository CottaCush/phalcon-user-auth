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
     * @param string $previousPassword
     * @param string $newPassword
     * @param int $max the maximum number of changes before a password can be re-used
     * @return bool
     */
    public static function changePassword($previousPassword, $newPassword, $max = self::MAX_PASSWORD_CHANGES_BEFORE_REUSE)
    {
        return true;
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
     * Get the date password change was made
     * @return string
     */
    public function getDateChanged()
    {
        return $this->date_changed;
    }

    /**
     * Get password hash
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password_hash;
    }

}