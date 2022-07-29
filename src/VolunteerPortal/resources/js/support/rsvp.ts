import {fetchSdrtApi} from './fetchRestApi';
import {AlertStatus, useToast} from '@chakra-ui/react';

export default async function rsvpToEvent(eventId: number, attending: boolean, toast: Function) {
    const response = await fetchSdrtApi('requirements/rsvp', {
        body: {eventId, attending},
    });

    if (response.ok) {
        toast({
            title: "You have RSVP'd",
            description: attending
                ? 'Thank you for your RSVP! See you at the event!'
                : "Sorry you're not able to attend. Thank you for letting us know!",
            status: 'success',
        });

        return true;
    } else {
        const error = await response.json();
        const reason = error.reason ?? null;
        let description: string,
            status: AlertStatus = 'error';

        if (reason === 'rsvp_already_exists') {
            status = 'info';
            description = "You have already RSVP'd to this event. No need to RSVP again.";
        } else if (reason === 'not_event') {
            status = 'warning';
            description = 'Invalid event. If you believe this is an error, please contact the volunteer coordinator.';
        } else if (reason === 'cannot_volunteer') {
            status = 'warning';
            description = 'You are not able to RSVP to volunteer events until all requirements have been met.';
        } else {
            description =
                'A problem occurred when creating your RSVP. Please refresh, try again, and contact the volunteer coordinator if the problem persists.';
        }

        toast({
            title: 'Unable to RSVP',
            description,
            status,
        });

        return false;
    }
}
