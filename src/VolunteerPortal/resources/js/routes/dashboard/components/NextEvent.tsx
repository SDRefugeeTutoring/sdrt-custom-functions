import {Heading, VStack, Flex, Text, Button} from '@chakra-ui/react';
import {format} from 'date-fns';

import Card from '../../../components/Card';

export interface NextEventProps {
    eventId: number;
    name: string;
    date: Date;
    category: string;
    organizer: string;
    location?: {
        name: string;
        address: string;
    };
}

export default function NextEvent({category, organizer, location, date}: NextEventProps) {
    return (
        <Card bg="blue.50">
            <Flex direction="column">
                <Heading as="h2" size="md" mb={4}>
                    Next Scheduled Event
                </Heading>
                <Flex justify="space-between" fontSize="sm" flexBasis={0} flexGrow={0} alignItems="flex-end">
                    <VStack align="flex-start" justify="space-between">
                        <Text as="strong" fontSize="lg" color="cyan.600">
                            {format(date, 'MMM do')}
                        </Text>
                        <Text>{format(date, 'EEE • h:mmaaa')}</Text>
                    </VStack>
                    <VStack justify="space-between">
                        <Text color="gray.500">Tutoring</Text>
                        <Text>{category}</Text>
                    </VStack>
                    <VStack justify="space-between">
                        <Text color="gray.500">Organizer</Text>
                        <Text>{organizer ? organizer : 'No Organizer'}</Text>
                    </VStack>
                    {
                        location && (
                            <VStack justify="space-between">
                                <Text color="gray.500">{location.name}</Text>
                                <Text>{location.address}</Text>
                            </VStack>
                        )
                    }
                    <Button>Cancel RSVP</Button>
                </Flex>
            </Flex>
        </Card>
    );
}
