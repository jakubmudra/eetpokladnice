<?php

namespace App\Controllers;

use App\models\User;

class DashboardController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Check permissions
        $this->checkSecurity();

        //Set user class
        $user = new User();

        //Set logged data to template
        $this->setData("logged", "true");

        //Handle logout
        if (!empty($params[0]) && $params[0] == 'logout') {
            $user->logout();
            $this->redirect("login");
        }

        $this->setTitle("Dashboard");
        $this->setTemplate("dashboard");
    }
}
