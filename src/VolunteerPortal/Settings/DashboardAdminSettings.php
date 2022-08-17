<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Settings;

use HD_WP_Settings_API;

class DashboardAdminSettings
{
    public function __invoke()
    {
        new HD_WP_Settings_API($this->menuRegistration(), $this->fieldsRegistration());
    }

    private function menuRegistration(): array
    {
        return [
            'page_title' => 'Volunteer Dashboard Settings',
            'menu_title' => 'Dashboard Settings',
            'parent_slug' => 'edit.php?post_type=rsvp',
            'menu_slug' => 'sdrt_dashboard_settings',
            'capability' => 'can_view_rsvps',
            'icon' => 'dashicons-admin-generic',
        ];
    }

    private function fieldsRegistration(): array
    {
        return [
            'sdrt_volunteer_message_section' => [
                'title' => 'Volunteer Message',
                'type' => 'section',
                'desc' => 'A message to display to all volunteers from the dashboard.',
            ],
            'sdrt_volunteer_message_enabled' => [
                'title' => 'Display Message',
                'type' => 'checkbox',
            ],
            'sdrt_volunteer_message_urgency' => [
                'title' => 'Message Type',
                'type' => 'select',
                'default' => 'info',
                'choices' => [
                    'info' => 'Info',
                    'warning' => 'Warning',
                    'urgent' => 'Urgent',
                ]
            ],
            'sdrt_volunteer_message_heading' => [
                'title' => 'Heading',
                'type' => 'text',
                'sanit' => 'html',
            ],
            'sdrt_volunteer_message' => [
                'title' => 'Message',
                'type' => 'textarea',
                'sanit' => 'html',
            ]
        ];
    }
}