<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_Post;
use WP_User;

class Requirements
{
    public function toArray(): array
    {
        /** @var WP_User $user */
        $user = wp_get_current_user();

        switch ($user->background_check) {
            case 'Yes':
                $backgroundCheck = 'passed';
                break;
            case 'No':
                $backgroundCheck = 'failed';
                break;
            case 'Cleared':
                $backgroundCheck = 'cleared';
                break;
            case 'Invited':
                $backgroundCheck = 'invited';
                break;
            default:
                $backgroundCheck = 'pending';
        }

        $backgroundPassed = $backgroundCheck === 'passed';
        $orientationPassed = $user->sdrt_orientation_attended === 'Yes';
        $conductPassed = $user->sdrt_coc_consented === 'Yes';
        $releasePassed = $user->sdrt_waiver_consented === 'Yes';

        return [
            'allPassed' => $backgroundPassed && $orientationPassed && $conductPassed && $releasePassed,
            'backgroundCheck' => [
                'status' => $backgroundCheck,
                'inviteUrl' => $user->background_check_invite_url,
            ],
            'orientation' => [
                'completed' => $orientationPassed,
                'upcomingEvents' => $this->getUpcomingOrientations(),
            ],
            'codeOfConduct' => [
                'completed' => $conductPassed,
            ],
            'volunteerRelease' => [
                'completed' => $releasePassed,
            ],
        ];
    }

    private function getUpcomingOrientations(): array
    {
        $orientations = tribe_get_events([
            'posts_per_page' => 5,
            'eventDisplay' => 'list',
            'tax_query' => [
                [
                    'taxonomy' => 'tribe_events_cat',
                    'field' => 'slug',
                    'terms' => 'orientation-dates',
                ],
            ],
        ]);

        return array_map(static function (WP_Post $event) {
            return [
                'id' => $event->ID,
                'address' => [
                    'street' => tribe_get_address($event),
                    'city' => tribe_get_city($event),
                    'state' => tribe_get_region($event),
                    'zipCode' => tribe_get_zip($event),
                    'mapLink' => tribe_get_map_link($event->ID)
                ],
                'organizer' => tribe_get_organizer($event),
                'title' => $event->post_title,
                'date' => $event->event_date,
                'link' => get_permalink($event),
            ];
        }, $orientations);
    }
}