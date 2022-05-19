import {Box, FormControl, FormLabel, Heading, VStack, Input, Text, Button} from '@chakra-ui/react';
import Section from '../../components/Section';

export default function Profile() {
    return (
        <Section heading="Profile Information">
            <FormControl>
                <VStack gap={12} alignItems="flex-start">
                    <InputAndLabel label="First Name" id="firstName" required />
                    <InputAndLabel label="Last Name" id="lastName" required />
                    <InputAndLabel label="Email" id="email" type="email" />
                    <Box bg="gray.200" px={8} py={6}>
                        <Heading as="h2">Change Your Password</Heading>
                        <InputAndLabel label="New Password" id="password" type="password" />
                        <InputAndLabel label="Re-Enter Password" id="confirmPassword" type="password" />
                    </Box>
                </VStack>
                <Button as="input" type="submit" value="Update Profile">
                    Update Profile
                </Button>
            </FormControl>
        </Section>
    );
}

function InputAndLabel({id, label, required = false, type = 'text'}) {
    return (
        <Box w="100%">
            <FormLabel htmlFor={id}>
                {label}
                {required && (
                    <Text as="span" color="red.400">
                        *
                    </Text>
                )}
            </FormLabel>
            <Input id={id} type={type} required={required} />
        </Box>
    );
}
