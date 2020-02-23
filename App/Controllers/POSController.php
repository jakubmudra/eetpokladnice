<?php

namespace App\Controllers;

/**
 * Class POSController
 * @package App\Controllers
 * @deprecated Use App\Controller\TransactionController
 */
class POSController extends Controller
{
    /**
     * @var TransactionController
     */
    private $controller;

    /**
     * Main controlller procces function
     * @param $params url params
     * @deprecated Use Transaction controller instead
      */
    function process($params)
    {
        $this->setTitle("cash-register");
        $this->setTemplate("pos/layout");
    }
}
