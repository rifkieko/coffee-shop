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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Coffee palette mapped onto indigo scale so existing
                // indigo-* utilities adopt the new brand look.
                indigo: {
                    50: '#F5E6D3',   // Latte
                    100: '#ECC9A8',  // Cappuccino
                    200: '#D2AA7B',  // Macchiato
                    300: '#C58A53',  // Caramel
                    400: '#A86F47',  // Mocha
                    500: '#8C5A3A',  // Hazelnut
                    600: '#6B5E29',  // Espresso
                    700: '#4C2B1C',  // Dark Roast
                    800: '#2A1A13',  // Black Coffee
                    900: '#1A100B',
                    950: '#0D0705',
                },
                coffee: {
                    latte: '#F5E6D3',
                    cappuccino: '#ECC9A8',
                    macchiato: '#D2AA7B',
                    caramel: '#C58A53',
                    mocha: '#A86F47',
                    hazelnut: '#8C5A3A',
                    espresso: '#6B5E29',
                    darkroast: '#4C2B1C',
                    black: '#2A1A13',
                },
            },
        },
    },

    plugins: [forms],
};
