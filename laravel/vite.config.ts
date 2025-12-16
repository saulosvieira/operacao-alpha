import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react-swc';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';

export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
        laravel({
            input: ['resources/react/main.tsx', 'resources/react/index.css'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            port: 5173,
            protocol: 'ws',
        },
        watch: {
            usePolling: true,
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/react'),
        },
    },
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                manualChunks: {
                    'react-vendor': ['react', 'react-dom', 'react-router-dom'],
                },
            },
        },
    },
});
