<?php

namespace App\Controllers;

class ErrorController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    public function process($param)
    {
        //Handle error
        header("HTTP/1.0 404 Not Found");
        $this->setTitle("404");
        $this->setTemplate("error/404");
    }
}
