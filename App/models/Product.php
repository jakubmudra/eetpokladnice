<?php

namespace App\models;

class Product
{
    /**
     * Get all products
     * @return mixed
     */
    public function getAll()
    {
        return Db::allRows('SELECT * FROM `product` ORDER BY `id` ASC');
    }

    /**
     * Get all taxes
     * @return mixed
     */
    public function getAllTaxes()
    {
        return Db::allRows("select * from taxes");
    }

    /**
     * Get single tax
     * @param $id
     * @return mixed
     */
    public function getTax($id)
    {
        return Db::oneRow("select * from taxes where id = ?", [$id]);
    }


    /**
     * Get price by product id
     * @param $pid
     * @return float|int
     */
    public function getPrice($pid)
    {
        $product = $this->getSingle($pid);
        $taxes = $this->getTax($product["tax_id"]);
        $price = $this->getSingle($pid)["price"];

        return ((1 + $taxes['value'] / 100) * $price);
    }

    /**
     * Get product types
     * @return mixed
     */
    public function getTypes()
    {
        return Db::allRows("SELECT * FROM product_type");
    }

    /**
     * Get product stock quantity
     * @param int $id
     * @return int|mixed
     */
    public function getProductStockQuantity(int $id)
    {
        return Db::singleEntry(
                "SELECT sum(p.quantity) count FROM products_in_stock p WHERE p.product_id = ?",
                [$id]
            ) ?? 0;
    }

    /**
     * get single products
     * @param int $id
     * @return mixed
     */
    public function getSingle(int $id)
    {
        return Db::oneRow('SELECT * FROM product WHERE id = ?', [$id]);
    }

    /**
     * Save product
     * @param $product
     * @param bool $id
     */
    public function saveProduct($product, $id = false)
    {
        if (!$id) {
            Db::insert('product', $product);
        } else {
            Db::update('product', $product, 'WHERE id = ?', [$id]);
        }
    }

    /**
     * Save product meta
     * @param $key
     * @param $value
     * @param $product_id
     */
    public function saveMeta($key, $value, $product_id)
    {
        $id = Db::oneRow("SELECT * FROM product_meta WHERE product_id = ? AND meta_key = ?", [$product_id, $key]);
        $id = $id["id"];
        if ($id) {
            Db::update(
                'product_meta',
                ['meta_key' => $key, "meta_value" => $value, "product_id" => $product_id],
                'WHERE id = ?',
                [$id]
            );
        } else {
            Db::insert('product_meta', ['meta_key' => $key, "meta_value" => $value, "product_id" => $product_id]);
        }
    }

    /**
     * Get product meta
     * @param $key
     * @param $product_id
     * @return mixed
     */
    public function getMeta($key, $product_id)
    {
        return Db::oneRow("SELECT * FROM product_meta WHERE meta_key = ? AND product_id = ?", [$key, $product_id]);
    }

    /**
     * remove product
     * @param $id
     */
    public function removeProduct($id)
    {
        Db::rowCount('DELETE FROM product WHERE id = ?', [$id]);
    }
}
