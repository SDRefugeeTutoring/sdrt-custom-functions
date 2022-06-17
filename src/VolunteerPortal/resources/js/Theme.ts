import {extendTheme} from '@chakra-ui/react';
import type {ComponentStyleConfig} from '@chakra-ui/theme';

const Button: ComponentStyleConfig = {
    baseStyle: {
        border: 0,
        borderRadius: '2rem',
        boxShadow: '0px 0.1rem 0.3rem rgba(0, 0, 0, 0.4)',
        textTransform: 'uppercase',
        color: 'white',
        '&:hover, &:focus, &:active': {
            border: 0,
            color: 'white',
            textDecoration: 'none',
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
            bg: 'red.500',
            '&:hover': {
                bg: 'red.600',
            },
            '&:active,&:focus': {
                bg: 'red.500',
            },
        },
        green: {
            bg: 'green.500',
            '&:hover': {
                bg: 'green.600',
            },
        },
        orange: {
            bg: 'orange.500',
            '&:hover': {
                bg: 'orange.600',
            },
        },
        group: {
            bg: 'transparent',
            border: '1px solid',
            borderRadius: '0.4rem',
            borderColor: 'cyan.600',
            boxShadow: 'none',
            color: 'cyan.600',
            '&[data-active]': {
                bg: 'cyan.600',
                color: 'white',
            },
            '&:hover': {
                bg: 'cyan.700',
                border: '1px solid',
                borderColor: 'cyan.800',
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
        'dark-outline': {
            bg: 'transparent',
            border: '1px solid',
            borderColor: 'cyan.600',
            color: 'cyan.600',
            '&:hover': {
                bg: 'cyan.700',
                border: '1px solid',
                borderColor: 'cyan.800',
                textDecoration: 'none',
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

const Input: ComponentStyleConfig = {
    baseStyle: {
        field: {
            border: 0,
            borderRadius: 0,
        },
    },
    defaultProps: {
        variant: 'filled',
    },
};

export default extendTheme({
    colors: {
        neutral: {
            50: '#fafafa',
            100: '#f5f5f5',
            200: '#e5e5e5',
            300: '#d4d4d4',
            400: '#a3a3a3',
            500: '#737373',
            600: '#525252',
            700: '#404040',
            800: '#262626',
            900: '#171717',
        },
    },
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
        // Input,
    },
});
