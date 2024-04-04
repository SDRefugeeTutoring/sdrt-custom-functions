<?php

namespace SDRT\CustomFunctions\Events\Hooks;

use WP_Query;

class HideInPersonEventsInEventsArchive
{
    public function __invoke(WP_Query $query)
    {
        if (!is_admin() && $query->is_main_query() && $query->is_archive()) {
            $query->set('meta_query', [
                [
                    'key' => 'event_type',
                    'value' => 'event-in-person',
                    'compare' => '!=',
                ],
            ]);
        }
    }
}