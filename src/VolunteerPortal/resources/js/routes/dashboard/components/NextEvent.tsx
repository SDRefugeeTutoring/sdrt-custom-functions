import {useRef, useState} from 'react';
import {
    Heading,
    VStack,
    Flex,
    Text,
    Button,
    useDisclosure,
    AlertDialog,
    AlertDialogOverlay,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogBody,
    AlertDialogFooter,
    useToast,
} from '@chakra-ui/react';
import {Markup} from 'interweave';
import {format} from 'date-fns';

import Card from '../../../components/Card';
import {fetchSdrtApi} from '../../../support/fetchRestApi';

export interface NextEventProps {
    eventId: number;
    name: string;
    date: Date;
    category: string;
}

export default function NextEvent(initialNextEvent: NextEventProps) {
    // const [isCanceling, setIsCanceling] = useState<boolean>(false);
    const [nextEvent, setNextEvent] = useState<NextEventProps | null>(initialNextEvent);
    const cancelState = useDisclosure();
    const cancelRef = useRef();
    const toast = useToast({
        duration: 7500,
        position: 'bottom',
    });

    if (nextEvent === null) {
        return null;
    }

    const {eventId, category, name, date} = nextEvent;

    const handleCancel = async () => {
        const showErrorToast = () =>
            toast({
                title: 'Error',
                duration: 10000,
                description:
                    'There was an error cancelling your RSVP. Please try again and contact the Volunteer Coordinator if the problem persists.',
                status: 'error',
            });

        try {
            const response = await fetchSdrtApi(`portal/cancel-rsvp/${eventId}`);

            if (response.ok) {
                const newEvent = await response.json();
                if (newEvent.nextEvent === null) {
                    setNextEvent(null);
                } else {
                    setNextEvent({
                        ...newEvent.nextEvent,
                        date: new Date(newEvent.nextEvent.date),
                    });
                }
            } else {
                showErrorToast();
            }
        } catch (error) {
            console.error(error);
            showErrorToast();
        }

        cancelState.onClose();
    };

    return (
        <>
            <Card bg="blue.50">
                <Flex direction="column">
                    <Heading as="h2" size="md" mb={4}>
                        Next Scheduled Event
                    </Heading>
                    <Flex justify="space-between" fontSize="sm" flexBasis={0} flexGrow={0} alignItems="flex-end">
                        <Flex alignItems="flex-start" flexDirection="column">
                            <Text as={Markup} content={name} fontSize="xl" fontWeight="bolder" color="cyan.600" />
                            <Text color="gray.500" fontWeight={400}>
                                {`${format(date, 'LLLL d')} • ${format(date, "EEEE '•' h:mmaaa")}`}
                            </Text>
                        </Flex>
                        <VStack justify="space-between">
                            <Text color="gray.500">Tutoring</Text>
                            <Text as={Markup} content={category} />
                        </VStack>
                        <Button onClick={cancelState.onOpen}>Cancel RSVP</Button>
                    </Flex>
                </Flex>
            </Card>
            <AlertDialog isOpen={cancelState.isOpen} leastDestructiveRef={cancelRef} onClose={cancelState.onClose}>
                <AlertDialogOverlay>
                    <AlertDialogContent>
                        <AlertDialogHeader fontSize="lg" fontWeight="bold">
                            Cancel RSVP
                        </AlertDialogHeader>

                        <AlertDialogBody>
                            Are you sure you want to cancel your RSVP for the <strong>{name}</strong> event?
                        </AlertDialogBody>

                        <AlertDialogFooter>
                            <Button ref={cancelRef} variant="outline" onClick={cancelState.onClose}>
                                Keep RSVP
                            </Button>
                            <Button variant="red" onClick={handleCancel} ml={3}>
                                Cancel RSVP
                            </Button>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialogOverlay>
            </AlertDialog>
        </>
    );
}
