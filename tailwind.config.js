import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Lora', 'serif'],
            },
            colors: {
                blue: {
                    50: '#fdf6f6', 100: '#f8e7e8', 200: '#f0cfd2', 300: '#e3a9ae', 400: '#cf7881',
                    500: '#b84f5b', 600: '#9e3543', 700: '#7f2532', 800: '#681f2b', 900: '#571d27', 950: '#310b12',
                },
                gold: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#f5b82e', 500: '#d99a16', 600: '#b8750d', 700: '#93550f', 800: '#784413', 900: '#663915' },
            },
            boxShadow: {
                soft: '0 18px 45px -24px rgba(49, 11, 18, 0.32)',
            },
        },
    },

    plugins: [forms],
};
