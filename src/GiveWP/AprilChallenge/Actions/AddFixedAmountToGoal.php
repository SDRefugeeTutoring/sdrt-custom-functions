<?php

namespace SDRT\CustomFunctions\GiveWP\AprilChallenge\Actions;

class AddFixedAmountToGoal
{
    /**
     * @param array{income: float, goal: float} $stats
     *
     * @return array
     */
    public function __invoke(array $stats, $formId, array $goal, array $args)
    {
        if ($formId != 34085) {
            return $stats;
        }

        $fixed_amount = 500;
        $stats['income'] += $fixed_amount;

        return $stats;
    }
}