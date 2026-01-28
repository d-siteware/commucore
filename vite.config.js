import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

const isDev = process.env.NODE_ENV === 'development';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
                'resources/images/parchment.svg',],
            refresh: true, // Ignored in prod

        }),
    ],
});
