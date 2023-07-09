const path = require("path");
const mix = require("laravel-mix");

require("laravel-mix-tailwind");
require("laravel-mix-purgecss");

mix.js("resources/js/app.js", "public/js").vue({ version: 3 });
mix.sass("resources/sass/app.scss", "public/css");
mix.tailwind(); //"./tailwind.config.js"
mix.vue();

mix.webpackConfig({
    output: {
        chunkFilename: "js/[name].js?id=[chunkhash]",
    },
    resolve: {
        alias: {
            // vue$: "vue/dist/vue.runtime.js",
            "@": path.resolve("resources/js"),
            "s@": path.resolve("storage/app"),
        },
    },
});
//mix.purgeCss();
