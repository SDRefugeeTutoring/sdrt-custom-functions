import {Heading, VStack, Flex, Text, Button} from '@chakra-ui/react';
import {Markup} from 'interweave';
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

export default function NextEvent({category, name, date}: NextEventProps) {
    return (
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
                    <Button>Cancel RSVP</Button>
                </Flex>
            </Flex>
        </Card>
    );
}
