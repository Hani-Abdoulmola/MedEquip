import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {

    darkMode: 'class', // ⭐ مهم جداً لتفعيل الـ Dark Mode

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Professional Medical Color Palette
            colors: {
                // Primary Medical Blue - Trust & Professionalism
                'medical-blue': {
                    50: '#e6f0f7',
                    100: '#cce1ef',
                    200: '#99c3df',
                    300: '#66a5cf',
                    400: '#3387bf',
                    500: '#0069af',
                    600: '#00548c',
                    700: '#003f69',
                    800: '#002a46',
                    900: '#001523',
                },
                // Secondary Medical Green
                'medical-green': {
                    50: '#e8f5f0',
                    100: '#d1ebe1',
                    200: '#a3d7c3',
                    300: '#75c3a5',
                    400: '#47af87',
                    500: '#199b69',
                    600: '#147c54',
                    700: '#0f5d3f',
                    800: '#0a3e2a',
                    900: '#051f15',
                },
                // Medical Red
                'medical-red': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                // Clean Grays
                'medical-gray': {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
            },

            // Typography
            fontFamily: {
                sans: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Tajawal', ...defaultTheme.fontFamily.sans],
                arabic: ['Cairo', 'Tajawal', ...defaultTheme.fontFamily.sans],
            },

            // Spacing
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '100': '25rem',
                '112': '28rem',
                '128': '32rem',
            },

            // Border Radius
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },

            // Shadows
            boxShadow: {
                'medical': '0 4px 6px -1px rgba(0, 105, 175, 0.1), 0 2px 4px -1px rgba(0, 105, 175, 0.06)',
                'medical-lg': '0 10px 15px -3px rgba(0, 105, 175, 0.1), 0 4px 6px -2px rgba(0, 105, 175, 0.05)',
                'medical-xl': '0 20px 25px -5px rgba(0, 105, 175, 0.1), 0 10px 10px -5px rgba(0, 105, 175, 0.04)',
                'medical-2xl': '0 25px 50px -12px rgba(0, 105, 175, 0.25)',
            },

            // Animations
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'fade-in-down': 'fadeInDown 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.6s ease-out',
                'slide-in-left': 'slideInLeft 0.6s ease-out',
                'scale-in': 'scaleIn 0.4s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },

            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(-30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(30px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
            },
        },
    },

    plugins: [forms],
};