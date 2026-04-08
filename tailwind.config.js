import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbitePlugin from 'flowbite/plugin';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: colors.indigo[50],
                    100: colors.indigo[100],
                    200: colors.indigo[200],
                    300: colors.indigo[300],
                    400: colors.indigo[400],
                    500: colors.indigo[500],
                    600: colors.indigo[600],
                    700: colors.indigo[700],
                    800: colors.indigo[800],
                    900: colors.indigo[900],
                },
                slate: {
                    50: colors.slate[50],
                    100: colors.slate[100],
                    200: colors.slate[200],
                    300: colors.slate[300],
                    400: colors.slate[400],
                    500: colors.slate[500],
                    600: colors.slate[600],
                    700: colors.slate[700],
                    800: colors.slate[800],
                    900: colors.slate[900],
                },
                zinc: {
                    50: colors.zinc[50],
                    100: colors.zinc[100],
                    200: colors.zinc[200],
                    300: colors.zinc[300],
                    400: colors.zinc[400],
                    500: colors.zinc[500],
                    600: colors.zinc[600],
                    700: colors.zinc[700],
                    800: colors.zinc[800],
                    900: colors.zinc[900],
                },
                surface: {
                    base: colors.slate[50],
                    panel: colors.white,
                    muted: colors.zinc[100],
                    border: colors.slate[200],
                },
                ink: {
                    primary: colors.slate[900],
                    secondary: colors.slate[600],
                },
            },
            fontFamily: {
                sans: ['Quicksand', ...defaultTheme.fontFamily.sans],
                display: ['Quicksand', ...defaultTheme.fontFamily.sans],
            },
            letterSpacing: {
                tighter: '-0.03em',
            },
            boxShadow: {
                soft: '0 10px 30px rgb(15 23 42 / 0.06)',
                float: '0 20px 55px rgb(79 70 229 / 0.16)',
                glass: '0 10px 30px rgb(15 23 42 / 0.08), inset 0 1px 0 rgb(255 255 255 / 0.55)',
            },
            backdropBlur: {
                glass: '12px',
            },
            transitionTimingFunction: {
                smooth: 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'pop-in': {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 320ms ease-out both',
                'pop-in': 'pop-in 220ms ease-out both',
                'soft-pulse': 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
        },
    },

    plugins: [forms, flowbitePlugin],
};
