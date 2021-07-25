<?php

declare(strict_types=1);

/**
 * @return object|WP_Error
 */
function sdrtCreateCheckrCandidate(string $firstName, string $lastName, string $email, DateTime $dateOfBirth): object
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

    return json_decode(wp_remote_retrieve_body($response), false);
}

/**
 * @return object|WP_Error
 */
function sdrtCreateCheckrInvitation(string $candidateId): object
{
    $response = wp_remote_post('https://api.checkr.com/v1/invitations', [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode(SDRT_CHECKR_API . ':' . ''),
        ],
        'body' => [
            'package' => 'tasker_standard',
            'candidate_id' => $candidateId,
        ],
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    return json_decode(wp_remote_retrieve_body($response), false);
}