<?php

use App\models\Db;

error_reporting(E_ALL);
ini_set('display_errors', 'On');
session_start();
//Set internal encoding
mb_internal_encoding("UTF-8");

//Load autoload
require_once("App/Autoloader.php");
require_once("vendor/autoload.php");

Db::connect();

$router = new App\Controllers\RouterController();
$router->process($_SERVER['REQUEST_URI']);

//Render view
$router->renderView();
