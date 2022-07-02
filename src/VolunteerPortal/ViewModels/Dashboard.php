<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

class Dashboard
{
    public function toArray(): array
    {
        return [
            'message' => [
                'text' => 'We take the safety and health of our students very seriously. At the same time, we are extremely fortunate to have such a generous group of volunteers eager and willing to tutor. It\'s our goal to ensure the safety of our students and also make volunteering have as low a barrier to entry as possible.',
                'urgency' => 'urgent',
            ],
            'nextEvent' => [
                'eventId' => '12345',
                'name' => 'Next Event',
                'date' => '2020-01-01T00:00:00+00:00',
                'category' => 'K-5th Grade',
                'organizer' => 'Organizer Name',
                'location' => [
                    'name' => 'Location Name',
                    'address' => '123 Main St, Anytown, CA 12345',
                ],
            ],
            'volunteerStats' => [
                'startDate' => '2020-01-01T00:00:00+00:00',
                'eventsAttended' => 42,
                'totalHours' => 123,
                'currentTrimesterAttendanceRate' => 1,
                'previousTrimesterAttendanceRate' => 0.75,
            ],
        ];
    }

    private function getNextEvent(): ?array
    {
//        $event = get_even
    }
}