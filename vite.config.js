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
    // Fixed file names (no content hash). GitHub Pages lets browsers keep a
    // page for 10 minutes; with hashed names a saved page could ask for a
    // stylesheet that a newer deploy already deleted and show up unstyled.
    build: {
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name][extname]',
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
