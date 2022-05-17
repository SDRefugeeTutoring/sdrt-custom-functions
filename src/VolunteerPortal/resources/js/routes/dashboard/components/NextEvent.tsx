import {Heading, HStack, VStack, Flex, Text, Button} from '@chakra-ui/react';
import Card from '../../../components/Card';

export interface NextEventProps {
    eventId: number;
    name: string;
    date: Date;
    category: string;
    organizer: string;
    location: {
        name: string;
        address: string;
    };
}

export default function NextEvent({category, organizer, location}: NextEventProps) {
    return (
        <Card bg="blue.50">
            <Flex direction="column">
                <Heading as="h2" size="md" mb={4}>
                    Next Scheduled Event
                </Heading>
                <Flex justify="space-between" fontSize="sm" flexBasis={0} flexGrow={0}>
                    <VStack align="flex-start" justify="space-between">
                        <Text as="strong" fontSize="lg" color="cyan.600">
                            June 10
                        </Text>
                        <Text>Tues • 4:50-6:25</Text>
                    </VStack>
                    <VStack justify="space-between">
                        <Text color="gray.500">Tutoring</Text>
                        <Text>{category}</Text>
                    </VStack>
                    <VStack justify="space-between">
                        <Text color="gray.500">Organizer</Text>
                        <Text>{organizer}</Text>
                    </VStack>
                    <VStack justify="space-between">
                        <Text color="gray.500">{location.name}</Text>
                        <Text>{location.address}</Text>
                    </VStack>
                    <Button>Cancel RSVP</Button>
                </Flex>
            </Flex>
        </Card>
    );
}
