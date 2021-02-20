/* jshint esversion: 9 */

/* globals module, require */

const { merge } = require('webpack-merge');
const common = require('./webpack.common.js');

module.exports = merge(common, {
  mode: 'production'
});
