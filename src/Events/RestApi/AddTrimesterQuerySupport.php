<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Events\RestApi;

use WP_REST_Request;

class AddTrimesterQuerySupport
{
    public function __invoke(array $args, array $data, WP_REST_Request $request): array
    {
        if ( empty($request->get_param('trimester')) ) {
            return $args;
        }

        $args['tax_query'][] = [
            'taxonomy' => 'trimester',
            'field' => 'slug',
            'terms' => $request->get_param('trimester'),
        ];

        return $args;
    }
}