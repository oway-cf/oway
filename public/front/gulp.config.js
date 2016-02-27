const path = require('path'), modulesPath = './node_modules';

module.exports = {
    app: {
        index: 'app.js',
        modules: 'modules.js',
        folder: './app'
    },
    js: {
        all: './source/**/**/*.js',
        index: './source/app/app.js'
    },
    less: {
        all: './source/less/**/*.less',
        source: './source/less/styles.less',
        dest: './app/css'
    },
    html: {
        index: './source/index.html',
        source: './source/app/**/*.html',
        dest: './app'
    },
    fonts: {
        source: './source/fonts/**/*.*',
        dest: './app/fonts'
    },
    images: {
        source: './source/image/**/*.*',
        dest: './app/image'
    },
    libraries: {
        dest: './app/libs/',
        list: [
            './source/libs/**/*.js',
            modulesPath + '/jquery/dist/jquery.min.js'
        ]
    },
    browserify: {
        entries: './source/app/app.js'
    },
    config: {
        server: {
            baseDir: 'app'
        },
        host: 'localhost',
        port: 9000
    }
};


