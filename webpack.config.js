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
    .copyFiles({
        from: config.copy.from,
        to: config.copy.to
    })
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .configureSplitChunks(splitChunks => {
        splitChunks.minSize = 0;
    })
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
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
                        urlPattern: new RegExp('/assets/images/(.*)'),
                        handler: 'CacheFirst'
                    },
                    {
                        urlPattern: new RegExp('/media/(.*)'),
                        handler: 'CacheFirst'
                    },
                    {
                        urlPattern: new RegExp('/de/(.*)'),
                        handler: 'NetworkFirst'
                    },
                    {
                        urlPattern: new RegExp('/en/(.*)'),
                        handler: 'NetworkFirst'
                    }
                ],
                swDest: config.swDest
            })
        )
    ;
}

// https://github.com/symfony/webpack-encore/issues/191#issuecomment-340377542
const webpackConfig = Encore.getWebpackConfig();
webpackConfig.watchOptions = {
    poll: true
};

module.exports = webpackConfig;
