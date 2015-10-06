<?php

namespace App\Libs;


class DifferenceRatingAlgorithm implements IRatingAlgorithm
{
    /** @var IRateable */
    private $rateable;

    /** @var number */
    private $result;

    /**
     * @param IRateable $rateable
     */
    function __construct(IRateable $rateable)
    {
        $this->rateable = $rateable;
    }

    /**
     * Calculates the value of item's rating by summing up all rating values.
     *
     * @return $this
     */
    function calculate()
    {
        if ($this->rateable->getRatings()->count() == 0) {
            $this->result = 0;

            return $this;
        }

        $sum = 0;

        /** @var IRating $r */
        foreach ($this->rateable->getRatings() as $r) {
            $sum += (int)$r->getValue();
        }
        $this->result = $sum;

        return $this;
    }

    /**
     * Returns calculated numeric value of item's rating.
     *
     * @return number
     */
    function getResult()
    {
        return $this->result;
    }

}