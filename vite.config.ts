import {defineConfig} from 'vite';

/** @type {import('vite').UserConfig} */
export default defineConfig({
    plugins: [],
    build: {
        copyPublicDir: false,
        outDir: './public/build',
        manifest: true,
        rollupOptions: {
            input: [
                '/assets/app.css',
                '/assets/app.js',
            ],
        },
    },
});
