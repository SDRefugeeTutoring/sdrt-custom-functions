<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Events\RestApi;

class AddEventData
{
    public function __invoke(array $eventData): array
    {
        $user = wp_get_current_user();

        if (!$user) {
            return $eventData;
        }

        foreach ($eventData['events'] as &$event) {
            $rsvp = get_user_rsvp_for_event($user->ID, $event['id']);

            $event['rsvpStatus'] = $rsvp ? $rsvp->attending === 'yes' : null;
        }

        return $eventData;
    }
}