import {Container, SimpleGrid, VStack} from '@chakra-ui/react';
import Card from '../../components/Card';
import Message, {getMessageProps, MessageUrgency} from './components/Message';
import NextEvent, {NextEventProps} from './components/NextEvent';

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

export default function Index({message, nextEvent}: DashboardProps) {
    return (
        <Container bg="white" centerContent={false} px={10} py={8} m={0} maxWidth="100%">
            <VStack spacing={8}>
                {message && <Message {...getMessageProps(message.text, message.urgency)} />}
                <NextEvent {...nextEvent} />
                <SimpleGrid minChildWidth="20rem" spacing={5} width="100%">
                    <Card>Card 1</Card>
                    <Card>Card 2</Card>
                    <Card>Card 3</Card>
                </SimpleGrid>
            </VStack>
        </Container>
    );
}
