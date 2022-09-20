<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_Post;

use function SDRT\CustomFunctions\Helpers\Events\get_event_category_name;

class NextEvent
{
    /**
     * @var WP_Post
     */
    private $event;

    public function __construct(WP_Post $event)
    {
        $this->event = $event;
    }

    public function toArray(): array
    {
        return [
            'eventId' => $this->event->ID,
            'name' => $this->event->post_title,
            'date' => $this->event->event_date,
            'category' => get_event_category_name($this->event->ID),
        ];
    }
}