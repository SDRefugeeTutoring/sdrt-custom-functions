import {FormControl, FormLabel, Heading, Stack, VStack, Input, Text, Button, useToast} from '@chakra-ui/react';
import {useRef, forwardRef, FormEvent, Ref, ChangeEventHandler} from 'react';

import Section from '../../components/Section';
import {useStore} from '../../store';
import {fetchRestApi} from '../../support/fetchRestApi';

interface ValidationError {
    code: string;
    message: string;
    details: {
        [key: string]: {
            code: string;
            data: string;
            message: string;
        };
    };
}

interface UpdateData {
    first_name: string;
    last_name: string;
    email: string;
    password?: string;
}

export default function Profile() {
    const toast = useToast({
        duration: 5000,
        isClosable: true,
        position: 'bottom',
    });
    const {user} = useStore();

    const firstNameRef = useRef<HTMLInputElement>(null);
    const lastNameRef = useRef<HTMLInputElement>(null);
    const emailRef = useRef<HTMLInputElement>(null);
    const passwordRef = useRef<HTMLInputElement>(null);
    const confirmPasswordRef = useRef<HTMLInputElement>(null);

    const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (passwordRef.current.value && passwordRef.current.value !== confirmPasswordRef.current.value) {
            confirmPasswordRef.current.setCustomValidity('Passwords must match.');
            confirmPasswordRef.current.reportValidity();
            return;
        }

        try {
            const data = {
                first_name: firstNameRef.current.value,
                last_name: lastNameRef.current.value,
                email: emailRef.current.value,
            } as UpdateData;

            if (passwordRef.current.value) {
                data.password = passwordRef.current.value;
            }

            const response = await fetchRestApi('wp/v2/users/me', 'POST', JSON.stringify(data));

            if (response.ok) {
                toast({
                    title: 'Profile Updated',
                    description: 'Your profile information has been updated.',
                    status: 'success',
                });
            } else if (response.status === 400) {
                const error = (await response.json()) as ValidationError;
                toast({
                    title: 'Profile Update Failed',
                    description: error.message,
                    status: 'error',
                    duration: 10000,
                });
            } else {
                toast({
                    title: 'Error',
                    description: 'There was an error updating your profile.',
                    status: 'error',
                });
            }

            passwordRef.current.value = '';
            confirmPasswordRef.current.value = '';
        } catch (error) {
            toast({
                title: 'Error',
                description:
                    'There was an error updating your profile. Please try again and contact the volunteer coordinator if the problem persists.',
                status: 'error',
            });

            console.error(error);
        }
    };

    const resetPasswordValidity = () => {
        if (!confirmPasswordRef.current.validity.valid) {
            confirmPasswordRef.current.setCustomValidity('');
            return;
        }
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
                            <InputAndLabel
                                label="New Password"
                                id="password"
                                type="password"
                                ref={passwordRef}
                                onChange={resetPasswordValidity}
                            />
                            <InputAndLabel
                                label="Re-Enter Password"
                                id="confirmPassword"
                                type="password"
                                ref={confirmPasswordRef}
                                onChange={resetPasswordValidity}
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
    onChange?: ChangeEventHandler<HTMLInputElement>;
    required?: boolean;
}

const InputAndLabel = forwardRef(
    (
        {id, label, initialValue, type = 'text', required = false, onChange = null}: InputAndLabelProps,
        ref: Ref<HTMLInputElement>
    ) => {
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
                <Input
                    id={id}
                    type={type}
                    ref={ref}
                    required={required}
                    variant="filled"
                    defaultValue={initialValue}
                    onChange={onChange}
                />
            </FormControl>
        );
    }
);
