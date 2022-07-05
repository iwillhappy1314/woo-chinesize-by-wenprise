const purgecssWhiteList = require('@wenprise/purgecss-with-wordpress');

module.exports = {
    content   : [
         '../src/*.php',
         '../templates/*.php',
    ],
    safelist: purgecssWhiteList.whitelist.concat([
        'ln-letters',
        'letterCountShow',
        'mr-1',
        'mr-2',
        'mb-2',
        'mb-6',
        'mb-4',
        'py-16',
        'block',
        'flex',
        'inline-block',
        'items-center',
        'justify-center',
        'text-primary',
        {
            pattern: /bg-(red|green|blue)-(100|200|300)|rs-.+|flex-*|mr-2/,
        },
    ]),
    theme   : {
        extend: {
            colors: {
                primary  : '#e50011',
                secondary: '#9e7b07',
                gray     : {
                    '100': '#f5f5f5',
                    '200': '#eee',
                    '300': '#e0e0e0',
                    '400': '#bdbdbd',
                    '500': '#9e9e9e',
                    '600': '#757575',
                    '700': '#616161',
                    '800': '#424242',
                    '900': '#212121',
                },
            },
        },
    },
    variants: {
        extend: {},
    },
    plugins : [
    ],
};
