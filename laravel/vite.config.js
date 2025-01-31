import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import collectModuleAssetsPaths from './vite-module-loader.js';

async function getConfig() {
    const paths = [
        'resources/css/app.css',
        'resources/js/app.js',
    ];
    const allPaths = await collectModuleAssetsPaths(paths, 'Modules');

    return defineConfig({
        plugins: [
            laravel({
                input: allPaths,
                refresh: true,
            }),
        ],
        build: {
            outDir: 'public/build',
            manifest: true,
            rollupOptions: {
                input: allPaths
            }
        }
    });
}

export default getConfig();