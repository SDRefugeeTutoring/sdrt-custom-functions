<?php

namespace SDRT\CustomFunctions\Events\Hooks;

use WP_Query;

class HideInPersonEventsInEventsArchive
{
    public function __invoke(WP_Query $query)
    {
        $isMainEventQuery = !is_admin() && $query->is_main_query() && $query->is_post_type_archive('tribe_events');

        $isEventDateQuery = defined('DOING_AJAX') && DOING_AJAX && isset($query->query_vars['meta_query']['_eventhidefromupcoming_not_exists']) && !empty($query->get('post__in'));

        if ($isMainEventQuery || $isEventDateQuery) {
            $metaQuery = $query->get('meta_query', []);
            $metaQuery['_hideInPersonEvents'] = [
                'key' => 'event_type',
                'value' => 'event-in-person',
                'compare' => '!=',
            ];

            $query->set('meta_query', $metaQuery);
        }
    }
}