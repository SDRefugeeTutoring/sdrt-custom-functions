import Section from '../../components/Section';
import {
    Table,
    TableContainer,
    Tbody,
    Text,
    Tr,
    Td,
    VStack,
    Button,
    ButtonGroup,
    Center,
    Flex,
    Select,
    Checkbox,
    useToast,
    Spinner,
    Alert,
    Link,
    AlertIcon,
} from '@chakra-ui/react';
import {NavLink as RouterNavLink} from 'react-router-dom';
import {format} from 'date-fns';
import React, {useEffect, useState, useCallback} from 'react';
import {fetchRestApi} from '../../support/fetchRestApi';
import rsvpToEvent from '../../support/rsvp';
import produce from 'immer';

enum EventType {
    TutoringInPerson = 'tutoring-in-person',
    TutoringOnline = 'tutoring-online',
    OrientationInPerson = 'orientation-in-person',
    OrientationOnline = 'orientation-online',
    RefresherInPerson = 'refresher-in-person',
    RefresherOnline = 'refresher-online',
    EventInPerson = 'event-in-person',
    EventOnline = 'event-online',
}

function getEventTypeLabel(eventType: EventType): string {
    switch (eventType) {
        case EventType.TutoringInPerson:
            return 'In-Person Tutoring';
        case EventType.TutoringOnline:
            return 'Online Tutoring';
        case EventType.OrientationInPerson:
            return 'In-Person Orientation';
        case EventType.OrientationOnline:
            return 'Online Orientation';
        case EventType.RefresherInPerson:
            return 'In-Person Refresher';
        case EventType.RefresherOnline:
            return 'Online Refresher';
        case EventType.EventInPerson:
            return 'In-Person Event';
        case EventType.EventOnline:
            return 'Online Event';
        default:
            return 'Event';
    }
}

interface EventResponse {
    events: Array<{
        id: number;
        start_date: string;
        url: string;
        type: EventType;
        categories: Array<{
            name: string;
            slug: string;
        }>;
        organizer: Array<{
            organizer: string;
        }>;
        rsvpStatus: boolean | null;
    }>;
    total: number;
    total_pages: number;
}

interface Event {
    id: number;
    date: Date;
    url: string;

    type: EventType;
    category: string | null;
    categorySlug: string | null;
    organizer: string | null;
    rsvpStatus: boolean | null;
    atCapacity: boolean;
}

const categoryFilters = window.sdrtVolunteerPortal.upcomingEvents.categories;

const categoryColorScheme = {
    'k-5th-grade': 'white',
    'middle-high-school': 'orange',
    'non-tutoring-event': 'cyan',
};

export default function UpcomingEvents() {
    const [activeCategories, setActiveCategories] = useState<Array<string>>([]);
    const [events, setEvents] = useState<Array<Event>>([]);
    const [loading, setLoading] = useState<boolean>(false);

    const setEventAttendance = useCallback((id: number, atCapacity: boolean, attending: boolean) => {
        setEvents(
            produce<Array<Event>>((draft) => {
                const event = draft.find((event) => event.id === id);
                event.rsvpStatus = attending;
                event.atCapacity = atCapacity;
            })
        );
    }, []);

    const toast = useToast({
        duration: 7500,
        position: 'bottom',
    });

    const rsvpForEvent = async (eventId: number, attending: boolean) => {
        const rsvpData = await rsvpToEvent(eventId, attending, toast);

        if (rsvpData === true) {
            setEventAttendance(eventId, false, attending);
        } else if (rsvpData === 'atCapacity') {
            setEventAttendance(eventId, true, null);
        }
    };

    function handleCategoryChange(event: React.ChangeEvent<HTMLInputElement>) {
        const include = event.target.checked;
        const slug = event.target.value;

        setActiveCategories((prevState) => {
            if (include) {
                return [...prevState, slug];
            } else {
                return prevState.filter((category) => category !== slug);
            }
        });
    }

    useEffect(() => {
        if (!window.sdrtVolunteerPortal.requirements.allPassed) {
            return;
        }

        const fetchEvents = async () => {
            try {
                setLoading(true);

                const categories =
                    activeCategories.length > 0
                        ? activeCategories.join(',')
                        : [categoryFilters.k5.slug, categoryFilters.middle.slug, categoryFilters.other.slug].join(',');

                const response = await fetchRestApi(
                    `tribe/events/v1/events?categories=${categories}&per_page=100&status=publish`,
                    {method: 'GET'}
                );

                if (response.ok) {
                    const events: EventResponse = await response.json();
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    setEvents(
                        events.events
                            .map((event) => ({
                                id: event.id,
                                date: new Date(event.start_date),
                                type: event.type,
                                url: event.url,
                                category: event.categories[0] ? event.categories[0].name : null,
                                categorySlug: event.categories[0] ? event.categories[0].slug : null,
                                organizer: event.organizer[0] ? event.organizer[0].organizer : null,
                                rsvpStatus: event.rsvpStatus,
                                atCapacity: false,
                            }))
                            .filter((event) => event.date > today)
                    );
                } else {
                    toast({
                        title: 'Error',
                        duration: 10000,
                        status: 'error',
                        description:
                            'There was a problem loading the events. Please try refreshing the page. If the problem persists, please contact the Volunteer Coordinator.',
                    });
                }
            } catch (error) {
                toast({
                    title: 'Error',
                    duration: 10000,
                    status: 'error',
                    description:
                        'There was a problem loading the events. Please try refreshing the page. If the problem persists, please contact the Volunteer Coordinator.',
                });
            }
            setLoading(false);
        };

        fetchEvents();
    }, [activeCategories]);

    if (!window.sdrtVolunteerPortal.requirements.allPassed) {
        return (
            <Section heading="Upcoming Events">
                <Alert status="warning">
                    <AlertIcon />
                    <div>
                        In order to RSVP to events you must pass{' '}
                        <Link as={RouterNavLink} to="/requirements" textColor="cyan.700">
                            all requirements.
                        </Link>
                    </div>
                </Alert>
            </Section>
        );
    }

    return (
        <Section heading="Upcoming Events">
            <Text mb={4}>
                These are the upcoming events which you may filter by event type. Review and RSVP if you
                are able to attend.
            </Text>
            <Alert status="info" variant="subtle" mb={16}>
                <AlertIcon />
                Only RSVP "no" to a session if that session is part of your regular weekly attendance
            </Alert>
            <Flex
                gap={8}
                direction={{base: 'column', md: 'row'}}
                justify="space-between"
                mb={6}
                as="form"
                role="search"
                aria-label="Upcoming Events"
            >
                <Flex gap={3} direction={{base: 'column', md: 'row'}}>
                    <Checkbox onChange={handleCategoryChange} value={categoryFilters.k5.slug} colorScheme="gray" mb={0}>
                        {categoryFilters.k5.name}
                    </Checkbox>
                    <Checkbox
                        onChange={handleCategoryChange}
                        value={categoryFilters.middle.slug}
                        colorScheme="orange"
                        mb={0}
                    >
                        {categoryFilters.middle.name}
                    </Checkbox>
                    <Checkbox
                        onChange={handleCategoryChange}
                        value={categoryFilters.other.slug}
                        colorScheme="teal"
                        mb={0}
                    >
                        {categoryFilters.other.name}
                    </Checkbox>
                </Flex>
            </Flex>
            <TableContainer boxShadow="0px 0.15rem 0.3rem rgb(0 0 0 / 30%)" borderRadius="0.75rem">
                {loading ? (
                    <Center py={10}>
                        <Spinner size="xl" thickness="4px" color="cyan.600" />
                    </Center>
                ) : events.length <= 0 ? (
                    <Center py={10}>
                        <Text>No events found.</Text>
                    </Center>
                ) : (
                    <Table variant="simple">
                        <Tbody>
                            {events.map(
                                ({id, date, type, category, categorySlug, organizer, url, rsvpStatus, atCapacity}) => (
                                    <EventRow
                                        key={id}
                                        date={date}
                                        category={category}
                                        // type="Online Tutoring:"
                                        type={`${getEventTypeLabel(type)}:`}
                                        organizer={organizer}
                                        link={url}
                                        colorScheme={categoryColorScheme[categorySlug]}
                                        rsvp={rsvpStatus}
                                        atCapacity={atCapacity}
                                        handleRsvp={(attending) => rsvpForEvent(id, attending)}
                                    />
                                )
                            )}
                        </Tbody>
                    </Table>
                )}
            </TableContainer>
        </Section>
    );
}

interface EventRowProps {
    colorScheme: string;
    date: Date;
    category: string;
    type: string;
    organizer: string;
    link: string;
    rsvp?: boolean;
    atCapacity: boolean;

    handleRsvp(attending: boolean): void;
}

function EventRow({colorScheme, date, category, type, atCapacity, link, rsvp, handleRsvp}: EventRowProps) {
    return (
        <Tr bg={`${colorScheme}.50`}>
            <Td py={7}>
                <VStack>
                    <Text fontSize="2xl" fontWeight="bolder" color="cyan.700">
                        {format(date, 'LLLL d')}
                    </Text>
                    <Text fontSize="md" fontWeight={400} color="gray.500">
                        {format(date, 'EEEE - h:mmaaa')}
                    </Text>
                </VStack>
            </Td>
            <Td py={7}>
                <VStack>
                    <Text fontSize="md" color="neutral.500">
                        {type}
                    </Text>
                    <Text fontSize="md" dangerouslySetInnerHTML={{__html: category}} />
                </VStack>
            </Td>
            <Td py={7}>
                <Center>
                    <Button as="a" variant="dark-outline" colorScheme="cyan" href={link} target="_blank">
                        More Info
                    </Button>
                </Center>
            </Td>
            <Td py={7}>
                <Center>
                    <ButtonGroup isAttached boxShadow="0px 0.1rem 0.3rem rgba(0, 0, 0, 0.4)" borderRadius="0.4rem">
                        <Button
                            onClick={() => rsvp !== true && handleRsvp(true)}
                            isActive={rsvp === true}
                            variant="group"
                            disabled={rsvp !== true && atCapacity}
                            width={28}
                        >
                            {rsvp !== true && atCapacity ? 'AT LIMIT' : 'RSVP YES'}
                        </Button>
                        <Button
                            onClick={() => rsvp !== false && handleRsvp(false)}
                            isActive={rsvp === false}
                            variant="group"
                            width={28}
                        >
                            RSVP NO
                        </Button>
                    </ButtonGroup>
                </Center>
            </Td>
        </Tr>
    );
}
