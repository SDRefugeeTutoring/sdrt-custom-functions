import {Container, Divider, Heading} from '@chakra-ui/react';

export default function Section({heading, children}) {
    return (
        <Container>
            <Heading as="h1">{heading}</Heading>
            <Divider my={8} borderBottomWidth="0.2rem" />
            {children}
        </Container>
    );
}
