import {Link, Text} from '@chakra-ui/react';
import {NavLink as RouterNavLink} from 'react-router-dom';

interface NavLinkProps {
    to: string;
    text: string;
}

export default function NavLink({to, text}: NavLinkProps) {
    return (
        <Link as={RouterNavLink} to={to}>
            {
                // @ts-ignore
                ({isActive}) => (
                    <Text fontWeight={isActive ? 'semibold' : 'normal'} fontSize="0.8rem">
                        {text}
                    </Text>
                )
            }
        </Link>
    );
}
