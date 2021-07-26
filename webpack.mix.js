const path = require('path');
const mix = require('laravel-mix');

require('laravel-mix-tailwind');
require('laravel-mix-purgecss');

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .tailwind('./tailwind.config.js')
    .webpackConfig({
        output: {
            chunkFilename: 'js/[name].js?id=[chunkhash]',
        },
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.runtime.js',
                '@': path.resolve('resources/js'),
                's@': path.resolve('storage/app')
            }
        },
        watchOptions: {
            ignored: /node_modules/
        }
    })
    .purgeCss()
;
