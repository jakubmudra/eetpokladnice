<?php

namespace App\Controllers;

use App\models\Messages;
use App\models\Settings;

class SettingsController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Set settings model
        $settingsModel = new Settings();

        //Handle post save
        if (isset($_POST)) {
            foreach ($_POST as $key => $value) {
                $settingsModel->saveSetting($key, $value);
            }
            Messages::addMessage("Uspesna uprava");
        }

        //Handle view settings page
        $settings = $settingsModel->getAll();
        $data = [];

        foreach ($settings as $item) {
            $data[$item["name"]] = $item["value"];
        }

        $this->setData("settings", $data);
        $this->setTemplate("settings/layout");
    }
}