import {createRoot} from 'react-dom/client';
import {Container, TabList, Tabs, Tab, TabPanels, TabPanel, Heading, ChakraProvider} from '@chakra-ui/react';

function App() {
    return (
        <ChakraProvider>
            <Container>
                <Heading>Reports</Heading>
                <Tabs>
                    <TabList>
                        <Tab>Tab 1</Tab>
                        <Tab>Tab 2</Tab>
                        <Tab>Tab 3</Tab>
                    </TabList>

                    <TabPanels>
                        <TabPanel>One</TabPanel>
                        <TabPanel>Two</TabPanel>
                        <TabPanel>Three</TabPanel>
                    </TabPanels>
                </Tabs>
            </Container>
        </ChakraProvider>
    );
}

createRoot(document.getElementById('sdrt-report')).render(<App />);
