<?php

namespace App\Libs;


class BooleanRatingAlgorithm implements IRatingAlgorithm
{
    /** @var  number */
    private $result;

    /** @var IRateable */
    private $rateable;

    /**
     * @param IRateable $rateable
     */
    function __construct(IRateable $rateable)
    {
        $this->rateable = $rateable;
    }

    /**
     * Calculate numeric rating of rateable item:
     * Simply count number of positive/negative rating.
     *
     * @param int $value value to count
     * @return $this
     */
    function calculateBy($value)
    {
        $sum = 0;

        /** @var IRating $r */
        foreach ($this->rateable->getRatings() as $r) {
            if ($r->getValue() == $value) {
                $sum++;
            }
        }

        $this->result = $sum;

        return $this;
    }

    /**
     * Returns calculated numeric value of item's rating.
     *
     * @return number
     * @throws InvalidOperationException
     */
    function getResult()
    {
        return $this->result;
    }


}