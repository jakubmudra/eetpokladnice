<?php

namespace App\models;

class Transanction
{
    /**
     * Get transaction by id
     * @param int|null $id
     * @return bool
     */
    public function getTransaction(int $id = null)
    {
        // If is id null, return false
        if ($id === null) {
            return false;
        }

        $transaction = $this->getSingle($id);

        if (count($transaction) == 0) {
            return false;
        }

        return $transaction;
    }

    /**
     * update transaction
     * @param $data
     * @param $id
     */
    public function update($data, $id)
    {
        Db::update('transaction', $data, 'WHERE id = ?', [$id]);
    }

    /**
     * Remove transaction
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        Db::oneRow(" DELETE FROM products_in_transaction WHERE transaction_id = ?", [$id]);
        return Db::oneRow("DELETE FROM transaction WHERE id = ?", [$id]);
    }

    /**
     * Get product to pos ( formated )
     * @return mixed
     */
    public function getProductToPOS()
    {
        return Db::allRows(
            'select p.id,p.name, p.price, p.tax_id, t.value, round(p.price * (1 + t.value/100 ) , 2) calculatedPrice, CASE WHEN p.visibility = 0 THEN "false" ELSE "true" END visibility,max(case when meta_key = "xPos" then meta_value end) x,max(case when meta_key = "yPos" then meta_value end) y from product p JOIN product_meta pm ON p.id = pm.product_id JOIN taxes t ON t.id = p.tax_id WHERE pm.meta_key IN ("xPos", "yPos") AND p.visibility = 1 GROUP BY pm.product_id;'
        );
    }

    /**
     * Get products from transaction (formated)
     * @param $tid
     * @return mixed
     */
    public function getTransactionProducts($tid)
    {
        return Db::allRows(
            'select p.id, p.name name,pit.quantity quantity, round(p.price * (1 + t.value/100 ) , 2) price from product p JOIN taxes t on t.id = p.tax_id JOIN products_in_transaction pit ON p.id = pit.product_id WHERE pit.transaction_id = ?',
            [$tid]
        );
    }

    /**
     * Generate receipt id
     * @param $id int transaction id
     * @return string
     */
    public function generateReceiptID($id)
    {
        return date("Ymd") . "/" . $id;
    }

    /**
     * Save product to tranasction
     * @param $pid int product id
     * @param $tid int transaction id
     * @param $q
     */
    public function saveProductToTransaction($pid, $tid, $q)
    {
        $result = Db::oneRow(
            "SELECT id, quantity FROM products_in_transaction WHERE product_id = ? AND transaction_id = ?",
            [$pid, $tid]
        );
        $quantity = isset($result["quantity"]) ? $result["quantity"] : null;
        $quantity = (is_null($quantity)) ? $q : $quantity + $q;

        $product = ["product_id" => $pid, "transaction_id" => $tid, "quantity" => $quantity];

        if (!$result) {
            Db::insert('products_in_transaction', $product);
        } else {
            Db::update('products_in_transaction', $product, 'WHERE id = ?', [$result["id"]]);
        }
    }

    /**
     * Add payment method to transaction
     * @param $methods
     * @param $tid
     */
    public function addPaymentMethod($methods, $tid)
    {
        foreach ($methods as $method) {
            Db::insert(
                "payments_in_transactions",
                ["transaction_id" => $tid, "payment_name" => $method["name"], "ammount" => $method["value"]]
            );
        }
    }

    /**
     * Check, if there is a uncomleted transaction with total of 0. If not, create new.
     * @return mixed id of last transaction
     */
    public function createTransaction()
    {
        $rtn = Db::allRows(
            "select t.id, t.total, tm.meta_value from transaction t JOIN transaction_meta tm WHERE tm.meta_key = 'receiptId' and t.completed != 1 AND t.total = 0 order by t.id desc"
        );
        if (count($rtn) <= 1) {
            Db::insert("transaction", ["date_time" => date("Y-m-d H:i:s"), "total" => 0]);
            $lastID = Db::getLastId();
            Db::insert(
                "transaction_meta",
                [
                    "meta_key" => "receiptId",
                    "meta_value" => $this->generateReceiptID($lastID),
                    "transaction_id" => $lastID
                ]
            );
        } else {
            $lastID = $rtn[0]["id"];
        }

        return $lastID;
    }

    /**
     * Return single transaction ( with id, date_time, total price, completed state, receiptID number )
     * @param int $id id of transaction
     * @return mixed Single transaction if exist
     */
    public function getSingle(int $id)
    {
        return Db::oneRow(
            "SELECT t.id , t.date_time date_time, t.total total, t.completed completed, tm.meta_value receiptId  FROM transaction t JOIN transaction_meta tm ON tm.transaction_id = t.id WHERE tm.meta_key = 'receiptId' and t.id = ?",
            [$id]
        );
    }

    /**
     * Get all incompleted transactions ordered by datetime desc
     * @return mixed array of transactions if exists
     */
    public function getTransactions()
    {
        return Db::allRows("SELECT * FROM transaction WHERE completed = '' ORDER BY date_time desc");
    }

}
