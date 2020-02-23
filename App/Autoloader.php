<?php

namespace app;

class Autoloader
{
    /**
     * Base Autoloader function
     *
     * @param $className
     * @return bool
     */
    static public function loader($className)
    {
        $filename = str_replace("\\", '/', $className) . ".php";
        $filename[count(str_split($filename)) - 1] = strtolower($filename[count(str_split($filename)) - 1]);
        if (file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('app\Autoloader::loader');
