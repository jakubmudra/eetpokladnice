<?php

namespace App\Controllers;

use App\Config\Config;
use http\Url;

class RouterController extends Controller
{
    //Controller variable - stores actual controller
    protected $controller;

    /**
     * Router processing method. Sets default attr. to params
     *
     * @param $params REQUEST_URI from SERVER var.
     */
    public function process($params)
    {
        //Parse url to controll classname and filenamme
        $parsedURL = $this->parseURL($params);
        $className = $this->parseClassName(array_shift($parsedURL));
        $controllerClass = Config::$controllerNamespace . ucfirst($className) . "Controller";
        $filename = str_replace("\\", '/', $controllerClass) . ".php";

        //Handle controller
        if (get_parent_class($this) === $controllerClass) {
            $this->controller = new LoginController();
        } elseif (file_exists($filename)) {
            $this->controller = new $controllerClass();
        } else {
            $this->redirect('error');
        }

        //Proccess controller
        $this->controller->process($parsedURL);

        //Set template defaults
        $this->setData("title", $this->controller->getHeader("title"));
        $this->setData("desc", $this->controller->getHeader("description"));

        $this->setTemplate($className == "api" ? "api" : "baseLayout");
    }


    /**
     * Parse URL to params
     * @param $url
     * @return array
     */
    private function parseURL($url)
    {
        $parsedURL = parse_url($url);
        $parsedURL["path"] = ltrim($parsedURL["path"], "/");
        $parsedURL["path"] = trim($parsedURL["path"]);

        return explode("/", $parsedURL["path"]);
    }

    /**
     *  Return parsed class name
     * @param $param
     * @return mixed
     */
    private function parseClassName($param)
    {
        $string = str_replace(' ', '', ucwords(str_replace('-', ' ', $param)));
        return $param;
    }
}
