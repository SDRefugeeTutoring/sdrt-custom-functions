import {createRoot} from 'react-dom/client';
import {BrowserRouter, Routes, Route} from 'react-router-dom';
import {ChakraProvider, Container, SimpleGrid} from '@chakra-ui/react';

import Index, {DashboardProps, dashboardPropsFromWindow} from './routes/dashboard';
import NavLink from './components/NavLink';
import Profile from './routes/Profile';
import Requirements from './routes/Requirements';
import UpcomingEvents from './routes/UpcomingEvents';
import theme from './Theme';

declare global {
    interface Window {
        sdrtVolunteerPortal: {
            dashboard: DashboardProps;
        };
    }
}

const dashboardProps = dashboardPropsFromWindow();

function App() {
    return (
        <ChakraProvider theme={theme}>
            <BrowserRouter basename="volunteer-portal">
                <Container as="nav" py={8} centerContent>
                    <SimpleGrid
                        gap={6}
                        templateColumns={{md: 'repeat(4, max-content)', sm: '1fr'}}
                        justifyItems="center"
                    >
                        <NavLink to="/" text="Dashboard" />
                        <NavLink to="/profile" text="Profile Information" />
                        <NavLink to="/requirements" text="Requirements Status" />
                        <NavLink to="/events" text="Upcoming Events" />
                    </SimpleGrid>
                </Container>
                <Routes>
                    <Route path="/" element={<Index {...dashboardProps} />} />
                    <Route path="profile" element={<Profile />} />
                    <Route path="requirements" element={<Requirements />} />
                    <Route path="upcoming-events" element={<UpcomingEvents />} />
                </Routes>
            </BrowserRouter>
        </ChakraProvider>
    );
}

const app = document.getElementById('volunteer-portal');
const root = createRoot(app);

root.render(<App />);
