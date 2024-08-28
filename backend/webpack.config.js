// webpack.config.js
const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('calendar', './assets/plugins/calendar.js')
    .addEntry('appreciation', './assets/plugins/Appreciation.js')  // Path to your JS file
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
    .copyFiles({
        from: './node_modules/froala-editor/css',
        to: 'froala-editor/css/[path][name].[ext]',
    })
    .copyFiles({
        from: './node_modules/froala-editor/js',
        to: 'froala-editor/js/[path][name].[ext]',
    })

;


module.exports = Encore.getWebpackConfig();
