import {ReactNode} from 'react';
import {
    Box,
    Button,
    Heading,
    HStack,
    Link,
    List,
    ListIcon,
    ListItem,
    Tag,
    TagLabel,
    Text,
    VStack,
} from '@chakra-ui/react';
import {CheckIcon} from '@chakra-ui/icons';

import Section from '../../components/Section';
import Card from '../../components/Card';

export default function Requirements() {
    return (
        <Section heading="Volunteer Requirement Status">
            <VStack alignItems="flex-start" gap={4} mb={10}>
                <Text>
                    We take the safety and health of our students very seriously. At the same time, we are extremely
                    fortunate to have such a generous group of volunteers eager and willing to tutor. It's our goal to
                    ensure the safety of our students and also make volunteering have as low a barrier to entry as
                    possible.
                </Text>
                <Text>To volunteer, we currently require the following:</Text>
                <List>
                    <CheckListItem>Acceptance of our Code of Conduct</CheckListItem>
                    <CheckListItem>Acceptance of our Virtual Tutoring Code of Conduct</CheckListItem>
                    <CheckListItem>Attendance to a one-hour orientation session</CheckListItem>
                    <CheckListItem>Pass an online background check</CheckListItem>
                </List>
                <Text>
                    Each of those items and your current status are listed below. For any questions about your status
                    below, please feel free to reach out to our volunteer coordinator via our{' '}
                    <Link href="/contact" color="cyan.500">
                        Contact Form.
                    </Link>
                </Text>
            </VStack>
            <Card bg="red.100">
                <VStack gap={4} alignItems="flex-start">
                    <HStack justify="space-between" w="100%">
                        <Heading as="h2" size="xl">
                            Background Check
                        </Heading>
                        <HStack>
                            <Text as="span" fontWeight={700} fontSize="sm" textTransform="uppercase">
                                Status:
                            </Text>
                            <Tag
                                borderRadius="full"
                                px={4}
                                bg="red.500"
                                color="white"
                                fontWeight={700}
                                textTransform="uppercase"
                                letterSpacing={2}
                            >
                                Incomplete
                            </Tag>
                        </HStack>
                    </HStack>
                    <Text>
                        <strong>REQUIREMENT: </strong> To volunteer, you must apply for and clear a background check via
                        Checkr -- our online background check partner.
                    </Text>
                    <Box bg="red.200" w="100%" p={3}>
                        <Text>
                            <strong>Note:</strong> Missing second form of identification, please provide at your
                            earliest convenience.
                        </Text>
                    </Box>
                    <Button variant="red">Complete Background Check</Button>
                </VStack>
            </Card>
        </Section>
    );
}

function CheckListItem({children}: {children: ReactNode}) {
    return (
        <ListItem>
            <ListIcon as={CheckIcon} color="cyan.500" />
            {children}
        </ListItem>
    );
}
