<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Checkr\Actions;

use DateTime;
use WP_Error;
use SDRT\CustomFunctions\Checkr\DataTransferObjects\Candidate;

class CreateCandidate
{
    /**
     * @return Candidate|WP_Error
     */
    public function __invoke(string $firstName, string $lastName, string $email, DateTime $dateOfBirth)
    {
        $response = wp_remote_post('https://api.checkr.com/v1/candidates', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(SDRT_CHECKR_API . ':' . ''),
            ],
            'body' => [
                'first_name' => $firstName,
                'no_middle_name' => true,
                'last_name' => $lastName,
                'email' => $email,
                'dob' => $dateOfBirth->format('Y-m-d'),
            ],
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        return Candidate::fromResponse($data);
    }
}