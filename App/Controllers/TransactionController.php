<?php

namespace App\Controllers;

use App\Libs\PosRenderer;
use App\models\EET;
use App\models\Messages;
use App\models\Transanction;

class TransactionController extends Controller
{
    /**
     * Main controlller procces function
     * @param $params url params
     */
    function process($params)
    {
        //Check user permissions
        $this->checkSecurity();
        //Prepare transaction model
        $transactionModel = new Transanction();
        //Detect actual action
        $action = isset($params[0]) ? $params[0] : null;

        if ($action == "new") {
            //Redirect user to new transaciton
            $this->redirect("transaction/" . $transactionModel->createTransaction());
        }

        if ($action == "pay") {
            //Define new pos renderer
            $posRenderer = new posRenderer(6, 6, "pay");
            $posRenderer->preRenderTable();
            //Set POS data to template
            $this->setData("posRender", $posRenderer->renderTable());
            $this->setData("transactionId", $params[1]);
            $this->setData("isPos", true);

            //Add transaction list to template
            $transactions = $transactionModel->getTransactions();
            $this->setData("transactions", $transactions);

            //Add actual receipt id to template
            $single = $transactionModel->getSingle($params[1]);
            $this->setData("receiptId", $single['receiptId']);

            //Set title and template
            $this->setTitle("cash-register");
            $this->setTemplate("pos/payment");
        } else {
            //Define new pos renderer with products
            $posRenderer = new posRenderer(6, 6);
            $posRenderer->addProducts($transactionModel->getProductToPOS());
            //Add POS data to template
            $this->setData("posRender", $posRenderer->renderTable());
            $this->setData("transactionId", $params[0]);
            $this->setData("isPos", true);

            //Add transaction list to template
            $transactions = $transactionModel->getTransactions();
            $single = $transactionModel->getSingle($params[0]);

            //Add data to template
            $this->setData("receiptId", $single['receiptId']);
            $this->setData("transactions", $transactions);

            //Set title and template
            $this->setTitle("cash-register");
            $this->setTemplate("pos/transaction");
        }
    }
}
