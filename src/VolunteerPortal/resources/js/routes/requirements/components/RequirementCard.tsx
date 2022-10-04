import {ReactNode} from 'react';
import Card from '../../../components/Card';
import {Heading, Flex, HStack, Tag, Text, VStack} from '@chakra-ui/react';

export default function RequirementCard({
    children,
    header,
    completed,
    baseColor = null,
    alwaysShowChildren = false,
}: {
    header: string;
    children: ReactNode;
    completed: boolean;
    baseColor?: string | null;
    alwaysShowChildren?: boolean;
}) {
    const color: string = baseColor || (completed ? 'green' : 'red');

    return (
        <Card bg={`${color}.100`}>
            <VStack gap={4} alignItems="flex-start">
                <Flex justify="space-between" w="100%" direction={{base: 'column', md: 'row'}} gap={{base: 4, md: 0}}>
                    <Heading as="h2" size="lg">
                        {header}
                    </Heading>
                    <Flex gap={4}>
                        <Text as="span" fontWeight={700} fontSize="sm" textTransform="uppercase">
                            Status:
                        </Text>
                        <Tag
                            borderRadius="full"
                            px={4}
                            bg={`${color}.500`}
                            color="white"
                            fontWeight={700}
                            textTransform="uppercase"
                            letterSpacing={2}
                        >
                            {completed ? 'Completed' : 'Incomplete'}
                        </Tag>
                    </Flex>
                </Flex>
                {(!completed || alwaysShowChildren) && children}
            </VStack>
        </Card>
    );
}
