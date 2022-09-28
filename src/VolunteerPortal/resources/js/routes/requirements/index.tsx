import {ReactNode} from 'react';
import {Button, Link, List, ListIcon, ListItem, Text, VStack} from '@chakra-ui/react';
import {ChevronRightIcon} from '@chakra-ui/icons';

import RequirementCard from './components/RequirementCard';
import BackgroundCheckCard from './components/BackgroundCheckCard';
import Section from '../../components/Section';
import {Requirements} from '../../types/Requirements';
import OrientationCard from './components/OrientationCard';

export default function Requirements({
    requirements: {orientation, codeOfConduct, volunteerRelease},
}: {
    requirements: Requirements;
}) {
    return (
        <Section heading="Volunteer Requirement Status">
            <VStack alignItems="flex-start" gap={4} mb={10}>
                <Text>
                    We take the safety and health of our students very seriously. At the same time, we are extremely
                    fortunate to have such a generous group of volunteers eager and willing to tutor. It's our goal to
                    ensure the safety of our students and also make volunteering have as low a barrier to entry as
                    possible.
                </Text>
                <Text>To volunteer, we currently require the following:</Text>
                <List>
                    <CheckListItem>Pass an Online Background Check</CheckListItem>
                    <CheckListItem>Attendance of an Orientation Session</CheckListItem>
                    <CheckListItem>Acceptance of the Code of Conduct</CheckListItem>
                    <CheckListItem>Acceptance of the Volunteer Release & Waiver</CheckListItem>
                </List>
                <Text>
                    Each of those items and your current status are listed below. For any questions about your status
                    below, please feel free to reach out to our volunteer coordinator via our{' '}
                    <Link href="/contact" color="cyan.500">
                        Contact Form.
                    </Link>
                </Text>
            </VStack>
            <VStack gap={6}>
                <BackgroundCheckCard />
                <OrientationCard orientation={orientation} />
                <RequirementCard header="Code of Conduct Status" completed={codeOfConduct.completed}>
                    <Text>
                        <strong>REQUIREMENT: </strong>To volunteer, you must agree to our Code of Conduct.
                    </Text>
                    <Button as="a" href="/volunteer/code-of-conduct/" target="_blank" variant="red">
                        View & Submit Code of conduct
                    </Button>
                </RequirementCard>
                <RequirementCard header="Volunteer Release Status" completed={volunteerRelease.completed}>
                    <Text>
                        <strong>REQUIREMENT: </strong>To volunteer, you must agree to our Volunteer Release & Waiver of
                        Liability.
                    </Text>
                    <Button as="a" href="/volunteer/waiver/" target="_blank" variant="red">
                        View & Submit Volunteer Release
                    </Button>
                </RequirementCard>
            </VStack>
        </Section>
    );
}

function CheckListItem({children}: {children: ReactNode}) {
    return (
        <ListItem>
            <ListIcon as={ChevronRightIcon} color="cyan.500" />
            {children}
        </ListItem>
    );
}
