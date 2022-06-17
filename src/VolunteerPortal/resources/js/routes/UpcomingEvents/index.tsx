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
} from '@chakra-ui/react';
import {format} from 'date-fns';
import {useEffect, useState} from 'react';
import {fetchRestApi} from '../../support/fetchRestApi';

interface EventResponse {
    id: number;
    date: string;
    url: string;
    categories: Array<{
        name: string;
    }>;
    organizer: Array<{
        organizer: string;
    }>;
    rsvpStatus: boolean | null;
}

interface Event {
    id: number;
    date: Date;
    url: string;
    category: string;
    organizer: string;
    rsvpStatus: boolean | null;
}

export default function UpcomingEvents() {
    const [trimester, setTrimester] = useState<string>(null);
    const [activeCategory, setActiveCategory] = useState<string>(null);
    const [events, setEvents] = useState<Array<Event>>([]);

    useEffect(() => {
        const fetchEvents = async () => {
            try {
                const response = await fetchRestApi('events/v1/events', {method: 'GET'});

                if (response.ok) {
                }
            } catch (error) {
                console.error(error);
            }
        };

        fetchEvents();
    }, [trimester, activeCategory]);

    return (
        <Section heading="Upcoming Events">
            <Text mb={16}>
                These are the upcoming events which you may be filter by trimester and event type. Please review the
                events and RSVP as to whether you are able to attend.
            </Text>
            <Flex gap={8} justify="space-between" mb={6}>
                <Select w="auto">
                    <option>Q2 Trimester 2022</option>
                    <option>Q3 Trimester 2022</option>
                    <option>Q4 Trimester 2022</option>
                </Select>
                <HStack gap={3}>
                    <Checkbox sx={{marginBottom: 0}}>K-5th Grade</Checkbox>
                    <Checkbox colorScheme="orange">Middle & High School</Checkbox>
                    <Checkbox colorScheme="teal">Other</Checkbox>
                </HStack>
            </Flex>
            <TableContainer boxShadow="0px 0.15rem 0.3rem rgb(0 0 0 / 30%)" borderRadius="0.75rem">
                <Table variant="simple">
                    <Tbody>
                        <EventRow
                            date={new Date()}
                            category="Middle & High School"
                            type="Online Tutoring:"
                            organizer="John Stamos"
                            link="https://www.google.com"
                            colorScheme="white"
                        />
                        <EventRow
                            date={new Date()}
                            category="Middle & High School"
                            type="Online Tutoring:"
                            organizer="John Stamos"
                            link="https://www.google.com"
                            colorScheme="cyan"
                        />
                        <EventRow
                            date={new Date()}
                            category="Middle & High School"
                            type="Online Tutoring:"
                            organizer="John Stamos"
                            link="https://www.google.com"
                            rsvp={true}
                            colorScheme="orange"
                        />
                        <EventRow
                            date={new Date()}
                            category="Middle & High School"
                            type="Online Tutoring:"
                            organizer="John Stamos"
                            link="https://www.google.com"
                            colorScheme="white"
                        />
                        <EventRow
                            date={new Date()}
                            category="Middle & High School"
                            type="Online Tutoring:"
                            organizer="John Stamos"
                            link="https://www.google.com"
                            rsvp={false}
                            colorScheme="cyan"
                        />
                    </Tbody>
                </Table>
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
}

function EventRow({colorScheme, date, category, type, organizer, link, rsvp}: EventRowProps) {
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
                    <Text fontSize="md">{category}</Text>
                </VStack>
            </Td>
            <Td py={10}>
                <VStack>
                    <Text fontSize="md" color="neutral.500">
                        Organizer:
                    </Text>
                    <Text fontSize="md">{organizer}</Text>
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
                        <Button isActive={rsvp === true} variant="group">
                            RSVP YES
                        </Button>
                        <Button isActive={rsvp === false} variant="group">
                            RSVP NO
                        </Button>
                    </ButtonGroup>
                </Center>
            </Td>
        </Tr>
    );
}
