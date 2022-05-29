import {Link} from '@chakra-ui/react';
import {ReactNode} from 'react';

type Requirement = {
    completed: boolean;
};

export interface Requirements {
    backgroundCheck: {
        status: BackgroundCheckStatus;
        inviteUrl: string;
    };
    orientation: Requirement;
    codeOfConduct: Requirement;
    volunteerRelease: Requirement;
}

export type BackgroundCheck = {
    status: BackgroundCheckStatus;
    invitationUrl: string;
};

export enum BackgroundCheckStatus {
    PASSED = 'passed',
    FAILED = 'failed',
    CLEARED = 'cleared',
    INVITED = 'invited',
    PENDING = 'pending',
    INVITE_ERROR = 'invite_error',
    CANDIDATE_ERROR = 'candidate_error',
    DOB_ERROR = 'dob_error',
}

export function getBackgroundCheckMessage({status, invitationUrl}: BackgroundCheck): ReactNode {
    switch (status) {
        case BackgroundCheckStatus.PASSED:
            return 'Your background check has cleared! Thank you!';
        case BackgroundCheckStatus.FAILED:
            return 'Your background check did not clear. You are not allowed to tutor with us at this time. If you have questions about this at all, please contact boardmembers@sdrefugeetutoring.com';
        case BackgroundCheckStatus.CLEARED:
            return 'Your background check has cleared from the previous year and needs to be renewed.';
        case BackgroundCheckStatus.INVITED:
            return (
                <>
                    You have been invited to take a background check and your status is still pending. You can{' '}
                    <Link href={invitationUrl} target="_blank" rel="noopener noreferrer">
                        check on the status
                    </Link>{' '}
                    on the Checkr website.
                </>
            );
        case BackgroundCheckStatus.PENDING:
            return 'Please request a background check to begin the process.';
        case BackgroundCheckStatus.INVITE_ERROR:
            return 'There was an issue creating a Checkr Candidate, please try again and contact the volunteer coordinator if the problem persists.';
        case BackgroundCheckStatus.CANDIDATE_ERROR:
            return 'There was an issue creating a Checkr Candidate, please try again and contact the volunteer coordinator if the problem persists.';
        case BackgroundCheckStatus.DOB_ERROR:
            return 'Please update your profile to include your date of birth.';
        default:
            return <strong>Warning:</strong> + 'Unrecognized background check status, please contact an administrator.';
    }
}
