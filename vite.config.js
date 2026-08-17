import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // The React admin app — previously a fully separate
                // Vite project on its own port, which is what caused
                // every CORS/port-mismatch issue chased in earlier
                // sessions. Building it as a second entry point here
                // means Laravel serves it from the same origin as the
                // API it talks to, same as every Blade page.
                'resources/js/admin/main.jsx',
            ],
            refresh: true,
        }),
        react(),
    ],
});
