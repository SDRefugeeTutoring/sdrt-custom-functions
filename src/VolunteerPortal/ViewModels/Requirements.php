<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_User;

class Requirements
{
    public function toArray(): array
    {
        /** @var WP_User $user */
        $user = wp_get_current_user();

        switch ($user->background_check) {
            case 'Yes':
                $backgroundCheck = 'passed';
                break;
            case 'No':
                $backgroundCheck = 'failed';
                break;
            case 'Cleared':
                $backgroundCheck = 'cleared';
                break;
            case 'Invited':
                $backgroundCheck = 'invited';
                break;
            default:
                $backgroundCheck = 'pending';
        }

        return [
            'backgroundCheck' => [
                'status' => $backgroundCheck,
                'inviteUrl' => $user->background_check_invite_url,
            ],
            'orientation' => [
                'completed' => $user->sdrt_orientation_attended === 'Yes',
            ],
            'codeOfConduct' => [
                'completed' => $user->sdrt_coc_consented === 'Yes',
            ],
            'volunteerRelease' => [
                'completed' => $user->sdrt_waiver_consented === 'Yes',
            ],
        ];
    }
}