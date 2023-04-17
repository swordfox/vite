import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'themes/custom/src/app.scss',
                'themes/custom/src/app.js',
                // 'themes/custom/cms/cms.scss',
                // 'themes/custom/cms/wysiwyg.scss',
            ],
            refresh: { paths: [
                'themes/custom/templates/**',
            ]}
        }),
        viteStaticCopy({
          targets: [
            {
              src: 'themes/custom/src/images',
              dest: 'public/images'
            },
            {
              src: 'themes/custom/src/fonts',
              dest: 'public/fonts'
            }
          ]
        })
    ]
});