import {Heading, HStack, Icon, Text, VStack} from '@chakra-ui/react';
import {InfoIcon, WarningTwoIcon} from '@chakra-ui/icons';
import Card from '../../../components/Card';

export interface MessageProps {
    heading: string;
    message: string;
    icon: typeof Icon;
    bgColor: string;
}

export enum MessageUrgency {
    Info = 'info',
    Warning = 'warning',
    Urgent = 'urgent',
}

export function getMessageProps(message: string, urgency: MessageUrgency): MessageProps {
    switch (urgency) {
        case MessageUrgency.Info:
            return {
                message,
                heading: 'Notice',
                icon: InfoIcon,
                bgColor: 'blue.100',
            };
        case MessageUrgency.Warning:
            return {
                message,
                heading: 'Warning Message',
                icon: WarningTwoIcon,
                bgColor: 'orange.100',
            };
        case MessageUrgency.Urgent:
            return {
                message,
                heading: 'Urgent Message',
                icon: WarningTwoIcon,
                bgColor: 'red.100',
            };
    }
}

export default function Message({message, heading, icon, bgColor}: MessageProps) {
    return (
        <Card bg={bgColor}>
            <VStack spacing={4} align="flex-start">
                <HStack>
                    <Icon as={icon} size="2x" color="red.600" />
                    <Heading as="h2" size="md" textTransform="uppercase">
                        {heading}
                    </Heading>
                </HStack>
                <Text>{message}</Text>
            </VStack>
        </Card>
    );
}
