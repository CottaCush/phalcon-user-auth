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
    public static function verifyPassword($rawPassword, $dbHash)
    {
        //todo test this with many randomly generated passwords for vulnerabilities.
        return password_verify($rawPassword, $dbHash);
    }


    /**
     * Function to generate a random password
     * @param int $length
     * @param bool|true $strict
     * @return string
     */
    public static function generateRandomPassword($length = 8, $strict = true)
    {
        $passwordArray = [];

        $randomString = str_shuffle("abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ1234567890");

        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, strlen($randomString) - 1);
            $passwordArray[] = $randomString[$n];
        }

        $password = implode($passwordArray);

        if (!$strict) {
            return $password;
        }

        //todo may add more symbols later
        $shuffledSymbols = str_shuffle("@#$%^&*!");

        return substr($password, 0, strlen($password) - 1) . $shuffledSymbols[0];
    }
}