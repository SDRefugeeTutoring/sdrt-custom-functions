import React, {useRef} from 'react';
import {Button, Flex, FormControl, FormLabel, Input, Text} from '@chakra-ui/react';
import {fetchReportFile} from '../support/fetchReportFile';
import {format} from 'date-fns';

export default function TutoringSessions() {
    const form = useRef<HTMLFormElement>(null);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const formData = new FormData(form.current);
        formData.set('startDate', format(new Date(formData.get('startDate') as string), "yyyy-MM-dd '00:00:00'"));
        formData.set('endDate', format(new Date(formData.get('endDate') as string), "yyyy-MM-dd '23:59:59'"));

        const data = await fetchReportFile('sessions', formData);
        const file = await data.blob();

        const url = window.URL.createObjectURL(file);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'sessions.csv');
        document.body.appendChild(link);
        link.click();
    };

    return (
        <Flex as="form" onSubmit={handleSubmit} ref={form} flexDir="column" gap={3}>
            <Text fontSize="lg">Export all sessions with attendance metrics</Text>

            <FormControl isRequired>
                <FormLabel>Start Date</FormLabel>
                <Input name="startDate" type="date" required />
            </FormControl>
            <FormControl isRequired>
                <FormLabel>End Date</FormLabel>
                <Input name="endDate" type="date" defaultValue={format(Date.now(), 'yyyy-MM-dd')} required />
            </FormControl>
            <Button type="submit">Export</Button>
        </Flex>
    );
}
