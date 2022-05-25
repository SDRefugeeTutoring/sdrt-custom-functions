import {Container, SimpleGrid, VStack, Text, Link, Heading, Center} from '@chakra-ui/react';
import {Link as RouterLink} from 'react-router-dom';
import {format, formatDistanceToNow} from 'date-fns';

import Card from '../../components/Card';
import Message, {getMessageProps, MessageUrgency} from './components/Message';
import NextEvent, {NextEventProps} from './components/NextEvent';
import {useUserContext} from '../../store';

export interface DashboardProps {
    message: {
        text: string;
        urgency: MessageUrgency;
    };
    nextEvent: NextEventProps;
    volunteerStats: {
        startDate: Date;
        eventsAttended: number;
        totalHours: number;
        currentTrimesterAttendanceRate: number;
        previousTrimesterAttendanceRate: number;
    };
}

export function dashboardPropsFromWindow(): DashboardProps {
    const dashboard = window.sdrtVolunteerPortal.dashboard;
    return {
        ...dashboard,
        nextEvent: {
            ...dashboard.nextEvent,
            date: new Date(dashboard.nextEvent.date),
        },
        volunteerStats: {
            ...dashboard.volunteerStats,
            startDate: new Date(dashboard.volunteerStats.startDate),
        },
    };
}

export default function Dashboard({message, nextEvent, volunteerStats}: DashboardProps) {
    const {user} = useUserContext();

    return (
        <Container>
            <VStack spacing={10}>
                {message && <Message {...getMessageProps(message.text, message.urgency)} />}
                <NextEvent {...nextEvent} />
                <SimpleGrid minChildWidth="16rem" spacing={5} width="100%" autoRows="1fr">
                    <Card>
                        <VStack alignItems="flex-start" spacing={7}>
                            <LabelAndText label="Name" text={`${user.firstName} ${user.lastName}`} />
                            <LabelAndText label="Email" text={user.email} />
                            <Link as={RouterLink} to="/profile" color="cyan.600" fontSize="sm">
                                Update profile information
                            </Link>
                        </VStack>
                    </Card>
                    <Card>
                        <Center h="100%">
                            <VStack>
                                <Text as="strong" fontSize="2xl" color="gray.500">
                                    Volunteer Start Date
                                </Text>
                                <Text fontSize="3xl" fontWeight="bolder" color="cyan.600">
                                    {format(volunteerStats.startDate, 'MM/dd/yyyy')}
                                </Text>
                                <Text color="gray.500" fontWeight={400}>
                                    {`(${formatDistanceToNow(volunteerStats.startDate)})`}
                                </Text>
                            </VStack>
                        </Center>
                    </Card>
                    <StatCard label="Total Events Attended" text={volunteerStats.eventsAttended} />
                    <StatCard label="Total Hours Tutored" text={volunteerStats.totalHours} />
                    <StatCard
                        label="Attendance Rate"
                        text={`${volunteerStats.currentTrimesterAttendanceRate * 100}%`}
                        subText="(CURRENT TRIMESTER)"
                    />
                    <StatCard
                        label="Attendance Rate"
                        text={`${volunteerStats.previousTrimesterAttendanceRate * 100}%`}
                        subText="(PREVIOUS TRIMESTER)"
                    />
                </SimpleGrid>
            </VStack>
        </Container>
    );
}

function LabelAndText({label, text}: {label: string; text: string}) {
    return (
        <VStack spacing={1} alignItems="flex-start">
            <Text as="strong" fontSize="sm" textTransform="uppercase" color="gray.500">
                {label}
            </Text>
            <Text fontWeight="bolder">{text}</Text>
        </VStack>
    );
}

function StatCard({label, text, subText = ' '}: {label: string; text: string | number; subText?: string}) {
    return (
        <Card py={10}>
            <VStack>
                <Text as="strong" fontSize="2xl" color="gray.500">
                    {label}
                </Text>
                <Text fontSize="5xl" fontWeight="bolder" color="cyan.800">
                    {text}
                </Text>
                {subText && (
                    <Text color="gray.500" fontWeight={400}>
                        {subText}
                    </Text>
                )}
            </VStack>
        </Card>
    );
}
