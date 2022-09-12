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
    HStack,
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

interface EventResponse {
    events: Array<{
        id: number;
        start_date: string;
        url: string;
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
    category: string | null;
    categorySlug: string | null;
    organizer: string | null;
    rsvpStatus: boolean | null;
}

const TrimesterOptions = window.sdrtVolunteerPortal.upcomingEvents.trimesters.map(({id, name, slug}) => (
    <option key={id} value={slug}>
        {name}
    </option>
));

const categoryFilters = window.sdrtVolunteerPortal.upcomingEvents.categories;

const categoryColorScheme = {
    'k-5th-grade': 'white',
    'middle-high-school': 'orange',
    'non-tutoring-event': 'cyan',
};

export default function UpcomingEvents() {
    const [trimester, setTrimester] = useState<string>(window.sdrtVolunteerPortal.upcomingEvents.trimesters[0].slug);
    const [activeCategories, setActiveCategories] = useState<Array<string>>([]);
    const [events, setEvents] = useState<Array<Event>>([]);
    const [loading, setLoading] = useState<boolean>(false);

    const setEventAttendance = useCallback((id: number, attending: boolean) => {
        setEvents(
            produce<Array<Event>>((draft) => {
                const event = draft.find((event) => event.id === id);
                event.rsvpStatus = attending;
            })
        );
    }, []);

    const toast = useToast({
        duration: 7500,
        position: 'bottom',
    });

    const rsvpForEvent = async (eventId: number, attending: boolean) => {
        const worked = rsvpToEvent(eventId, attending, toast);

        if (!worked) {
            return;
        }

        setEventAttendance(eventId, attending);
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
                    `tribe/events/v1/events?trimester=${trimester}&categories=${categories}`,
                    {method: 'GET'}
                );

                if (response.ok) {
                    const events: EventResponse = await response.json();

                    setEvents(
                        events.events.map((event) => {
                            return {
                                id: event.id,
                                date: new Date(event.start_date),
                                url: event.url,
                                category: event.categories[0] ? event.categories[0].name : null,
                                categorySlug: event.categories[0] ? event.categories[0].slug : null,
                                organizer: event.organizer[0] ? event.organizer[0].organizer : null,
                                rsvpStatus: event.rsvpStatus,
                            };
                        })
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
    }, [trimester, activeCategories]);

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
                These are the upcoming events which you may filter by trimester and event type. Review and RSVP if you
                are able to attend.
            </Text>
            <Alert status="info" variant="subtle" mb={16}>
                <AlertIcon />
                Only RSVP "no" to a session if that session is part of your regular weekly attendance
            </Alert>
            <Flex gap={8} justify="space-between" mb={6} as="form" role="search" aria-label="Upcoming Events">
                <Select w="auto" onChange={(event) => setTrimester(event.target.value)}>
                    {TrimesterOptions}
                </Select>
                <HStack gap={3}>
                    <Checkbox
                        onChange={handleCategoryChange}
                        value={categoryFilters.k5.slug}
                        colorScheme="gray"
                        sx={{marginBottom: 0}}
                    >
                        {categoryFilters.k5.name}
                    </Checkbox>
                    <Checkbox onChange={handleCategoryChange} value={categoryFilters.middle.slug} colorScheme="orange">
                        {categoryFilters.middle.name}
                    </Checkbox>
                    <Checkbox onChange={handleCategoryChange} value={categoryFilters.other.slug} colorScheme="teal">
                        {categoryFilters.other.name}
                    </Checkbox>
                </HStack>
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
                            {events.map(({id, date, category, categorySlug, organizer, url, rsvpStatus}) => (
                                <EventRow
                                    key={id}
                                    date={date}
                                    category={category}
                                    type="Online Tutoring:"
                                    organizer={organizer}
                                    link={url}
                                    colorScheme={categoryColorScheme[categorySlug]}
                                    rsvp={rsvpStatus}
                                    handleRsvp={(attending) => rsvpForEvent(id, attending)}
                                />
                            ))}
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

    handleRsvp(attending: boolean): void;
}

function EventRow({colorScheme, date, category, type, organizer, link, rsvp, handleRsvp}: EventRowProps) {
    return (
        <Tr bg={`${colorScheme}.50`}>
            <Td py={10}>
                <VStack>
                    <Text fontSize="2xl" fontWeight="bolder" color="cyan.700">
                        {format(date, 'LLLL d')}
                    </Text>
                    <Text fontSize="md" fontWeight={400} color="gray.500">
                        {format(date, 'EEEE - h:mmaaa')}
                    </Text>
                </VStack>
            </Td>
            <Td py={10}>
                <VStack>
                    <Text fontSize="md" color="neutral.500">
                        {type}
                    </Text>
                    <Text fontSize="md" dangerouslySetInnerHTML={{__html: category}} />
                </VStack>
            </Td>
            <Td py={10}>
                <Center>
                    <Button as="a" variant="dark-outline" colorScheme="cyan" href={link} target="_blank">
                        More Info
                    </Button>
                </Center>
            </Td>
            <Td py={10}>
                <Center>
                    <ButtonGroup isAttached boxShadow="0px 0.1rem 0.3rem rgba(0, 0, 0, 0.4)" borderRadius="0.4rem">
                        <Button
                            onClick={() => rsvp !== true && handleRsvp(true)}
                            isActive={rsvp === true}
                            variant="group"
                        >
                            RSVP YES
                        </Button>
                        <Button
                            onClick={() => rsvp !== false && handleRsvp(false)}
                            isActive={rsvp === false}
                            variant="group"
                        >
                            RSVP NO
                        </Button>
                    </ButtonGroup>
                </Center>
            </Td>
        </Tr>
    );
}
