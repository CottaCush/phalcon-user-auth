<?php

namespace UserAuth\Models;

use Phalcon\Mvc\Model;
use UserAuth\Exceptions\PasswordChangeException;
use UserAuth\Libraries\Utils;

/**
 * Model class for managing user password changes
 * Class UserPasswordChange
 * @package UserAuth\Models
 */
class UserPasswordChange extends BaseModel
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

    /**
     * check if the new password does not correspond to the previous max passwords
     * We use max-1 in the query because we are assuming that the user's current password is
     * inclusive of the last max passwords used and this has already been checked above
     *
     * @param int $userId
     * @param string $newPassword
     * @param int $max
     * @throws PasswordChangeException
     */
    public static function validateNewPassword($userId, $newPassword, $max = self::MAX_PASSWORD_CHANGES_BEFORE_REUSE)
    {
        $recentPasswords = UserPasswordChange::query()
            ->where("user_id = :user_id:")
            ->bind(["user_id" => $userId])
            ->orderBy("date_changed DESC")
            ->limit($max - 1)
            ->execute()
            ->toArray();

        foreach ($recentPasswords as $aRecentPassword) {
            if (Utils::verifyPassword($newPassword, $aRecentPassword['password_hash'])) {
                throw new PasswordChangeException("You cannot use any of your last {$max} passwords");
            }
        }
    }

}