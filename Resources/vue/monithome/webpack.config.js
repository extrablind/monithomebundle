var Encore = require('@symfony/webpack-encore');
Encore.disableVersionning = function() {
    Encore.configureFilenames({
        js: '[name].js',
        css: '[name].css',
        images: '[path]/[name].[ext]',
        fonts: '[path]/[name].[ext]'
    })
};

Encore
// directory where compiled assets will be stored
.setOutputPath('../../public/build/')
// public path used by the web server to access the output path
.setPublicPath('/build')
// only needed for CDN's or sub-directory deploy
//.setManifestKeyPrefix('build/')
.addEntry('monithome',   './src/main.js')
//.addEntry('admin', './assets/js/page1.js')
//.addEntry('page2', './assets/js/page2.js')
.enableSingleRuntimeChunk()
// will require an extra script tag for runtime.js
// but, you probably want this, unless you're building a single-page app
.enableSingleRuntimeChunk()
.cleanupOutputBeforeBuild()
.enableSourceMaps(!Encore.isProduction())
// enables hashed filenames (e.g. app.abc123.css)
.enableVersioning(Encore.isProduction())
// uncomment if you use TypeScript
//.enableTypeScriptLoader()
.cleanupOutputBeforeBuild()
.enableBuildNotifications()
// uncomment if you use Sass/SCSS files
.enableSassLoader()
.enableLessLoader()
// Vue.js
.enableVueLoader()
// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

.disableVersionning()

var config = Encore.getWebpackConfig();
config.target = "node";

module.exports = Encore.getWebpackConfig();
