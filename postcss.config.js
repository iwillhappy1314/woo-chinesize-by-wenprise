const autoprefixer = require('autoprefixer');
const mqpacker = require('css-mqpacker');

module.exports = {
  // You can add more plugins and other postcss config
  // For more info see
  // <https://github.com/postcss/postcss-loader#configuration>
  // There is no need to use cssnano, webpack takes care of it!
  plugins: {
    tailwindcss: { config: './tailwind.config.js' },
    autoprefixer: {},
    cssnano: {
      preset: 'default',
    },
    'css-mqpacker': {}
  },
};