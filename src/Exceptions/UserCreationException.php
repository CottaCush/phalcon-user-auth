<?php

namespace UserAuth\Exceptions;

use \Phalcon\Exception;


/**
 * Class UserCreationException
 * @package UserAuth\Exceptions
 * @author Tega Oghenekohwo <tega@cottacush.com>
 */
class UserCreationException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        // check if the messages parameter passed is an array or a string
        $messages = "";
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

        // make sure everything is assigned properly
        parent::__construct($messages, $code, $previous);
    }
}