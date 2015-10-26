<?php
/**
 * Created by PhpStorm.
 * User: tegaoghenekohwo
 * Date: 26/10/15
 * Time: 02:00
 */

namespace UserAuth\Models;

use Phalcon\Mvc\Model;

class UserPasswordReset extends Model
{
    /**
     * Table for managing model
     * @return string
     */
    public function getSource()
    {
        return "user_password_resets";
    }

}