import {Box} from '@chakra-ui/react';

export default function Card({children, ...props}) {
    return (
        <Box boxShadow="md" borderRadius="lg" px={10} py={6} w="100%" bg="gray.100" {...props}>
            {children}
        </Box>
    );
}
