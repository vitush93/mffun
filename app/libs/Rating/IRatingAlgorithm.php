<?php

namespace App\Libs;

/**
 * Defines interface for classes that are responsible for calculating rateable item's rating.
 *
 * Interface IRating
 * @package App\Libs
 */
interface IRatingAlgorithm
{
    /**
     * Returns calculated numeric value of item's rating.
     *
     * @return number
     */
    function getResult();
}