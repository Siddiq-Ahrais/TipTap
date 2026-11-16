import { defineConfig } from 'vite';

export default defineConfig({
    publicDir: 'static-public',
    build: {
        outDir: 'dist',
        emptyOutDir: true,
    },
});
