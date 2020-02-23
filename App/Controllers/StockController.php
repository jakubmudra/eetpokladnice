<?php

namespace App\Controllers;

use App\models\Db;
use App\models\Messages;
use App\models\Product;
use App\models\Stock;

class StockController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Check user permissions
        $this->checkSecurity();

        //Prepare stock and product model
        $productModel = new Product();
        $stockModel = new Stock();

        //Set all products and stocks
        $products = $productModel->getAll();
        $stocks = $stockModel->getAll();

        //Handle stock deleting
        if (!empty($params[1]) && $params[0] == 'remove') {
            $stockModel->removeStock($params[1]);
            Messages::addMessage("Stock succesfully removed");
            $this->redirect("stock");
        }

        //Handle stock editing
        if (isset($params[0])
            && $params[0] == "edit") {
            //set default response structure
            $this->setData("stock", ["id" => "", "name" => "", "description" => "", "active" => ""]);
            $this->setData("products", $products);

            //Handle post
            if (isset($_POST)
                && !empty($_POST['name'])
                && !empty($_POST['description'])
                && !empty($_POST['active'])) {
                //create response
                $keys = ["name", "description", "active"];
                $stock = array_intersect_key($_POST, array_flip($keys));

                //Save stock
                $stockModel->saveStock($stock, $_POST['id']);
                Messages::addMessage("Stock was saved");

                //Get stock id
                $stockID = ($_POST['id'] == '') ? Db::getLastId() : $_POST['id'];
                //Get products
                $productsArray = $_POST['products'] ?? [];

                foreach ($productsArray as $product) {
                    $data[] = ['product_id' => $product, 'stock_id' => $stockID, 'quantity' => 0];
                }

                //update products in stock
                // TODO - rewrite this
                $oldData = $stockModel->getProducts($stockID);
                $stockModel->cleanStock($stockID);
                if (!empty($data)) {
                    foreach ($oldData as $oldProduct) {
                        foreach ($data as $id => $newProduct) {
                            if ($oldProduct["id"] == $newProduct["product_id"]) {
                                $data[$id]["quantity"] = $oldProduct['quantity'];
                            }
                        }
                    }
                }

                //Add products to stock
                $stockModel->insertToStock($data);
                //Redirect to  edited stock
                $this->redirect("stocktaking/" . $stockID);
            }
            //Handle stock edit page
            if (isset($params[1])) {
                $stock = $stockModel->getSingle($params[1]);
                if ($stock) {
                    $this->setData("stock", $stock);
                    $this->setData("activeProducts", array_column($stockModel->getProducts($stock['id']), 'id'));
                }
            }

            $this->setTitle("Stock edit");#
            $this->setTemplate("stock/stock-edit");
            //Show all stocks
        } else {
            $this->setData("stocks", $stocks);

            $this->setTitle("Stock");
            $this->setTemplate("stock/stockList");
        }
    }
}
