<?php

namespace App\Libs;

/**
 * Defines interface for rating values.
 *
 * Interface IRating
 * @package App\Libs
 */
interface IRating
{
    /**
     * Get numeric representation of this rating object.
     *
     * @return number
     */
    function getValue();
}