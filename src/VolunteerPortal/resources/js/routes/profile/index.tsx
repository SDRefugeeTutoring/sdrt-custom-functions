import {FormControl, FormLabel, Heading, Stack, VStack, Input, Text, Button} from '@chakra-ui/react';
import {useRef, forwardRef, FormEvent, Ref} from 'react';

import Section from '../../components/Section';
import {useStore} from '../../store';

export default function Profile() {
    const {user} = useStore();

    const firstNameRef = useRef<HTMLInputElement>(null);
    const lastNameRef = useRef<HTMLInputElement>(null);
    const emailRef = useRef<HTMLInputElement>(null);
    const passwordRef = useRef<HTMLInputElement>(null);
    const confirmPasswordRef = useRef<HTMLInputElement>(null);

    const handleSubmit = (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        const firstName = firstNameRef.current?.value;
        const lastName = lastNameRef.current?.value;
        const email = emailRef.current?.value;
        const password = passwordRef.current?.value;
        const confirmPassword = confirmPasswordRef.current?.value;
        console.log({firstName, lastName, email, password, confirmPassword});
    };

    return (
        <Section heading="Profile Information">
            <form onSubmit={handleSubmit}>
                <VStack gap={12} alignItems="flex-start">
                    <Stack w="100%" gap={4} direction={['column', 'row']}>
                        <InputAndLabel
                            label="First Name"
                            id="firstName"
                            ref={firstNameRef}
                            initialValue={user.firstName}
                            required
                        />
                        <InputAndLabel
                            label="Last Name"
                            id="lastName"
                            ref={lastNameRef}
                            initialValue={user.lastName}
                            required
                        />
                    </Stack>
                    <InputAndLabel label="Email" id="email" type="email" ref={emailRef} initialValue={user.email} />
                    <VStack bg="neutral.50" px={8} py={6} w="100%" gap={4} alignItems="flex-start">
                        <Heading as="h2" fontSize={['lg', 'xl']}>
                            Change Your Password
                        </Heading>
                        <Stack w="100%" gap={4} direction={['column', 'row']}>
                            <InputAndLabel label="New Password" id="password" type="password" ref={passwordRef} />
                            <InputAndLabel
                                label="Re-Enter Password"
                                id="confirmPassword"
                                type="password"
                                ref={confirmPasswordRef}
                            />
                        </Stack>
                    </VStack>
                    <Button type="submit">Update Profile</Button>
                </VStack>
            </form>
        </Section>
    );
}

interface InputAndLabelProps {
    id: string;
    label: string;
    initialValue?: string;
    type?: string;
    required?: boolean;
}

const InputAndLabel = forwardRef(
    ({id, label, initialValue, type = 'text', required = false}: InputAndLabelProps, ref: Ref<HTMLInputElement>) => {
        return (
            <FormControl w="100%">
                <FormLabel htmlFor={id}>
                    {label}
                    {required && (
                        <Text as="span" color="red.400">
                            *
                        </Text>
                    )}
                </FormLabel>
                <Input id={id} type={type} ref={ref} required={required} variant="filled" defaultValue={initialValue} />
            </FormControl>
        );
    }
);
