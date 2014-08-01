<?php

namespace App\Libs;


use Nette\Object;

class Utils extends Object
{
    public static function safeExplodeByComma(&$data)
    {
        $data = trim($data, ' ,');
        $data = explode(',', $data);
        array_walk($data, function (&$value, $key) {
            $value = trim($value, ' ,');
        });
        $data = array_values(array_unique(array_filter($data)));
    }
} 