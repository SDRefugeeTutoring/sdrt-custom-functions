import {FC, ReactNode, useState} from 'react';
import {Box, Button, Heading, HStack, Link, List, ListIcon, ListItem, Tag, Text, VStack} from '@chakra-ui/react';
import {CheckIcon} from '@chakra-ui/icons';

import Section from '../../components/Section';
import Card from '../../components/Card';
import {fetchSdrtApi} from '../../support/fetchRestApi';
import {Requirements, BackgroundCheckStatus, BackgroundCheck} from '../../types/Requirements';

export default function Requirements({
    requirements: {backgroundCheck, orientation, codeOfConduct, volunteerRelease},
}: {
    requirements: Requirements;
}) {
    const [checkMessage, setCheckMessage] = useState<string | null>(null);
    const [backgroundCheckCompleted, setBackgroundCheckCompleted] = useState<boolean>(
        backgroundCheck.status === BackgroundCheckStatus.PASSED
    );

    async function requestBackgroundCheck() {
        const response = await fetchSdrtApi('background-check');
        const {message} = await response.json();

        if (response.ok) {
            setBackgroundCheckCompleted(true);
            setCheckMessage(message);
        } else {
            setBackgroundCheckCompleted(false);
            setCheckMessage(message);
        }
    }

    let backgroundCheckMessage;
    switch (backgroundCheck.status) {
        case BackgroundCheckStatus.PENDING:
            backgroundCheckMessage = 'Please request a background check to begin the process.';
            break;
        case BackgroundCheckStatus.INVITED:
            backgroundCheckMessage = (
                <>
                    You have been invited to take a background check and your status is still pending. You can{' '}
                    <Link href={backgroundCheck.inviteUrl} target="_blank" rel="noopener noreferrer">
                        check on the status
                    </Link>{' '}
                    on the Checkr website.
                </>
            );
            break;
        case BackgroundCheckStatus.FAILED:
            backgroundCheckMessage =
                'Your background check did not clear. You are not allowed to tutor with us at this time. If you have questions about this at all, please contact boardmembers@sdrefugeetutoring.com';
            break;
        case BackgroundCheckStatus.CLEARED:
            backgroundCheckMessage =
                'Your background check has cleared from the previous year and needs to be renewed.';
            break;
        default:
            backgroundCheckMessage =
                <strong>Warning:</strong> + 'Unrecognized background check status, please contact an administrator.';
    }

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
                <RequirementCard header="Background Check" completed={backgroundCheckCompleted}>
                    <Text>
                        <strong>REQUIREMENT: </strong> To volunteer, you must apply for and clear a background check via
                        Checkr — our online background check partner.
                    </Text>
                    {backgroundCheckMessage && (
                        <Box bg={backgroundCheckCompleted ? 'green.200' : 'red.200'} w="100%" p={3}>
                            <Text>{backgroundCheckMessage}</Text>
                        </Box>
                    )}
                    <Button variant="red">Complete Background Check</Button>
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

function RequirementCard({children, header, completed}: {header: string; children: ReactNode; completed: boolean}) {
    return (
        <Card bg={completed ? 'green.100' : 'red.100'}>
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
                            bg={completed ? 'green.500' : 'red.500'}
                            color="white"
                            fontWeight={700}
                            textTransform="uppercase"
                            letterSpacing={2}
                        >
                            {completed ? 'Completed' : 'Incomplete'}
                        </Tag>
                    </HStack>
                </HStack>
                {!completed && children}
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
