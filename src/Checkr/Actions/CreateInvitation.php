<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Checkr\Actions;

use SDRT\CustomFunctions\Checkr\DataTransferObjects\Invitation;
use WP_Error;

class CreateInvitation
{
    /**
     * @return Invitation|WP_Error
     */
    public function __invoke(string $candidateId)
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

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if ( !empty($data['error']) ) {
            return new WP_Error('checkr_invitation_error', $data['error']);
        }

        return Invitation::fromResponse($data);
    }
}