var webpack = require('webpack');
module.exports = (env, argv) => ({
  entry: {
    input_tags: './js/input-tags.js',
  },
  output: {
    filename: 'main.js',
    path: __dirname + '/dist/js',
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    }),
  ],
  watch: argv.mode === 'development',
});