<?php

namespace UserAuth\Models;

/**
 * Class ErrorMessages
 * @package UserAuth\Models
 */
class ErrorMessages
{
    const INVALID_AUTHENTICATION_DETAILS = "The email/password combination provided is invalid";

    const EMAIL_DOES_NOT_EXIST = "The email provided does not exist in our records";

    const OLD_PASSWORD_INVALID = "The previous password supplied was invalid";

    const UNKNOWN_ERROR_OCCURRED = "Unknown error occurred";

    const PASSWORD_UPDATE_FAILED = "Password update failed!";

    const INACTIVE_ACCOUNT = "User account is inactive";

    const DISABLED_ACCOUNT = "User account is disabled";

    const INVALID_STATUS = "The status provided is invalid";

    const STATUS_UPDATE_FAILED = "Status update failed";

    const RESET_PASSWORD_TOKEN_TOO_LONG = "The reset password token provided exceeds %s characters";

    const RESET_PASSWORD_TOKEN_TOO_SHORT = "The reset password token provided is less than %s characters";

    const RESET_PASSWORD_FAILED = "Password reset failed";

    const INVALID_RESET_PASSWORD_TOKEN = "The token specified is invalid";

    const EXPIRED_RESET_PASSWORD_TOKEN = "The token specified has expired";

    const TOKEN_EXPIRY_FAILED = "The token could not be expired";
}