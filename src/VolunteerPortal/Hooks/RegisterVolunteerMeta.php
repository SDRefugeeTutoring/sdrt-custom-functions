<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

use DateTime;

/**
 * Registers meta to be accessible via the REST API
 */
class RegisterVolunteerMeta
{
    public function __invoke(): void
    {
        register_meta('user', 'your_date_of_birth', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => function($value) {
                return (new DateTime($value))->format('Y-m-d');
            }
        ]);
    }
}