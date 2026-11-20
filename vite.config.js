
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),

        tailwindcss(),

        VitePWA({
            injectRegister: false,
            outDir: 'public',
            scope: '/',
            manifest: false,
            workbox: {
                globPatterns: [
                    'build/assets/**/*.{js,css,woff2}',
                    'pwa-{192x192,512x512,maskable-512x512}.png',
                ],
                navigateFallback: null,
            },
        }),
    ],

    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
