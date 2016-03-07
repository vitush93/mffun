<?php

namespace App\Libs;


interface Enum
{
    /**
     * Checks if given number is withing enum bounds.
     *
     * @param $num
     * @return boolean TRUE if $num is OK, FALSE if otherwise
     */
    static function check($num);
}