import {useRef, useState} from "react";
import {Flex, Text, Button} from "@chakra-ui/react";
import {fetchAndDownloadReportFile} from "../support/fetchReportFile";

export default function Volunteers() {
    const form = useRef<HTMLFormElement>(null);
    const [loading, setLoading] = useState<boolean>(false);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const formData = new FormData(form.current);

        setLoading(true);
        await fetchAndDownloadReportFile('volunteers', formData);
        setLoading(false);
    };

    return (
        // @ts-ignore
        <Flex as="form" onSubmit={handleSubmit} ref={form} flexDir="column" gap={3}>
            <Text fontSize="lg">Export all volunteers with metrics for volunteer history</Text>
            <Button isLoading={loading} loadingText="Exporting..." type="submit">Export</Button>
        </Flex>
    );
}