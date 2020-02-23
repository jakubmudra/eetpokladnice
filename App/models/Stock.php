<?php

namespace App\models;

class Stock
{
    /**
     * Get all stocks
     * @return mixed
     */
    public function getAll()
    {
        return Db::allRows('SELECT * FROM `stock` ORDER BY `id` ASC');
    }

    /**
     * Get single stock
     * @param int $id
     * @return mixed
     */
    public function getSingle(int $id)
    {
        return Db::oneRow('SELECT * FROM stock WHERE id = ?', [$id]);
    }

    /**
     * Get all stock products
     * @param $stockId
     * @return mixed
     */
    public function getProducts($stockId)
    {
        $products = Db::allRows(
            "SELECT p.id, p.name,p.price, p.visibility, pin.quantity, pin.min_order_quantity FROM products_in_stock pin LEFT JOIN product p ON pin.product_id=p.id WHERE pin.stock_id = ?",
            [$stockId]
        );
        return $products;
    }

    /**
     * Update product quantiy
     * @param $data
     */
    public function updateQuantity($data)
    {
        Db::update(
            'products_in_stock',
            $data,
            'WHERE product_id = ? AND stock_id = ?',
            [$data["product_id"], $data["stock_id"]]
        );
    }

    /**
     * Insert product to stock
     * @param $data
     */
    public function insertToStock($data)
    {
        if (count($data) == count($data, COUNT_RECURSIVE)) {
            Db::insert("products_in_stock", $data);
        } else {
            Db::insertMultiple("products_in_stock", $data);
        }
    }

    /**
     * Save / insert stock
     * @param $stock
     * @param bool $id
     * @return mixed
     */
    public function saveStock($stock, $id = false)
    {
        if (!$id) {
            Db::insert('stock', $stock);
        } else {
            Db::update('stock', $stock, 'WHERE id = ?', [$id]);
        }

        return Db::getLastId();
    }

    /**
     * Remove stock by id
     * @param $id
     */
    public function removeStock($id)
    {
        Db::rowCount('DELETE FROM stock WHERE id = ?', [$id]);
    }

    /**
     * Remove all products from stock
     * @param $id
     */
    public function cleanStock($id)
    {
        Db::rowCount('DELETE FROM products_in_stock WHERE stock_id = ?', [$id]);
    }
}
