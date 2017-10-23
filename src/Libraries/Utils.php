<?php

namespace UserAuth\Libraries;

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
        return self::generateRandomString($length, $strict);
    }

    /**
     * @param int $length length of the string
     * @param bool|true $strict whether or not the string should contain a symbol
     * @return string
     */
    public static function generateRandomString($length, $strict = true)
    {
        if (!$strict) {
            return Text::random(Text::RANDOM_ALNUM, $length);
        }

        $password = self::generateRandomString($length - 1, false);

        //todo may add more symbols later
        $shuffledSymbols = str_shuffle("@#$%^&*!+-_~");

        return substr($password, 0, strlen($password) - 1) . $shuffledSymbols[0] . Text::random(Text::RANDOM_NUMERIC, 1);
    }

    /**
     * Get current date and time
     * @author Tega Oghenekohwo <tega@cottacush.com>
     * @return bool|string
     */
    public static function getCurrentDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @param array $keys
     * @param array $array
     * @return bool
     */
    public static function validateArrayHasAllKeys(array $keys, array $array)
    {
        foreach ($keys as $aKey) {
            if (!array_key_exists($aKey, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $properties
     * @param $object
     * @return bool
     */
    public static function validateObjectHasAllProperties(array $properties, $object)
    {
        foreach ($properties as $aProperty) {
            if (!property_exists($object, $aProperty)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $message
     * @return string
     */
    public static function getMessagesFromStringOrArray($message)
    {
        $messages = "";
        // check if the messages parameter passed is an array or a string
        if (is_array($message)) {
            foreach ($message as $m) {
                //double check to ensure that internal value is not an array
                if (!is_array($m)) {
                    $messages .= $m . ",";
                }
            }
        } else {
            $messages = $message;
        }

        return $messages;
    }
}