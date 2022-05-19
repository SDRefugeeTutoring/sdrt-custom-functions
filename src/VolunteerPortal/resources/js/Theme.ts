import {extendTheme} from '@chakra-ui/react';
import type {ComponentStyleConfig} from '@chakra-ui/theme';

const Button: ComponentStyleConfig = {
    baseStyle: {
        border: 0,
        borderRadius: '2rem',
        boxShadow: '0px 0.1rem 0.3rem rgba(0, 0, 0, 0.4)',
        textTransform: 'uppercase',
        color: 'white',
        '&:hover': {
            border: 0,
        },
    },
    variants: {
        primary: {
            bg: 'cyan.400',
            '&:hover': {
                bg: 'cyan.600',
            },
        },
        red: {
            bg: 'red.400',
            '&:hover': {
                bg: 'red.600',
            },
        },
        outline: {
            bg: 'transparent',
            border: '1px solid',
            borderColor: 'cyan.400',
            color: 'cyan.400',
            '&:hover': {
                bg: 'cyan.400',
                border: '1px solid',
                borderColor: 'cyan.600',
            },
        },
    },
    defaultProps: {
        variant: 'primary',
    },
};

const Container: ComponentStyleConfig = {
    baseStyle: {
        maxWidth: '100%',
        bg: 'white',
        px: 10,
        py: 8,
        m: 0,
    },
    defaultProps: {
        centerContent: false,
    },
};

export default extendTheme({
    fontSizes: {
        xs: '0.75rem',
        sm: '0.875rem',
        md: '1rem',
        lg: '1.125rem',
        xl: '1.25rem',
        '2xl': '1.5rem',
        '3xl': '1.875rem',
        '4xl': '2.25rem',
        '5xl': '3rem',
    },
    components: {
        Button,
        Container,
    },
});
