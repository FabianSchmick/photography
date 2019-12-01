const Encore = require('@symfony/webpack-encore'),
    CompressionPlugin = require('compression-webpack-plugin'),
    workboxPlugin = require('workbox-webpack-plugin'),
    config = require('./webpack-config.json');

Encore.setOutputPath(config.outputPath)
    .setPublicPath(config.publicPath)
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(true);

Encore
    .addEntry(config.scripts.frontend.dest, config.scripts.frontend.src)
    .addEntry(config.scripts.admin.dest, config.scripts.admin.src);

Encore
    .addStyleEntry(config.styles.frontend.dest, config.styles.frontend.src)
    .addStyleEntry(config.styles.admin.dest, config.styles.admin.src);


// Array.from(config.scripts).forEach(script => {
//     console.log(script);
//     Encore
//         .addEntry(script.dest, script.src)
// });
//
// Array.from(config.styles).forEach(style => {
//     console.log(style);
//     Encore
//         .addStyleEntry(style.dest, style.src)
// });

Encore
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .configureSplitChunks(splitChunks => {
        splitChunks.minSize = 0;
    })
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3,
        exclude: /node_modules/
    })
    .enableBuildNotifications();

if (Encore.isProduction()) {
    Encore
        .addPlugin(
            new CompressionPlugin({
                algorithm: 'gzip',
                test: /\.(js|css)$/
            }),
            10
        )
        .addPlugin(
            new workboxPlugin.GenerateSW({
                clientsClaim: true,
                skipWaiting: true,
                runtimeCaching: [
                    {
                        urlPattern: new RegExp('\/de\/(.*)'),
                        handler: 'StaleWhileRevalidate'
                    },
                    {
                        urlPattern: new RegExp('\/en\/(.*)'),
                        handler: 'StaleWhileRevalidate'
                    }
                ],
                swDest: config.swDest
            })
        )
    ;
}

module.exports = Encore.getWebpackConfig();
