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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#000000', // Vercel core action color
                    600: '#111111',
                    700: '#333333',
                    800: '#444444',
                    900: '#666666',
                },
            },
            boxShadow: {
                'sm': '0 2px 4px 0 rgba(0,0,0,0.03)',
                'DEFAULT': '0 4px 8px 0 rgba(0,0,0,0.04)',
                'md': '0 8px 16px 0 rgba(0,0,0,0.05)',
                'lg': '0 12px 24px 0 rgba(0,0,0,0.06)',
                'xl': '0 24px 48px 0 rgba(0,0,0,0.08)',
                'vercel': '0 4px 14px 0 rgba(0,0,0,0.1)',
                'vercel-hover': '0 6px 20px rgba(93,93,93,0.23)',
            },
            borderRadius: {
                'md': '6px',
                'lg': '8px',
                'xl': '12px',
                '2xl': '16px',
            }
        },
    },

    plugins: [forms],
};
