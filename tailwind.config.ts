import forms from '@tailwindcss/forms';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
                kantumruy: ['Kantumruy Pro', 'sans-serif'],
                roboto: ['Roboto', 'sans-serif'],
                // Add more locale-specific fonts
                khmer: ['Kantumruy Pro', 'Khmer OS', 'sans-serif'],
                english: ['Inter', 'sans-serif'], // Changed from 'Roboto'
                chinese: ['Noto Sans SC', 'PingFang SC', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
