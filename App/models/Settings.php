<?php

namespace App\models;

class Settings
{
    /**
     * Get all settings
     * @return mixed
     */
    public function getAll()
    {
        return Db::allRows("select name,value from settings");
    }

    /**
     * Save settings
     * @param $name
     * @param $value
     */
    public function saveSetting($name, $value)
    {
        $result = Db::oneRow("select id from settings where name = ?", [$name]);

        $setting = ["name" => $name, "value" => $value];

        if (!$result) {
            Db::insert('settings', $setting);
        } else {
            Db::update('settings', $setting, 'WHERE id = ?', [$result["id"]]);
        }
    }
}