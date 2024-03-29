import {Box} from '@chakra-ui/react';

export default function Card({children, ...props}) {
    return (
        <Box boxShadow="md" borderRadius="lg" px={{base: 6, md: 10}} py={6} w="100%" bg="neutral.100" {...props}>
            {children}
        </Box>
    );
}
