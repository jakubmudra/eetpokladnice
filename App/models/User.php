<?php

namespace App\models;

use App\Libs\Password;

class User
{
    /**
     * Auhtenticate user
     * @return bool|mixed
     */
    public function authenticate()
    {
        return ($_SESSION['user']) ?? false;
    }

    /**
     * Login user
     * @param $code
     * @param $password
     * @return bool
     */
    public function login($code, $password)
    {
        $user = Db::oneRow("SELECT * FROM users WHERE code = ?", array($code));

        if (!$user || !Password::verify($password, $user["pass_code"])) {
            Messages::addMessage("Bad credentials", "error");
            return false;
        }

        Messages::addMessage("Succesfully logged", "success");
        $_SESSION['user'] = $user;

        return true;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        unset($_SESSION['user']);
    }

    /**
     * get user
     * @return mixed|null
     */
    public function returnUser()
    {
        return $_SESSION['user'] ?? null;
    }


}
