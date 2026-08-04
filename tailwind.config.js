import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Tajawal', 'sans-serif', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: { DEFAULT: 'hsl(217, 100%, 50%)', foreground: '#ffffff', light: 'hsl(217, 100%, 65%)', dark: 'hsl(217, 100%, 40%)' },
                background: 'hsl(240, 5%, 96%)',
                foreground: 'hsl(240, 10%, 10%)',
                card: 'hsl(0, 0%, 100%)',
                border: 'hsl(240, 6%, 90%)',
                muted: 'hsl(240, 4%, 90%)',
                destructive: 'hsl(0, 84%, 60%)',
                success: 'hsl(142, 71%, 45%)',
                warning: 'hsl(38, 92%, 50%)',
            }
        },
    },
    plugins: [],
};
