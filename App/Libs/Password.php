<?php

namespace App\Libs;

use App\models\Messages;

class Password
{
    public static $options = [
        "cost" => 10
    ];

    /**
     * Hash password
     * @param $password string password to be hashed
     * @return false|string|null
     */
    public static function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, self::$options);
    }

    /**
     * Verify hashed password
     * @param $password string passcode to be verified
     * @param $hash string passcode hash
     * @return bool
     */
    public static function verify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate random passcode
     * @return int Generated passcode
     */
    public static function generate()
    {
        return rand(1000, 9999);
    }
}
