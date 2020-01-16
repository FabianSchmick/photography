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

Object.keys(config.scripts).forEach(key => {
    let script = config.scripts[key];
    Encore
        .addEntry(script.dest, script.src);
});

Object.keys(config.styles).forEach(key => {
    let style = config.styles[key];
    Encore
        .addStyleEntry(style.dest, style.src);
});

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
                        urlPattern: new RegExp('/assets/img/(.*)'),
                        handler: 'CacheFirst'
                    },
                    {
                        urlPattern: new RegExp('/media/(.*)'),
                        handler: 'CacheFirst'
                    },
                    {
                        urlPattern: new RegExp('/de/(.*)'),
                        handler: 'StaleWhileRevalidate'
                    },
                    {
                        urlPattern: new RegExp('/en/(.*)'),
                        handler: 'StaleWhileRevalidate'
                    }
                ],
                swDest: config.swDest
            })
        )
    ;
}

module.exports = Encore.getWebpackConfig();
