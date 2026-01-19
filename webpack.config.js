const path = require('path')
const webpack = require('webpack')
const webpackConfig = require('@nextcloud/webpack-vue-config')
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin')
const packageJson = require('./package.json')

// Define entry points for the application
webpackConfig.entry = {
    'iframewidget-dashboard': path.join(__dirname, 'src', 'dashboard.js'),
    'iframewidget-admin': path.join(__dirname, 'src', 'admin.js'),
    'iframewidget-personal': path.join(__dirname, 'src', 'iframewidget-personal.js')
}

// Configure output paths
webpackConfig.output = {
    path: path.resolve(__dirname, 'js'),
    publicPath: 'js/',
    filename: '[name].js' 
}

// More detailed fallbacks for Node.js modules
webpackConfig.resolve = webpackConfig.resolve || {}
webpackConfig.resolve.fallback = {
    buffer: require.resolve('buffer/'),
    process: require.resolve('process/browser'),
    util: require.resolve('util/'),
    stream: require.resolve('stream-browserify')
}

// Add Node.js polyfills plugin
if (!webpackConfig.plugins) {
    webpackConfig.plugins = []
}

// Add global polyfills
webpackConfig.plugins.push(
    new webpack.ProvidePlugin({
        process: 'process/browser',
        Buffer: ['buffer', 'Buffer']
    })
)

// Define app version from package.json (available as APP_VERSION in code)
webpackConfig.plugins.push(
    new webpack.DefinePlugin({
        APP_VERSION: JSON.stringify(packageJson.version)
    })
)

// Add Node polyfill plugin
webpackConfig.plugins.push(
    new NodePolyfillPlugin({
        excludeAliases: ['console']
    })
)

// Fix ESM modules in webpack 5
webpackConfig.module = webpackConfig.module || {}
webpackConfig.module.rules = webpackConfig.module.rules || []
webpackConfig.module.rules.push({
    test: /\.m?js$/,
    resolve: {
        fullySpecified: false
    }
})


module.exports = webpackConfig
