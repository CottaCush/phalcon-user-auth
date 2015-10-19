<?php

namespace UserAuth\Lib;

/**
 * Class Utils
 * @author Tega Oghenekohwo <tega@cottacush.com>
 * @package UserAuth\Lib
 */
class Utils
{

    /**
     * Performs one-way encryption of a user's password using PHP's bcrypt
     *
     * @param string $rawPassword the password to be encrypted
     * @return bool|string
     */
    public static function encryptPassword($rawPassword)
    {
        $options = [
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];

        return  password_hash($rawPassword, PASSWORD_BCRYPT, $options);
    }


    /**
     * Verify that password entered will match the hashed password
     *
     * @param string $rawPassword the user's raw password
     * @param string $dbHash the hashed password that was saved
     * @return bool
     */
    public function verifyPassword($rawPassword, $dbHash)
    {
        //todo test this with many randomly generated passwords for vulnerabilities.
        return password_verify($rawPassword, $dbHash);
    }
}