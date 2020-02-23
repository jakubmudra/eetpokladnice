<?php

namespace App\Libs;

use App\Config\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Translator extends AbstractExtension
{
    /**
     * Prepare twig functions
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('__', [$this, 'getTranslantion']),
        ];
    }

    public function getTranslantion(string $key)
    {
        $file = json_decode(file_get_contents( Config::$translantionFile . Config::$language . ".json"));
        $rtn = $file->$key ?? $key;
        return $rtn;
    }
}
