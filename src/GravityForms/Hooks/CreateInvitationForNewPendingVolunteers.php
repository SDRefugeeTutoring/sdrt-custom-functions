<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\GravityForms\Hooks;

use DateTime;
use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;
use SDRT\CustomFunctions\Support\Log;

use function get_user_by;
use function is_wp_error;
use function update_user_meta;

class CreateInvitationForNewPendingVolunteers
{
    public function __invoke(int $userId, array $feed, array $entry, string $password)
    {
        $user = get_user_by('id', $userId);

        if ( ! in_array('volunteer_pending', $user->roles)) {
            return;
        }

        $dateOfBirth = new DateTime($user->your_date_of_birth);
        $candidate = sdrt(CreateCandidate::class)($user->first_name, $user->last_name, $user->user_email, $dateOfBirth);

        if (is_wp_error($candidate)) {
            Log::error(
                "Checkr failed to create a Candidate for the registered user",
                [
                    'Date of Birth' => $dateOfBirth,
                    'User ID' => $userId,
                    'Candidate Error' => $candidate,
                ]
            );

            return;
        }

        $invitation = sdrt(CreateInvitation::class)($candidate->id);

        if (is_wp_error($invitation)) {
            Log::error(
                "Checkr failed to create an Invitation for the registered user",
                [
                    'Date of Birth' => $dateOfBirth,
                    'User ID' => $userId,
                    'Candidate' => $candidate,
                    'Invitation Error' => $invitation,
                ]
            );

            return;
        }

        update_user_meta($user->ID, 'background_check_candidate_id', $candidate->id);
        update_user_meta($user->ID, 'background_check_invite_url', $invitation->invitationUrl);
        update_user_meta($user->ID, 'background_check', 'Invited');
    }
}