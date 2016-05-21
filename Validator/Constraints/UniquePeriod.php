<?php

namespace CPASimUSante\SimupollBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniquePeriod extends Constraint
{
    public $message = 'The date {{ date }} is already in a period for this Simupoll';

    public function validatedBy()
    {
        return 'unique_period_validator';
    }
}
