<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_Term;

class UpcomingEvents
{
    public function toArray(): array
    {
        return [
            'trimesters' => $this->getTrimesters(),
            'categories' => [
                'k5' => $this->termToArray(get_term_by('slug', 'k-5th-grade', 'tribe_events_cat')),
                'middle' => $this->termToArray(get_term_by('slug', 'middle-high-school', 'tribe_events_cat')),
                'other' => $this->termToArray(get_term_by('slug', 'non-tutoring-event', 'tribe_events_cat')),
            ],
        ];
    }

    private function getTrimesters(): array
    {
        return array_map([$this, 'termToArray'],
            get_terms(
                [
                    'taxonomy' => 'trimester',
                    'hide_empty' => true,
                    'meta_query' => [
                        [
                            'key' => 'end_date',
                            'value' => date('Ymd'),
                            'compare' => '>=',
                            'type' => 'DATE',
                        ],
                    ],
                ]
            )
        );
    }

    private function termToArray(WP_Term $trimester): array
    {
        return [
            'id' => $trimester->term_id,
            'name' => htmlspecialchars_decode($trimester->name),
            'slug' => $trimester->slug,
        ];
    }
}