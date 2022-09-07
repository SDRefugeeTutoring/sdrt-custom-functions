import RequirementCard from './RequirementCard';
import {
    Accordion,
    AccordionButton,
    AccordionItem,
    AccordionPanel,
    Button,
    Center,
    Divider,
    Flex,
    Grid,
    HStack,
    Text,
    useToast,
} from '@chakra-ui/react';
import {ChevronRightIcon} from '@chakra-ui/icons';
import {format, parse} from 'date-fns';
import {Fragment} from 'react';
import rsvpToEvent from '../../../support/rsvp';

export default function OrientationCard({orientation}) {
    const toast = useToast({
        duration: 7500,
        position: 'bottom',
    });

    const rsvpToOrientation = (eventId: number) => rsvpToEvent(eventId, true, toast);

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
                                {orientation.upcomingEvents.length > 0 ? (
                                    <Grid templateColumns="2fr auto auto" gap={5} alignItems="center" pt={6}>
                                        {orientation.upcomingEvents.map(
                                            ({id, address, organizer, date, link, title}, index) => {
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
                                                        <Flex alignItems="flex-start" flexDirection="column">
                                                            <Text fontSize="xl" fontWeight="bolder" color="red.500">
                                                                {title}
                                                            </Text>
                                                            <Text color="gray.500" fontWeight={400}>
                                                                {`${format(eventDate, 'LLLL d')} • ${format(
                                                                    eventDate,
                                                                    "EEEE '•' h:mmaaa"
                                                                )}`}
                                                            </Text>
                                                        </Flex>
                                                        <Button as="a" href={link} variant="red" target="_blank">
                                                            More Info
                                                        </Button>
                                                        <Button onClick={() => rsvpToOrientation(id)}>RSVP</Button>
                                                    </Fragment>
                                                );
                                            }
                                        )}
                                    </Grid>
                                ) : (
                                    <Center pt={10} pb={6}>
                                        <Text>
                                            There are no scheduled upcoming orientations. Please check again later.
                                        </Text>
                                    </Center>
                                )}
                            </AccordionPanel>
                        </>
                    )}
                </AccordionItem>
            </Accordion>
        </RequirementCard>
    );
}
