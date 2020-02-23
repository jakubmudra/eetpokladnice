<?php

namespace App\Controllers;

use App\models\Db;
use App\models\EET;
use App\models\Transanction;

class ApiController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Set headers
        header("Content-Type: application/json;charset=utf-8");

        //Set json serialize precision
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('serialize_precision', -1);
        }

        //Sert transaction model
        $transtactionModel = new Transanction();
        //Detect action
        $action = isset($params[0]) ? $params[0] : null;
        //Handle save transaction
        if ($action == "saveTransaction") {
            $transactionID = $_REQUEST["trans_id"];
            $products = $_REQUEST["products"];
            $quantity = $_REQUEST["quantity"];
            $total = 0.00;

            for ($i = 0; $i < count($products); $i++) {
                $transtactionModel->saveProductToTransaction($products[$i]['id'], $transactionID, $quantity[$i]);
                $total = $total + $products[$i]["price"] * $quantity[$i];
            }

            $transtactionModel->update(["total" => $total], $transactionID);

            echo json_encode($_REQUEST);
        //Handle cancel transaction
        } elseif ($action == "cancelTransaction") {
            if ($_REQUEST) {
                $transtactionModel->remove($_REQUEST["trans_id"]);
                echo json_encode(["done" => true, "trans_id"]);
            }
        //Handle get products of transaciton
        } elseif ($action == "getTransactionProducts") {
            if ($_REQUEST) {
                $response = $transtactionModel->getTransactionProducts($_REQUEST["trans_id"]);
                $quanties = [];
                foreach ($response as $key => $item) {
                    $quanties[$key] = $item['quantity'];
                    unset($response[$key]['quantity']);
                }
                echo json_encode(["id" => $_REQUEST["trans_id"], "products" => $response, "quantities" => $quanties]);
            }
        // Handle EET sending
        } elseif ($action == "sendTransaction") {
            if ($_REQUEST) {
                //Prepare eet from request data
                $response = EET::prepare($_REQUEST);
                $transtactionModel->addPaymentMethod($_REQUEST["methods"], $_REQUEST["trans_id"]);

                if ($response["state"] != "error") {
                    $transtactionModel->update(["completed" => 1], $_REQUEST['trans_id']);
                }

                //Insert EET state to db
                Db::insert(
                    "eet_transactions",
                    [
                        "transaction_id" => $_REQUEST["trans_id"],
                        "fik" => $response["FIK"],
                        "bkp" => $response["BKP"],
                        "pkp" => $response["PKP"],
                        "state" => $response["state"],
                        "attempt" => 1
                    ]
                );

                echo json_encode($response);
            }
        // Todo() implement discount authorizing
        } elseif ($action == "authorizeDiscount") {
            if ($_REQUEST) {
                echo json_encode($_REQUEST);
            }
        }
        die();
    }

}
