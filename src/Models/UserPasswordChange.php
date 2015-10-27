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
    /**
     * Table for managing model
     * @return string
     */
    public function getSource()
    {
        return "user_password_changes";
    }
}