import {ReactNode, useState} from 'react';
import {
    Box,
    Button,
    Heading,
    HStack,
    Link,
    List,
    ListIcon,
    ListItem,
    Tag,
    Text,
    useToast,
    VStack,
} from '@chakra-ui/react';
import {CheckIcon} from '@chakra-ui/icons';

import Section from '../../components/Section';
import Card from '../../components/Card';
import {fetchSdrtApi} from '../../support/fetchRestApi';
import {
    Requirements,
    BackgroundCheckStatus,
    getBackgroundCheckMessage,
    getBackgroundCheckColor,
} from '../../types/Requirements';
import {useRequirementsContext} from '../../stores/RequirementsStore';

export default function Requirements({
    requirements: {orientation, codeOfConduct, volunteerRelease},
}: {
    requirements: Requirements;
}) {
    const {backgroundCheck, setBackgroundCheck} = useRequirementsContext();
    const [backgroundCheckLoading, setBackgroundCheckLoading] = useState<boolean>(false);
    const toast = useToast({
        duration: 5000,
        isClosable: true,
        position: 'bottom',
    });

    async function requestBackgroundCheck() {
        setBackgroundCheckLoading(true);
        const response = await fetchSdrtApi('background-check/');
        const {status, inviteUrl} = await response.json();

        setBackgroundCheck({status, invitationUrl: inviteUrl});
        setBackgroundCheckLoading(false);
    }

    const backgroundCheckColor = getBackgroundCheckColor(backgroundCheck.status);
    const backgroundCheckCompleted = [BackgroundCheckStatus.PASSED, BackgroundCheckStatus.INVITED].includes(
        backgroundCheck.status
    );

    return (
        <Section heading="Volunteer Requirement Status">
            <VStack alignItems="flex-start" gap={4} mb={10}>
                <Text>
                    We take the safety and health of our students very seriously. At the same time, we are extremely
                    fortunate to have such a generous group of volunteers eager and willing to tutor. It's our goal to
                    ensure the safety of our students and also make volunteering have as low a barrier to entry as
                    possible.
                </Text>
                <Text>To volunteer, we currently require the following:</Text>
                <List>
                    <CheckListItem>Acceptance of our Code of Conduct</CheckListItem>
                    <CheckListItem>Acceptance of our Virtual Tutoring Code of Conduct</CheckListItem>
                    <CheckListItem>Attendance to a one-hour orientation session</CheckListItem>
                    <CheckListItem>Pass an online background check</CheckListItem>
                </List>
                <Text>
                    Each of those items and your current status are listed below. For any questions about your status
                    below, please feel free to reach out to our volunteer coordinator via our{' '}
                    <Link href="/contact" color="cyan.500">
                        Contact Form.
                    </Link>
                </Text>
            </VStack>
            <VStack gap={6}>
                <RequirementCard
                    header="Background Check"
                    completed={backgroundCheckCompleted}
                    baseColor={backgroundCheckColor}
                    alwaysShowChildren
                >
                    <Text>
                        <strong>REQUIREMENT: </strong> To volunteer, you must apply for and clear a background check via
                        Checkr — our online background check partner.
                    </Text>
                    <Box bg={`${backgroundCheckColor}.200`} w="100%" p={3}>
                        <Text>{getBackgroundCheckMessage(backgroundCheck)}</Text>
                    </Box>
                    {!backgroundCheckCompleted && (
                        <Button
                            variant={backgroundCheckColor}
                            onClick={requestBackgroundCheck}
                            isLoading={backgroundCheckLoading}
                        >
                            Complete Background Check
                        </Button>
                    )}
                </RequirementCard>
                <RequirementCard header="Orientation Status" completed={orientation.completed}>
                    <Text>
                        <strong>REQUIREMENT: </strong> Attendance of required yearly Refresher or Orientation
                        session(s). The specific requirements will be posted on the Volunteer registration page. And
                        will always be relayed thru other avenues.
                    </Text>
                </RequirementCard>
                <RequirementCard header="Code of Conduct Status" completed={codeOfConduct.completed}>
                    <Text>
                        <strong>REQUIREMENT: </strong>To volunteer, you must agree to our Code of Conduct.
                    </Text>
                    <Button as="a" href="/volunteer/code-of-conduct/" target="_blank" variant="red">
                        View & Submit Code of conduct
                    </Button>
                </RequirementCard>
                <RequirementCard header="Volunteer Release Status" completed={volunteerRelease.completed}>
                    <Text>
                        <strong>REQUIREMENT: </strong>To volunteer, you must agree to our Volunteer Release & Waiver of
                        Liability.
                    </Text>
                    <Button as="a" href="/volunteer/waiver/" target="_blank" variant="red">
                        View & Submit Volunteer Release
                    </Button>
                </RequirementCard>
            </VStack>
        </Section>
    );
}

function RequirementCard({
    children,
    header,
    completed,
    baseColor = null,
    alwaysShowChildren = false,
}: {
    header: string;
    children: ReactNode;
    completed: boolean;
    baseColor?: string | null;
    alwaysShowChildren?: boolean;
}) {
    const color: string = baseColor || (completed ? 'green' : 'red');

    return (
        <Card bg={`${color}.100`}>
            <VStack gap={4} alignItems="flex-start">
                <HStack justify="space-between" w="100%">
                    <Heading as="h2" size="lg">
                        {header}
                    </Heading>
                    <HStack>
                        <Text as="span" fontWeight={700} fontSize="sm" textTransform="uppercase">
                            Status:
                        </Text>
                        <Tag
                            borderRadius="full"
                            px={4}
                            bg={`${color}.500`}
                            color="white"
                            fontWeight={700}
                            textTransform="uppercase"
                            letterSpacing={2}
                        >
                            {completed ? 'Completed' : 'Incomplete'}
                        </Tag>
                    </HStack>
                </HStack>
                {(!completed || alwaysShowChildren) && children}
            </VStack>
        </Card>
    );
}

function CheckListItem({children}: {children: ReactNode}) {
    return (
        <ListItem>
            <ListIcon as={CheckIcon} color="cyan.500" />
            {children}
        </ListItem>
    );
}
