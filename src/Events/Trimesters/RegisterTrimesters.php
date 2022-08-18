<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Events\Trimesters;

class RegisterTrimesters
{
    public function register(): void
    {
        register_taxonomy(
            'trimester',
            'tribe_events',
            [
                'label' => __('Trimesters', 'sdrt'),
                'rewrite' => ['slug' => 'trimester'],
                'hierarchical' => true,
            ]
        );
    }
}