import React, {FormEvent, ReactNode, useRef, useState} from "react";
import {Button, Flex, FormControl, FormHelperText, FormLabel, Text} from "@chakra-ui/react";
import AsyncSelect from "react-select/async";
import {fetchRestApi} from "../support/fetchRestApi";
import {fetchAndDownloadReportFile} from "../support/fetchReportFile";

interface EventOption {
    value: string;
    label: string|ReactNode;
}

interface EventResponse {
    events: Array<{
        id: number;
        title: string;
    }>;
}


export default function EventVolunteers() {
    const form = useRef<HTMLFormElement>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const handleSubmit = async (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        setLoading(true);
        const formData = new FormData(form.current);

        await fetchAndDownloadReportFile('event-volunteers', formData);
        setLoading(false);
    }

    return (
        // @ts-ignore
        <Flex as="form" onSubmit={handleSubmit} ref={form} flexDir="column" gap={3}>
            <Text fontSize="lg">Export volunteer data for a given event</Text>

            <FormControl isRequired>
                <FormLabel>Event</FormLabel>
                <FormHelperText>Search for an event by name or ID</FormHelperText>
                <AsyncSelect name="eventId" cacheOptions loadOptions={loadEvents} />
            </FormControl>
            <Button isLoading={loading} loadingText="Exporting..." type="submit">Export</Button>
        </Flex>
    );
}

async function loadEvents(inputValue: string): Promise<EventOption[]> {
    if (Number.isInteger(parseInt(inputValue))) {
        const response = await fetchRestApi(
            `tribe/events/v1/events/${inputValue}`,
            {method: 'GET'}
        );

        if (!response.ok) {
            throw Error('Failed to load events');
        }

        const event: { id: number, title: string } = await response.json();

        return [{
            label: <span dangerouslySetInnerHTML={{ __html: event.title }} />,
            value: event.id.toString(),
        }];
    }

    const response = await fetchRestApi(
        `tribe/events/v1/events?search=${inputValue}&per_page=10`,
        {method: 'GET'}
    );

    if (!response.ok) {
        throw Error('Failed to load events');
    }

    const events: EventResponse = await response.json();

    return events.events.map(event => ({
        label: <span dangerouslySetInnerHTML={{ __html: event.title }} />,
        value: event.id.toString(),
    }))
}
