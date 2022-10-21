import {fetchSdrtApi} from './fetchRestApi';
import {AlertStatus, useToast, UseToastOptions} from '@chakra-ui/react';

type RsvpResponse = {
    rsvp: {
        id: number;
    };
    eventAtCapacity: boolean;
};

export default async function rsvpToEvent(
    eventId: number,
    attending: boolean,
    toast: (options: UseToastOptions) => void
): Promise<'atCapacity' | boolean> {
    const response = await fetchSdrtApi('requirements/rsvp', {
        body: {eventId, attending},
    });

    if (response.ok) {
        await response.json();

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
            status: AlertStatus = 'error',
            duration: number = 5000,
            returnData: 'atCapacity' | false = false;

        if (reason === 'rsvp_already_exists') {
            status = 'info';
            description = "You have already RSVP'd to this event. No need to RSVP again.";
        } else if (reason === 'past_event') {
            description = "You can't RSVP to an event that has already passed.";
        } else if (reason === 'event_full') {
            status = 'warning';
            description =
                'This event is full. You have been added to the waitlist & will be notified if there are any openings!';
            duration = 10000;
            returnData = 'atCapacity';
        } else if (reason === 'not_event') {
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
            duration,
        });

        return returnData;
    }
}
