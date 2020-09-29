<?php

namespace Extrablind\MonitHomeBundle\Utilities;

use RRule\RRule;

class RruleUtility
{
    public function getNextDateFromNow($rruleString, $iteration = null, $limit = 10)
    {
        $rrule  = new RRule($rruleString);
        $now    = new \DateTime('now');
        $future = new \DateTime('now +10 years');
        $dates  = $rrule->getOccurrencesBetween($now, $future, $limit);

        foreach ($dates as $i => $utcDate) {
            if (null === $iteration) {
                return $utcDate;
            }
            if ($i === $iteration - 1) {
                return $utcDate;
            }
        }

        return false;
    }

    public function convertUTCToLocal($rruleString)
    {
        $rrule = new RRule($rruleString);

        foreach ($rrule as $occurrence) {
            return $occurrence;
        }

        return false;
    }
}
