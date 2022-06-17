import {createRoot} from 'react-dom/client';
import {BrowserRouter, Routes, Route} from 'react-router-dom';
import {ChakraProvider, Container, SimpleGrid} from '@chakra-ui/react';

import NavLink from './components/NavLink';
import Index, {DashboardProps, dashboardPropsFromWindow} from './routes/dashboard';
import Profile from './routes/profile';
import Requirements from './routes/requirements';
import UpcomingEvents from './routes/UpcomingEvents';
import theme from './Theme';
import {UserContextProvider} from './stores/UserStore';
import User from './types/User';
import {Requirements as RequirementsData} from './types/Requirements';
import {RequirementsContextProvider} from './stores/RequirementsStore';

declare global {
    interface Window {
        sdrtVolunteerPortal: {
            dashboard: DashboardProps;
            user: User;
            requirements: RequirementsData;
            restApi: {
                url: string;
                nonce: string;
            };
        };
    }
}

const dashboardProps = dashboardPropsFromWindow();

function App() {
    return (
        <ChakraProvider theme={theme}>
            <BrowserRouter basename="volunteer-portal">
                <UserContextProvider>
                    <RequirementsContextProvider>
                        <Container as="nav" centerContent bgColor="neutral.100">
                            <SimpleGrid
                                gap={6}
                                templateColumns={{md: 'repeat(4, max-content)', sm: '1fr'}}
                                justifyItems="center"
                            >
                                <NavLink to="/" text="Dashboard" />
                                <NavLink to="/profile" text="Profile Information" />
                                <NavLink to="/requirements" text="Requirements Status" />
                                <NavLink to="/upcoming-events" text="Upcoming Events" />
                            </SimpleGrid>
                        </Container>
                        <Routes>
                            <Route path="/" element={<Index {...dashboardProps} />} />
                            <Route path="profile" element={<Profile />} />
                            <Route
                                path="requirements"
                                element={<Requirements requirements={window.sdrtVolunteerPortal.requirements} />}
                            />
                            <Route path="upcoming-events" element={<UpcomingEvents />} />
                        </Routes>
                    </RequirementsContextProvider>
                </UserContextProvider>
            </BrowserRouter>
        </ChakraProvider>
    );
}

const app = document.getElementById('volunteer-portal');
const root = createRoot(app);

root.render(<App />);
