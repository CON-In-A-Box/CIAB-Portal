/* jshint esversion: 9 */

/* globals module, require */

const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  mode: 'development',
  devtool: 'inline-source-map',
  devServer: {
    contentBase: './dist',
    compress: true,
    host: '0.0.0.0',
    port: 8081,
    disableHostCheck: true
  }
});
