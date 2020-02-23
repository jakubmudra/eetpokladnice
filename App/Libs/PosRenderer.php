<?php

namespace App\Libs;

use App\Config\Config;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

class PosRenderer
{
    public $cols;
    public $rows;
    public $products = [];
    public $table;
    public $type;

    /**
     * PosRenderer constructor.
     * @param int $c pos collumns
     * @param int $r pos rows
     * @param string $type pos type ( pos OR pay only )
     */
    public function __construct($c = 6, $r = 6, $type = "pos")
    {
        $this->rows = $r;
        $this->cols = $c;
        $this->type = $type;

        $loader = new FilesystemLoader(Config::$templateDirectory);
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addExtension(new Translator());
    }

    /**
     * Hard typed payments keys
     * @return array off payments types
     */
    private function getPaymentKeys()
    {
        $paymentKeys = [
            [
                'id' => 20,
                'domId' => 'cashInput',
                'type' => 'input',
                'position' => ['x' => 1, 'y' => ($this->rows - 4)],
                'colSpan' => 3,
                'value' => '0.00',
                'color' => 'dark-grey',
            ],
            [
                'id' => 21,
                'type' => 'input',
                'jsAction' => 'removeLast()',
                'position' => ['x' => 4, 'y' => ($this->rows - 4)],
                'value' => 'C',
                'color' => 'orange',
            ],
            [
                'id' => 22,
                'type' => 'input',
                'jsAction' => 'clearCash()',
                'position' => ['x' => 5, 'y' => ($this->rows - 4)],
                'value' => 'CE',
                'color' => 'red',
            ],
            [
                'id' => 1,
                'type' => 'number',
                'domId' => '1',
                'jsAction' => 'addCash(1)',
                'position' => ['x' => 1, 'y' => ($this->rows - 3)],
                'value' => '1',
                'color' => 'dark-grey',
            ],
            [
                'id' => 2,
                'type' => 'number',
                'jsAction' => 'addCash(2)',
                'position' => ['x' => 2, 'y' => ($this->rows - 3)],
                'value' => '2',
                'color' => 'dark-grey',
            ],
            [
                'id' => 3,
                'type' => 'number',
                'jsAction' => 'addCash(3)',
                'position' => ['x' => 3, 'y' => ($this->rows - 3)],
                'value' => '3',
                'color' => 'dark-grey',
            ],
            [
                'id' => 4,
                'type' => 'number',
                'jsAction' => 'addCash(4)',
                'position' => ['x' => 1, 'y' => ($this->rows - 2)],
                'value' => '4',
                'color' => 'dark-grey',
            ],
            [
                'id' => 5,
                'type' => 'number',
                'jsAction' => 'addCash(5)',
                'position' => ['x' => 2, 'y' => ($this->rows - 2)],
                'value' => '5',
                'color' => 'dark-grey',
            ],
            [
                'id' => 6,
                'type' => 'number',
                'position' => ['x' => 3, 'y' => ($this->rows - 2)],
                'value' => '6',
                'jsAction' => 'addCash(6)',
                'color' => 'dark-grey',
            ],
            [
                'id' => 7,
                'type' => 'number',
                'jsAction' => 'addCash(7)',
                'position' => ['x' => 1, 'y' => ($this->rows - 1)],
                'value' => '7',
                'color' => 'dark-grey',
            ],
            [
                'id' => 8,
                'type' => 'number',
                'jsAction' => 'addCash(8)',
                'position' => ['x' => 2, 'y' => ($this->rows - 1)],
                'value' => '8',
                'color' => 'dark-grey',
            ],
            [
                'id' => 9,
                'type' => 'number',
                'jsAction' => 'addCash(9)',
                'position' => ['x' => 3, 'y' => ($this->rows - 1)],
                'value' => '9',
                'color' => 'dark-grey',
            ],
            [
                'id' => 10,
                'type' => 'delimiter',
                'jsAction' => 'addDelimiter()',
                'position' => ['x' => 1, 'y' => ($this->rows)],
                'value' => '.',
                'color' => 'dark-grey',
            ],
            [
                'id' => 11,
                'type' => 'number',
                'jsAction' => 'addCash(0)',
                'position' => ['x' => 2, 'y' => ($this->rows)],
                'value' => '0',
                'color' => 'dark-grey',
            ],
            [
                'id' => 12,
                'type' => 'number',
                'jsAction' => 'addCash(00)',
                'position' => ['x' => 3, 'y' => ($this->rows)],
                'value' => '00',
                'color' => 'dark-grey',
            ],
            [
                'id' => 13,
                'type' => 'number',
                'jsAction' => 'plusCash(100)',
                'position' => ['x' => 4, 'y' => ($this->rows - 3)],
                'value' => '100',
                'color' => 'blue',
            ],
            [
                'id' => 14,
                'type' => 'number',
                'jsAction' => 'plusCash(200)',
                'position' => ['x' => 5, 'y' => ($this->rows - 3)],
                'value' => '200',
                'color' => 'blue',
            ],
            [
                'id' => 15,
                'type' => 'number',
                'jsAction' => 'plusCash(500)',
                'position' => ['x' => 4, 'y' => ($this->rows - 2)],
                'value' => '500',
                'color' => 'blue',
            ],
            [
                'id' => 16,
                'type' => 'number',
                'jsAction' => 'plusCash(1000)',
                'position' => ['x' => 5, 'y' => ($this->rows - 2)],
                'value' => '1000',
                'color' => 'blue',
            ],
            [
                'id' => 17,
                'type' => 'number',
                'jsAction' => 'plusCash(2000)',
                'position' => ['x' => 4, 'y' => ($this->rows - 1)],
                'value' => '2000',
                'color' => 'blue',
            ],
            [
                'id' => 18,
                'type' => 'number',
                'jsAction' => 'plusCash(5000)',
                'position' => ['x' => 5, 'y' => ($this->rows - 1)],
                'value' => '5000',
                'color' => 'blue',
            ],
            [
                'id' => 19,
                'type' => 'text',
                'position' => ['x' => 5, 'y' => ($this->rows)],
                'value' => 'PLACENO PRESNE',
                'jsAction' => 'paidExactly()',
                'colSpan' => 2,
                'color' => 'Green',
            ],
            [
                'id' => 30,
                'type' => 'text',
                'jsAction' => 'paidBy("Hotove")',
                'position' => ['x' => 4, 'y' => ($this->rows)],
                'value' => 'PLACENO',
                'color' => 'Green',
            ],
            [
                'id' => 31,
                'type' => 'text',
                'jsAction' => 'paidBy("Kartou")',
                'position' => ['x' => 6, 'y' => ($this->rows - 2)],
                'value' => 'KARTOU',
                'color' => 'ORANGE',
            ],
            [
                'id' => 32,
                'type' => 'text',
                'jsAction' => 'paidBy("Stravenka")',
                'position' => ['x' => 6, 'y' => ($this->rows - 3)],
                'value' => 'STRAVENKA',
                'color' => 'ORANGE',
            ],
            [
                'id' => 33,
                'type' => 'text',
                'jsAction' => 'goBack()',
                'position' => ['x' => 1, 'y' => 1],
                'value' => 'ZPET',
                'color' => 'blue',
            ],
            [
                'id' => 33,
                'type' => 'discount',
                'jsAction' => 'discountModal()',
                'position' => ['x' => 5, 'y' => 1],
                'value' => 'Sleva',
                'color' => 'blue',
            ],
            [
                'id' => 33,
                'type' => 'storno',
                'jsAction' => 'refundModal()',
                'position' => ['x' => 6, 'y' => 1],
                'value' => 'Refund',
                'color' => 'red',
            ]
        ];

        return $paymentKeys;
    }

    /**
     * Hard typed function keys
     * @return array of function keys
     */
    private function getFunctionKeys()
    {
        $functionKeys = [
            [
                'id' => 1,
                'displayName' => 'Pay',
                'position' => ['x' => $this->cols, 'y' => $this->rows],
                'colSpan' => 2,
                'jsAction' => 'goToPayment()',
                'color' => 'green',
                'authRequired' => false
            ],
            [
                'id' => 2,
                'displayName' => 'Cancel',
                'position' => ['x' => 5, 'y' => $this->rows],
                'jsAction' => 'cancelTransaction()',
                'color' => 'dark-red',
                'authRequired' => true
            ],
            [
                'id' => 3,
                'displayName' => 'Otevrit transakci',
                'position' => ['x' => $this->cols, 'y' => 1],
                'color' => 'green',
                'jsAction' => 'openTransactionModal()',
                'authRequired' => true
            ],
            [
                'id' => 4,
                'displayName' => 'Mnozstvi',
                'position' => ['x' => $this->cols, 'y' => 2],
                'jsAction' => 'openQuantityModal',
                'color' => 'grey',
                'authRequired' => true
            ],
            [
                'id' => 4,
                'displayName' => 'Odstranit polozku',
                'position' => ['x' => 1, 'y' => $this->rows],
                'color' => 'red',
                'authRequired' => true
            ],
            [
                'id' => 4,
                'displayName' => 'Odlozit ucet',
                'position' => ['x' => 4, 'y' => $this->rows],
                'jsAction' => 'saveTransaction()',
                'color' => 'orange',
                'authRequired' => true
            ]
        ];

        return $functionKeys;
    }

    /**
     * Add products to pos
     * @param $products
     */
    public function addProducts($products)
    {
        if (is_array($products) && !empty($products)) {
            $this->products = $products;
        }

        $this->preRenderTable();
    }

    /**
     * Prepare table to render
     */
    public function preRenderTable()
    {
        for ($y = 1; $y <= $this->rows; $y++) {
            for ($x = 1; $x <= $this->cols; $x++) {
                $this->table[] = ["x" => $x, "y" => $y, "data" => ""];
            }
        }

        if ($this->type == "pos") {
            foreach ($this->products as $product) {
                $this->addToTable($product);
            }

            foreach ($this->getFunctionKeys() as $function) {
                $this->addFunctionsToTable($function);
            }
        } else {
            foreach ($this->getPaymentKeys() as $function) {
                $this->addPayToTable($function);
            }
        }
    }

    /**
     *  Add products to table rendering
     * @param $product
     */
    private function addToTable($product)
    {
        $product = (array)$product;
        $x = $product["x"];
        $y = $product["y"];
        $id = (($this->cols * $y) - ($this->cols) + ($x - 1));
        $this->table[$id]["type"] = "product";
        $this->table[$id]["data"] = $product;
        $this->table[$id]["data"]["color"] = "blue";
    }

    /**
     * Add functions to table
     * @param $function
     */
    private function addFunctionsToTable($function)
    {
        $x = $function["position"]["x"];
        $y = $function["position"]["y"];
        $jsAction = isset($function["jsAction"]) ? $function["jsAction"] : null;
        $function["jsAction"] = $jsAction;
        $id = (($this->cols * $y) - ($this->cols) + ($x - 1));
        $this->table[$id]["type"] = "function";
        $this->table[$id]["data"] = $function;
    }

    /**
     * Add payment keys to table
     * @param $function
     */
    private function addPayToTable($function)
    {
        $x = $function["position"]["x"];
        $y = $function["position"]["y"];
        $id = (($this->cols * $y) - ($this->cols) + ($x - 1));
        $this->table[$id]["type"] = "pay";
        $this->table[$id]["data"] = $function;
    }


    /**
     * Render table
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderTable()
    {
        $render = "";

        for ($i = 1; $i <= count($this->table); $i++) {
            $item = $this->table[$i - 1];
            $d = isset($item["type"]) ? $item["type"] : null;
            $colSpan = isset($item["data"]["colSpan"]) ? $item["data"]["colSpan"] : null;

            if (!$d) {
                $render = $render . $this->twig->render("pos/items/empty" . Config::$templateFileExtension);
            } elseif ($item["type"] == "product") {
                $active = (is_array($item["data"]) > 0) ? "active" : "";
                $render = $render . $this->twig->render(
                        "pos/items/product" . Config::$templateFileExtension,
                        ["item" => $item, "active" => $active]
                    );
            } elseif ($item["type"] == "function") {
                $render = $render . $this->twig->render(
                        "pos/items/function" . Config::$templateFileExtension,
                        ["item" => $item]
                    );
            } elseif ($item["type"] == "pay") {
                $domId = isset($item["data"]["domId"]) ? $item["data"]["domId"] : $item["data"]["id"];
                $render = $render . $this->twig->render(
                        "pos/items/pay" . Config::$templateFileExtension,
                        [
                            "item" => $item,
                            "colSpan" => $colSpan,
                            "type" => $item["data"]["type"],
                            "domId" => $domId
                        ]
                    );
                if ($colSpan) {
                    $i = $i + $colSpan - 1;
                }
            }

            if ($i % $this->cols == 0 && $i != 0) {
                $render = $render . '<div class="w-100"></div>';
            }
        }
        foreach ($this->table as $item) {
        }

        return $render;
    }
}
