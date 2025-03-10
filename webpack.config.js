const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

// Define entry points for the application
webpackConfig.entry = {
    'iframewidget-dashboard': path.join(__dirname, 'src', 'dashboard.js'),
    'iframewidget-admin': path.join(__dirname, 'src', 'admin.js')
}

// Configure output paths
webpackConfig.output = {
    path: path.resolve(__dirname, 'js'),
    publicPath: 'js/',
    filename: '[name].js' 
}

module.exports = webpackConfig
