import {useRef} from "react";
import {Flex, Text, FormLabel, Input, FormControl, Button} from "@chakra-ui/react";
import {format} from "date-fns";
import {fetchAndDownloadReportFile} from "../support/fetchReportFile";

export default function Volunteers() {
    const form = useRef<HTMLFormElement>(null);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const formData = new FormData(form.current);

        await fetchAndDownloadReportFile('volunteers', formData);
    };

    return (
        // @ts-ignore
        <Flex as="form" onSubmit={handleSubmit} ref={form} flexDir="column" gap={3}>
            <Text fontSize="lg">Export all volunteers with metrics for volunteer history</Text>
            <Button type="submit">Export</Button>
        </Flex>
    );
}