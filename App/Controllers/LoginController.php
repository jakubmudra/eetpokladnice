<?php

namespace App\Controllers;

use App\Libs\Password;
use App\models\Messages;
use App\models\User;

class LoginController extends Controller
{

    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Set default user class
        $user = new User();

        //If logged, redirect to dashboard
        if ($user->authenticate()) {
            $this->redirect("dashboard");
        }

        //Handle post event
        if ($_POST) {
            $state = $user->login($_POST['code'], $_POST['password']);
            if ($state) {
                $this->redirect("dashboard");
            }
        }

        $this->setTitle("login");
        $this->setTemplate("auth/login");
    }
}
