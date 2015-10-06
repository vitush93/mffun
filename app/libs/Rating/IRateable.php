<?php

namespace App\Libs;

/**
 * Defines contract that all rateable items must implement.
 *
 * Interface IRateable
 * @package App\Libs
 */
interface IRateable
{
    /**
     * Adds single rating representation object.
     *
     * @param $rating
     */
    function addRating($rating);

    /**
     * Returns collection of rating objects.
     *
     * @return mixed
     */
    function getRatings();

    /**
     * Returns numeric representation of item's rating.
     *
     * @return number
     */
    function getRating();

    /**
     * Sets calculated numeric item's rating value.
     *
     * @param $value
     * @return mixed
     */
    function setRating($value);
}