import React, {useRef, useState} from 'react';
import {Button, Flex, FormControl, FormLabel, Input, Text} from '@chakra-ui/react';
import {fetchAndDownloadReportFile} from '../support/fetchReportFile';
import {format} from 'date-fns';

export default function TutoringSessions() {
    const form = useRef<HTMLFormElement>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        setLoading(true);

        const formData = new FormData(form.current);
        formData.set('startDate', format(new Date(formData.get('startDate') as string), "yyyy-MM-dd '00:00:00'"));
        formData.set('endDate', format(new Date(formData.get('endDate') as string), "yyyy-MM-dd '23:59:59'"));

        await fetchAndDownloadReportFile('sessions', formData);
        setLoading(false);
    };

    return (
        // @ts-ignore
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
            <Button isLoading={loading} loadingText="Exporting..." type="submit">Export</Button>
        </Flex>
    );
}
