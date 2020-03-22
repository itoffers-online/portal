const CopyPlugin = require('copy-webpack-plugin');

require('dotenv').config();

module.exports = {
    mode: process.env.ITOF_ENV === "prod" ? "production" : "development",
    entry: [
        "./public/assets/js/public/offers.js",
        "./public/assets/sass/offers.scss",
    ],
    output: {
        filename: "offers.js",
        path: __dirname + "/public/assets/dist/js",
    },
    module: {
        rules: [
            {
                test: /\.(scss)$/,
                use: [{
                    loader: 'style-loader', // inject CSS to page
                }, {
                    loader: 'css-loader', // translates CSS into CommonJS modules
                }, {
                    loader: 'postcss-loader', // Run postcss actions
                    options: {
                        plugins: function () { // postcss plugins, can be exported to postcss.config.js
                            return [
                                require('autoprefixer')
                            ];
                        }
                    }
                }, {
                    loader: 'sass-loader' // compiles Sass to CSS
                }]
            },
        ],
    },
    plugins: [
        new CopyPlugin([
            { from: './public/assets/vendor', to: __dirname + "/public/assets/dist/vendor" }
        ]),
    ],
};