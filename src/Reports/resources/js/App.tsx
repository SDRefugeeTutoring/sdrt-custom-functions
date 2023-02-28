import {createRoot} from 'react-dom/client';
import {Container, TabList, Tabs, Tab, TabPanels, TabPanel, Heading, ChakraProvider, Flex} from '@chakra-ui/react';
import TutoringSessions from './tabs/TutoringSessions';
import EventVolunteers from "./tabs/EventVolunteers";
import Volunteers from "./tabs/Volunteers";

declare global {
    interface Window {
        sdrtReports: {
            restApi: {
                url: string;
                reportsUrl: string;
                nonce: string;
            }
        }
    }
}

function App() {
    return (
        <ChakraProvider>
            <Container py={8} maxW="2xl">
                <Flex flexDirection="column" gap={3} p={4} bgColor="gray.50" borderRadius={6}>
                    <Heading>Reports</Heading>
                    {/* @ts-ignore */}
                    <Tabs>
                        <TabList>
                            <Tab>Tutoring Sessions</Tab>
                            <Tab>Event Volunteers</Tab>
                            <Tab>All Volunteers</Tab>
                        </TabList>

                        <TabPanels>
                            <TabPanel>
                                <TutoringSessions />
                            </TabPanel>
                            <TabPanel>
                                <EventVolunteers />
                            </TabPanel>
                            <TabPanel>
                                <Volunteers />
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </Flex>
            </Container>
        </ChakraProvider>
    );
}

createRoot(document.getElementById('sdrt-report')).render(<App />);
