const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');

// # Selectize 暂不支持 Bootstrap 4
// # Selectize.js 是一个基于 jQuery 的 UI 控件，对于标签选择和下拉列表功能非常有用。我们将使用它来处理文章标签输入。
mix.combine([
    'node_modules/selectize/dist/css/selectize.css',
    'node_modules/selectize/dist/css/selectize.bootstrap3.css'
], 'public/css/selectize.default.css');

mix.combine([
    'node_modules/pickadate/lib/compressed/themes/default.css',
    'node_modules/pickadate/lib/compressed/themes/default.date.css',
    'node_modules/pickadate/lib/compressed/themes/default.time.css',
], 'public/css/pickadate.min.css');

mix.copy('node_modules/selectize/dist/js/standalone/selectize.min.js',
    'public/js/selectize.min.js');
mix.combine([
    'node_modules/pickadate/lib/compressed/picker.js',
    'node_modules/pickadate/lib/compressed/picker.date.js',
    'node_modules/pickadate/lib/compressed/picker.time.js'
], 'public/js/pickadate.min.js');