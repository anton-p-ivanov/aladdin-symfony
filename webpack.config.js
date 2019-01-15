let Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    // this creates a 'vendor.js' file with jquery and the bootstrap JS module
    // these modules will *not* be included in page1.js or page2.js anymore
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/partners/search', './assets/js/partners/search.js')

    .addStyleEntry('css/app', './assets/scss/app.scss')
    .addStyleEntry('css/catalog', './assets/scss/catalog/catalog.scss')
    .addStyleEntry('css/catalog-index', './assets/scss/catalog/index.scss')
    .addStyleEntry('css/catalog-jacarta-index', './assets/scss/catalog/jacarta/index.scss')
    .addStyleEntry('css/catalog-jacarta-faq', './assets/scss/catalog/jacarta/faq.scss')

    .copyFiles({from: './assets/images'})

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    // .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    // .enableTypeScriptLoader()

    .enableSingleRuntimeChunk()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
