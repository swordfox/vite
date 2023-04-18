import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import { viteStaticCopy } from 'vite-plugin-static-copy'
// import copy from 'rollup-plugin-copy';

export default defineConfig({
    plugins: [

        // viteStaticCopy({
        //   targets: [
        //     {
        //       src: 'themes/custom/src/images/*',
        //       dest: 'public/images',
        //     },
        //     // {
        //     //   src: path.resolve(__dirname, 'themes/custom/src/fonts/*'),
        //     //   dest: './public/fonts'
        //     // }
        //   ]
        // }),

        // copy({
        //   targets: [{ src: 'themes/custom/src/images/*', dest: 'public/build/images' }]
        // }),

        laravel({
            input: [
                'themes/custom/src/app.scss',
                'themes/custom/src/app.js',
                'themes/custom/cms/cms.scss',
                'themes/custom/cms/wysiwyg.scss',
            ],
            refresh: { paths: [
                'themes/custom/templates/**',
            ]}
        })
    ]
});