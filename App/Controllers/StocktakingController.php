<?php

namespace App\Controllers;

use App\Libs\Formater;
use App\models\Messages;
use App\models\Product;
use App\models\Stock;

class StocktakingController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    public function process($params)
    {
        //Check user permissions
        $this->checkSecurity();

        if (!isset($params[0])) {
            Messages::addMessage("Unathorized", "error");
            $this->redirect("stock");
        }

        //Set actual stock by params id
        $stockId = $params[0];
        $stockModel = new Stock();
        $productModel = new Product();
        $stock = $stockModel->getSingle($stockId);

        //Check if stock exists
        if (!$stock) {
            Messages::addMessage("Stock not found", "error");
            $this->redirect("stock");
        }

        //Set basic data to template
        $products = $stockModel->getProducts($stockId);
        $this->setData("items", $products);
        $this->setData("stockId", $stockId);

        //Check, if is set action param
        if (isset($params[1])
            && $params[1] == "edit") {
            //Set all products to template
            $allProducts = Formater::stockProductDiff($products, $productModel->getAll());
            $allProducts = array_map(function ($el) {
                return [$el["id"], $el["name"]];
            }, $allProducts);
            $this->setData("allProducts", $allProducts);

            //If is form send
            if (isset($_POST["submit"])) {
                unset($_POST["submit"]);
                //Set default response scheme
                $arg = ["stock_id" => $stockId, "product_id" => "", "quantity" => ""];

                foreach ($_POST as $key => $value) {
                    $arg["product_id"] = $key;
                    $arg["quantity"] = $value;
                    $stockModel->updateQuantity($arg);
                }
                //Proccess the stocktaking and redirect user to this stock
                Messages::addMessage("Inventura zapracovana", "success");
                $this->redirect("stocktaking/" . $stockId);
            }
            //Set title and template
            $this->setTitle("Stock taking edit");
            $this->setTemplate("stock/stockTakingEdit");
        } else {
            $this->setTitle("Stock taking");
            $this->setTemplate("stock/stockTaking");
        }
    }
}
