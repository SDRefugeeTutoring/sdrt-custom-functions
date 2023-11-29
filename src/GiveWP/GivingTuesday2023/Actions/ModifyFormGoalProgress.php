<?php

namespace SDRT\CustomFunctions\GiveWP\GivingTuesday2023\Actions;

use GiveFunds\Repositories\Revenue as RevenueRepository;

class ModifyFormGoalProgress
{
    private RevenueRepository $revenueRepository;

    public function __construct(RevenueRepository $revenueRepository)
    {
        $this->revenueRepository = $revenueRepository;
    }

    /**
     * @param array{income: float, goal: float} $stats
     */
    public function __invoke(array $stats, $formId, array $goal, array $args): array
    {
        if ($formId != 38284) {
            return $stats;
        }

        // The amount that is being used to double the revenue, which we want to hide from the goal
        $doubleOffset = 2450;

        // Amount that boosts the goal, representing money given offline
        $boostOffset = 4300;

        // The revenue for the fund that's come in from any form
        $fundRevenue = $this->revenueRepository->getFundRevenue(4);

        $stats['income'] = ($fundRevenue - $doubleOffset + $boostOffset) * 2;

        return $stats;
    }
}