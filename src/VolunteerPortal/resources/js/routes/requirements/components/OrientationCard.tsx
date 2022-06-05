import RequirementCard from './RequirementCard';
import {
    Accordion,
    AccordionButton,
    AccordionItem,
    AccordionPanel,
    AlertStatus,
    Button,
    Divider,
    Grid,
    HStack,
    Link,
    Text,
    useToast,
    VStack,
} from '@chakra-ui/react';
import {ChevronRightIcon} from '@chakra-ui/icons';
import {format, parse} from 'date-fns';
import {Fragment} from 'react';
import {fetchSdrtApi} from '../../../support/fetchRestApi';

export default function OrientationCard({orientation}) {
    const toast = useToast({
        duration: 7500,
        position: 'bottom',
    });

    async function requestRSVP(eventId: number) {
        const response = await fetchSdrtApi('requirements/orientation-rsvp', {
            body: {eventId},
        });

        if (response.ok) {
            toast({
                title: "You have RSVP'd",
                description: "Thank you for your RSVP! We'll see you at the orientation!",
                status: 'success',
            });
        } else {
            const error = await response.json();
            const reason = error.reason ?? null;
            let description: string,
                status: AlertStatus = 'error';

            if (reason === 'rsvp_already_exists') {
                status = 'info';
                description = "You have already RSVP'd to this event. No need to RSVP again.";
            } else if (reason === 'event_not_orientation') {
                status = 'warning';
                description =
                    'The event must be an orientation. If you believe this is an error, please contact the volunteer coordinator.';
            } else {
                description =
                    'A problem occurred when creating your RSVP. Please refresh, try again, and contact the volunteer coordinator if the problem persists.';
            }

            toast({
                title: 'Unable to RSVP',
                description,
                status,
            });
        }
    }

    return (
        <RequirementCard header="Orientation Status" completed={orientation.completed}>
            <Text>
                <strong>REQUIREMENT: </strong> Attendance of required yearly Refresher or Orientation session(s). The
                specific requirements will be posted on the Volunteer registration page and will always be relayed
                through other avenues.
            </Text>
            <Accordion allowToggle width="100%">
                <AccordionItem
                    width="100%"
                    sx={{
                        '&:last-of-type': {border: 0},
                        '& .chakra-collapse': {
                            mt: '-1rem',
                            pt: '1rem',
                            boxShadow: '0px 5px 11px 0px rgba(0, 0, 0, 0.25)',
                            borderBottomRadius: '1rem',
                        },
                    }}
                >
                    {({isExpanded}) => (
                        <>
                            <AccordionButton
                                as={Button}
                                variant="red"
                                width="100%"
                                _hover={{bg: 'red.500'}}
                                zIndex={100}
                            >
                                <HStack justify="space-between" width="100%">
                                    <div>View Upcoming Orientations</div>
                                    <ChevronRightIcon
                                        boxSize="2rem"
                                        transform={isExpanded ? 'rotate(90deg)' : ''}
                                        transition="transform 0.5s"
                                    />
                                </HStack>
                            </AccordionButton>
                            <AccordionPanel bg="white" px={10} mt="-1rem" pt="1rem">
                                <Grid templateColumns="1fr 1fr 1fr auto auto" gap={5} alignItems="center">
                                    {orientation.upcomingEvents.map(({id, address, organizer, date, link}, index) => {
                                        const eventDate = parse(date, 'yyyy-MM-dd HH:mm:ss', new Date());
                                        return (
                                            <Fragment key={date}>
                                                {index > 0 && (
                                                    <Divider
                                                        borderWidth="0.07rem"
                                                        borderColor="red.100"
                                                        gridColumn="1 / -1"
                                                    />
                                                )}
                                                <VStack>
                                                    <Text fontSize="3xl" fontWeight="bolder" color="red.500">
                                                        {format(eventDate, 'LLLL d')}
                                                    </Text>
                                                    <Text
                                                        color="gray.500"
                                                        fontWeight={400}
                                                        sx={{marginTop: '0 !important'}}
                                                    >
                                                        {format(eventDate, 'EEEE - h:mmaaa')}
                                                    </Text>
                                                </VStack>
                                                <VStack alignSelf="start" mt="2rem">
                                                    <Text color="neutral.400">Address:</Text>
                                                    <Link
                                                        sx={{marginTop: '0 !important'}}
                                                        color="cyan.500"
                                                        fontSize="sm"
                                                        href={address.mapLink}
                                                        target="_blank"
                                                    >
                                                        {address.street}
                                                        <br />
                                                        {address.city}, {address.state} {address.zipCode}
                                                    </Link>
                                                </VStack>
                                                <VStack alignSelf="start" mt="2rem">
                                                    <Text color="neutral.400">Organizer:</Text>
                                                    <Text sx={{marginTop: '0 !important'}}>{organizer}</Text>
                                                </VStack>
                                                <Button as="a" href={link} variant="red" target="_blank">
                                                    More Info
                                                </Button>
                                                <Button onClick={() => requestRSVP(id)}>RSVP</Button>
                                            </Fragment>
                                        );
                                    })}
                                </Grid>
                            </AccordionPanel>
                        </>
                    )}
                </AccordionItem>
            </Accordion>
        </RequirementCard>
    );
}
