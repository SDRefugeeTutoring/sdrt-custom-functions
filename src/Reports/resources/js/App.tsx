import {createRoot} from 'react-dom/client';
import {Container, TabList, Tabs, Tab, TabPanels, TabPanel, Heading, ChakraProvider} from '@chakra-ui/react';
import TutoringSessions from './tabs/TutoringSessions';
import EventVolunteers from "./tabs/EventVolunteers";

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
            <Container py={8}>
                <Heading>Reports</Heading>
                {/* @ts-ignore */}
                <Tabs>
                    <TabList>
                        <Tab>Tutoring Sessions</Tab>
                        <Tab>Event Volunteers</Tab>
                        <Tab>Tab 3</Tab>
                    </TabList>

                    <TabPanels>
                        <TabPanel>
                            <TutoringSessions />
                        </TabPanel>
                        <TabPanel>
                            <EventVolunteers />
                        </TabPanel>
                        <TabPanel>Three</TabPanel>
                    </TabPanels>
                </Tabs>
            </Container>
        </ChakraProvider>
    );
}

createRoot(document.getElementById('sdrt-report')).render(<App />);
