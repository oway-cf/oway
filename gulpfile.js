var gulp = require('gulp'),
    concat = require('gulp-concat'),
    coffee = require('gulp-coffee'),
    filter = require('gulp-filter'),
    rename = require('gulp-rename'),
    minCss = require('gulp-minify-css'),
    debug = require('gulp-debug'),
    sourcemaps = require('gulp-sourcemaps'),
    order = require('gulp-order'),
    gutil = require('gulp-util'),
    config = require('./gulp.config'),

    minifyHTML = require('gulp-minify-html'),
    less = require('gulp-less');


var mainBowerFiles = require('gulp-main-bower-files');
var uglify = require('gulp-uglify');
var gulpFilter = require('gulp-filter');

gulp.task('coffee', compileCoffee);
gulp.task('default', ['coffee', 'bower']);
//gulp.task('leff', compileLess);


function compileCoffee() {
    return gulp
        .src([
            './resources/assets/coffee/models/**/*.coffee',
            './resources/assets/coffee/factory/**/*.coffee',
            './resources/assets/coffee/controllers/**/*.coffee',
            './resources/assets/coffee/directives/**/*.coffee',
            './resources/assets/coffee/application.coffee'
        ])
        .pipe(debug({title: 'coffee:'}))
        .pipe(concat('app.coffee'))
        .pipe(coffee())
        .pipe(rename('app.js'))
        .pipe(gulp.dest('./public/js'));
}

gulp.task('bower', ['fonts'], function () {
    var filterJS = gulpFilter('**/*.js', {restore: true});
    var filterLess = gulpFilter('**/*.less', {restore: true});
    var filterCss = gulpFilter('**/*.css', {restore: true});
    return gulp.src('./bower.json')
        .pipe(mainBowerFiles({}))
        .pipe(filterJS)
        .pipe(debug({title: 'js:'}))
        .pipe(concat('vendor.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./public/js'))
        .pipe(filterJS.restore)

        .pipe(filterLess)
        .pipe(less())
        .pipe(filterLess.restore)
        .pipe(filterCss)

        .pipe(debug({title: 'css:'}))
        .pipe(concat('vendor.css'))
        .pipe(minCss())
        .pipe(gulp.dest('./public/css'));
});

gulp.task('fonts', function () {
    return gulp.src('./bower_components/components-font-awesome/fonts/**.*')
        .pipe(debug({title: 'fonts:'}))
        .pipe(gulp.dest('./public/fonts'))
});