import {useRequirementsContext} from '../../../stores/RequirementsStore';
import {useState} from 'react';
import {fetchSdrtApi} from '../../../support/fetchRestApi';
import {BackgroundCheckStatus, getBackgroundCheckColor, getBackgroundCheckMessage} from '../../../types/Requirements';
import RequirementCard from './RequirementCard';
import {Box, Button, Text} from '@chakra-ui/react';

export default function BackgroundCheckCard() {
    const {backgroundCheck, setBackgroundCheck} = useRequirementsContext();
    const [backgroundCheckLoading, setBackgroundCheckLoading] = useState<boolean>(false);

    async function requestBackgroundCheck() {
        setBackgroundCheckLoading(true);
        const response = await fetchSdrtApi('requirements/background-check/');
        const {status, inviteUrl} = await response.json();

        setBackgroundCheck({status, invitationUrl: inviteUrl});
        setBackgroundCheckLoading(false);
    }

    const backgroundCheckColor = getBackgroundCheckColor(backgroundCheck.status);
    const backgroundCheckCompleted = BackgroundCheckStatus.PASSED === backgroundCheck.status;
    const showBackgroundCheckButton = [
        BackgroundCheckStatus.CLEARED,
        BackgroundCheckStatus.PENDING,
        BackgroundCheckStatus.DOB_ERROR,
        BackgroundCheckStatus.CANDIDATE_ERROR,
        BackgroundCheckStatus.INVITE_ERROR,
    ].includes(backgroundCheck.status);

    return (
        <RequirementCard
            header="Background Check"
            completed={backgroundCheckCompleted}
            baseColor={backgroundCheckColor}
            alwaysShowChildren
        >
            <Text>
                <strong>REQUIREMENT: </strong> To volunteer, you must apply for and clear a background check via Checkr
                — our online background check partner.
            </Text>
            <Box bg={`${backgroundCheckColor}.200`} w="100%" p={3}>
                <Text>{getBackgroundCheckMessage(backgroundCheck)}</Text>
            </Box>
            {showBackgroundCheckButton && (
                <Button
                    variant={backgroundCheckColor}
                    onClick={requestBackgroundCheck}
                    isLoading={backgroundCheckLoading}
                >
                    Complete Background Check
                </Button>
            )}
        </RequirementCard>
    );
}
