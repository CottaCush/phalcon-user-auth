<?php

namespace UserAuth\Lib;

use \Phalcon\Security as Security;
use \Phalcon\Text as Text;

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
        $security = new Security();
        return $security->hash($rawPassword);
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
        $security = new Security();
        return $security->checkHash($rawPassword, $dbHash);
    }


    /**
     * Function to generate a random password
     * @param int $length
     * @param bool|true $strict
     * @return string
     */
    public static function generateRandomPassword($length = 8, $strict = true)
    {
        $password = Text::random(Text::RANDOM_ALNUM, $length);

        if (!$strict) {
            return $password;
        }

        //todo may add more symbols later
        $shuffledSymbols = str_shuffle("@#$%^&*!");

        return substr($password, 0, strlen($password) - 1) . $shuffledSymbols[0];
    }
}