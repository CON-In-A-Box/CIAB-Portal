/* jshint esversion: 9 */

const path = require('path');
const glob = require('glob');

const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');

var registrationEntries = {
  'registration': {
    import: './modules/registration/import.js',
    filename: 'modules/registration.js'
  },  
 };

mods = glob.sync('./modules/registration/sitesupport/*js');
 
mods.forEach((mod) => {
  const basename = path.basename(mod, '.js');
  // the actual registration.js file is currently not a module
  // and is required by older PHP code, so skip that one.

  registrationEntries[`registration/${basename}`] = {
    import: mod,
    filename: `modules/registration/${basename}.js`
  };
});

module.exports = {
entry: registrationEntries,
    /*
    These are out of the spike and are kept as reminders/future examples and will go away as we implment things more.
    announcements: './modules/announcements/sitesupport/announcements.js',
    common: './sitesupport/common.js', // We should get rid of this as soon as everything is a module
    deadlines: './modules/deadlines/sitesupport/deadlines.js',
    main: './sitesupport/main.js',
    password: './sitesupport/password.js',
    stores: './modules/store/admin/sitesupport/stores.js'
    */
  module: {
    rules: [
      {
        test: /\.(js)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              ['@babel/preset-env', { targets: "defaults" }]
            ]
          }
        }
      },
      {
        test: /\.vue$/,
        loader: "vue-loader",
      },
    ]
  },
  plugins: [
    new CleanWebpackPlugin(),
    new HtmlWebpackPlugin({
      title: 'Production'
    }),
    new VueLoaderPlugin()
  ],
  resolve: {
    extensions: ['*', '.js', '.vue', '.json'],
    modules: ['./sitesupport',
              './modules/sitesupport',              
              'node_modules'],
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    }
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'dist')
  }
};
