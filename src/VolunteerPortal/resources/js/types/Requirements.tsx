import {Link} from '@chakra-ui/react';
import {ReactNode} from 'react';

type Requirement = {
    completed: boolean;
};

export interface Requirements {
    allPassed: boolean;
    backgroundCheck: {
        status: BackgroundCheckStatus;
        inviteUrl: string;
    };
    orientation: {
        completed: boolean;
        upcomingEvents: Array<{
            id: string | number;
            title: string;
            address: {
                street: string;
                city: string;
                state: string;
                zipCode: string;
                mapLink: string;
            };
            organizer: string;
            date: string;
            link: string;
        }>;
    };
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

export function getBackgroundCheckColor(status: BackgroundCheckStatus): string {
    switch (status) {
        case BackgroundCheckStatus.PASSED:
            return 'green';
        case BackgroundCheckStatus.INVITED:
        case BackgroundCheckStatus.CLEARED:
        case BackgroundCheckStatus.PENDING:
            return 'orange';
        case BackgroundCheckStatus.FAILED:
        case BackgroundCheckStatus.INVITE_ERROR:
        case BackgroundCheckStatus.CANDIDATE_ERROR:
        case BackgroundCheckStatus.DOB_ERROR:
        default:
            return 'red';
    }
}

export function getBackgroundCheckMessage({status, invitationUrl}: BackgroundCheck): ReactNode {
    switch (status) {
        case BackgroundCheckStatus.PASSED:
            return 'Your background check has cleared! Thank you!';
        case BackgroundCheckStatus.FAILED:
            return (
                <>
                    Your Background Check or{' '}
                    <Link fontWeight="semibold" href="/volunteer/registration-for-minors/">
                        Minors Registration Form
                    </Link>{' '}
                    is not up-to-date. For more information on how to start a new background check, please contact:{' '}
                    <Link fontWeight="semibold" href="mailto:info@sdrefugeetutoring.com">
                        info@sdrefugeetutoring.com
                    </Link>
                </>
            );
        case BackgroundCheckStatus.CLEARED:
            return 'Your background check has cleared from the previous year and needs to be renewed.';
        case BackgroundCheckStatus.INVITED:
            return (
                <>
                    You have been invited to start a background check. Please click the link below to complete that
                    process on the Checkr website or to check your status: <br />
                    <br />
                    <Link href={invitationUrl} target="_blank" rel="noopener noreferrer" fontWeight="semibold">
                        {invitationUrl}
                    </Link>
                </>
            );
        case BackgroundCheckStatus.PENDING:
            return 'Please request a background check to begin the process.';
        case BackgroundCheckStatus.INVITE_ERROR:
            return 'There was an issue creating a Checkr Candidate. Please try again and contact the volunteer coordinator if the problem persists.';
        case BackgroundCheckStatus.CANDIDATE_ERROR:
            return 'There was an issue creating a Checkr Candidate. Please try again and contact the volunteer coordinator if the problem persists.';
        case BackgroundCheckStatus.DOB_ERROR:
            return 'Please update your profile to include your date of birth.';
        default:
            return <strong>Warning:</strong> + 'Unrecognized background check status, please contact an administrator.';
    }
}
