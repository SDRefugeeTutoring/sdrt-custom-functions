import {Heading, HStack, Icon, Text, VStack} from '@chakra-ui/react';
import {InfoIcon, WarningTwoIcon} from '@chakra-ui/icons';
import Card from '../../../components/Card';

export interface MessageProps {
    heading: string;
    message: string;
    icon: typeof Icon;
    bgColor: string;
    iconColor: string;
}

export enum MessageUrgency {
    Info = 'info',
    Warning = 'warning',
    Urgent = 'urgent',
}

export function getMessageProps(message: string, heading: string, urgency: MessageUrgency): MessageProps {
    switch (urgency) {
        case MessageUrgency.Info:
            return {
                message,
                heading,
                icon: InfoIcon,
                bgColor: 'blue.100',
                iconColor: 'blue.600',
            };
        case MessageUrgency.Warning:
            return {
                message,
                heading,
                icon: WarningTwoIcon,
                bgColor: 'orange.100',
                iconColor: 'orange.600',
            };
        case MessageUrgency.Urgent:
            return {
                message,
                heading,
                icon: WarningTwoIcon,
                bgColor: 'red.100',
                iconColor: 'red.600',
            };
    }
}

export default function Message({message, heading, icon, bgColor, iconColor}: MessageProps) {
    return (
        <Card bg={bgColor}>
            <VStack spacing={4} align="flex-start">
                <HStack>
                    <Icon as={icon} size="2x" color={iconColor} />
                    <Heading as="h2" size="md" textTransform="uppercase">
                        {heading}
                    </Heading>
                </HStack>
                <Text>{message}</Text>
            </VStack>
        </Card>
    );
}
