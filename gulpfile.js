var gulp = require('gulp'),
    concat = require('gulp-concat'),
    coffee = require('gulp-coffee'),
    filter = require('gulp-filter'),
    rename= require('gulp-rename'),
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
gulp.task('default', ['coffee']);
//gulp.task('leff', compileLess);


function compileCoffee() {
    return gulp
        .src('./resources/assets/coffee/**/*.coffee')
        .pipe(concat('app.coffee'))
        .pipe(coffee())
        .pipe(rename('app.js'))
        .pipe(gulp.dest('./public/js'));
}