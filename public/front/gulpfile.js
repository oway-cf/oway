var gulp = require('gulp'),
    gutil = require('gulp-util'),
    config = require('./gulp.config'),
    sourcemaps = require('gulp-sourcemaps'),
    less = require('gulp-less'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cssMinify = require('gulp-minify-css'),
    minifyHTML = require('gulp-minify-html'),
    ngAnnotate = require('gulp-ng-annotate'),
    templateCache = require('gulp-angular-templatecache'),
    del = require('del'),
    browserify = require('browserify'),
    source = require('vinyl-source-stream'),
    buffer = require('vinyl-buffer'),
    addStream = require('add-stream'),
    connect = require('browser-sync').create();

gulp.task('clean:css', cleanCss);
gulp.task('clean:js', cleanJs);
gulp.task('clean', clean);

gulp.task('compile:modules', compileJs);
gulp.task('compile:js', compileApp);
gulp.task('compile:less', compileLess);
gulp.task('copy:images', copyImages);
gulp.task('copy:fonts', copyFonts);
gulp.task('copy:html', copyHtml);
gulp.task('copy:libraries', copyLibraries);

gulp.task('server', startServer);

gulp.task('build', ['clean', 'copy:images', 'copy:fonts', 'copy:libraries', 'copy:html', 'compile:modules', 'compile:js', 'compile:less'], copyIndex);

gulp.task('watcher:css', ['clean:css', 'compile:less'], copyIndex);
gulp.task('watcher:js', ['clean:js', 'compile:js', 'copy:html'], copyIndex);
gulp.task('watch', ['build'], watch);

gulp.task('server:watch', ['server', 'build'], watch);
gulp.task('default', ['server:watch']);

function startServer() {
    connect.init(config.config);
}

function clean() {
    del.sync([config.app.folder]);
    console.log('[--------] App folder was deleted');
}

function copyIndex() {
    return gulp.src(config.html.index)
        .pipe(minifyHTML())
        .pipe(gulp.dest(config.app.folder))
        .pipe(connect.reload({stream: true}));
}

function compileJs() {
    return browserify({entries: config.browserify.entries}).bundle()
        .pipe(source(config.app.modules))
        .pipe(buffer())
        .pipe(sourcemaps.init())
        .pipe(ngAnnotate({single_quotes: true}))
        .pipe(addStream.obj(prepareTemplates()))
        .pipe(concat(config.app.modules))
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(config.app.folder))
}

function compileApp() {
    return gulp.src([
        config.js.all,
        config.js.index,
    ])
        .pipe(concat(config.app.index))
        .pipe(gulp.dest(config.app.folder))
}

function compileLess() {
    return gulp.src(config.less.source)
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(cssMinify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(config.less.dest))
}

function copyImages() {
    return gulp.src(config.images.source)
        .pipe(gulp.dest(config.images.dest));
}

function copyFonts() {
    return gulp.src(config.fonts.source)
        .pipe(gulp.dest(config.fonts.dest));
}

function copyHtml() {
    return gulp.src(config.html.source)
        .pipe(gulp.dest(config.html.dest));
}

function copyLibraries() {
    return gulp.src(config.libraries.list)
        .pipe(gulp.dest(config.libraries.dest));
}


function cleanCss() {
    del.sync(['./app/css/**']);
}

function cleanJs() {
    del.sync(['./app/app.*']);
}


function watch() {
    gulp.watch([config.less.all], ['watcher:css']);
    gulp.watch([config.html.source, config.html.index, config.js.all], ['watcher:js']);
}

function prepareTemplates() {
    return gulp.src(config.html.source)
        .pipe(minifyHTML({empty: true, quotes: true}))
        .pipe(templateCache({standalone: true}));
}

