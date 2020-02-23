<?php


namespace app\Config;


use App\models\Db;

class Config
{
    public static $templateFileExtension = ".twig";
    public static $controllerNamespace = "App\\Controllers\\";
    public static $templateDirectory = "App/Views/";
    public static $translantionFile = "App/Config/lang/";
    public static $language = 'cz';
    public static $maxPositions = ["x" => 5, "y" => 5];
    public static $EETCert = ["path" => "App/Config/eet/EET_CA1_Playground-CZ00000019.p12", "password" => "eet"];


    public static $database = [
        "host" => "",
        "user" => "",
        "password" => "",
        "database" => ""
    ];

    /**
     * Get settings from db
     * @param $string
     * @return mixed
     */
    public static function getSetting($string)
    {
        return Db::singleEntry("select value from settings where name = ?", [$string]);
    }
}
