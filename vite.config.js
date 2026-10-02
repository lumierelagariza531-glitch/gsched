import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/register-screening.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        rollupOptions: {
            output: {
                assetFileNames: asset => {
                    if (asset.name?.endsWith('eng.traineddata.gz')) {
                        return 'assets/tesseract/[name][extname]';
                    }

                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
    },
});
