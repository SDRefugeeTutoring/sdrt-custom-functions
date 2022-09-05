import Section from "./Section";
import {ListItem, Text, UnorderedList, Flex} from "@chakra-ui/react";

export default function RouteFallback({error, resetErrorBoundary}) {
    return (
        <Section heading="Something went wrong.">
            <Flex flexDirection="column" gap={4}>
                <Text>
                    We apologize that you're seeing this, but something broke in the dashboard.
                    Please reach out to <a href="mailto:info@sdrefugeetutoring.com">info@sdrefugeetutoring.com</a> with the
                    following details:
                </Text>
                <UnorderedList>
                    <ListItem>The email of your account</ListItem>
                    <ListItem>What happened right before this error occurred</ListItem>
                    <ListItem>What you were trying to do</ListItem>
                </UnorderedList>
                <Text>This will help us to best look into the issue and help you with what you were trying to do.</Text>
                <Text>Thank you for your patience!</Text>
            </Flex>
        </Section>
    );
}
