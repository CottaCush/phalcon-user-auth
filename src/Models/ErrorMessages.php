<?php
/**
 * Created by PhpStorm.
 * User: tegaoghenekohwo
 * Date: 30/10/15
 * Time: 22:14
 */

namespace UserAuth\Models;


class ErrorMessages
{
    const INVALID_AUTHENTICATION_DETAILS = "The email/password combination provided is invalid";

    const EMAIL_DOES_NOT_EXIST = "The email provided does not exist in our records";

    const OLD_PASSWORD_INVALID = "The previous password supplied was invalid";

    const UNKNOWN_ERROR_OCCURRED = "Unknown error occurred";

    const PASSWORD_UPDATE_FAILED = "Password Update Failed!";
}