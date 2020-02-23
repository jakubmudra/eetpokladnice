<?php

namespace App\Libs;

class Formater
{
    /**
     * Return two stock diference ( to add products to stock )
     * @param $ar1
     * @param $ar2
     * @return array
     */
    public static function stockProductDiff($ar1, $ar2)
    {
        $ids = [
            array_column($ar1, "id"),
            array_column($ar2, "id")
        ];

        $diff = array_udiff($ar2, $ar1, 'self::udiffCompare');

        return $diff;
    }

    /**
     * comparing
     * @param $a
     * @param $b
     * @return mixed
     */
    private static function udiffCompare($a, $b)
    {
        return $a['id'] - $b['id'];
    }


}
