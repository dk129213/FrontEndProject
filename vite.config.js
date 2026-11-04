import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// ASSET_URL (for example "/RivaMotors" on GitHub Pages) becomes the base
// for built files, so fonts and images resolve under the Pages path.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
