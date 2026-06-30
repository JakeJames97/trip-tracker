import {fileURLToPath} from 'node:url';
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.scss', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
    svgLoader({
      svgoConfig: {
        plugins: [
          {
            name: 'cleanupIds',
            params: {
              remove: false,
            }
          },
        ],
      },
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        loadPaths: ['resources/css'],
      },
    },
  }
});
