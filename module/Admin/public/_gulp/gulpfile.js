/* -------------------------------------------------------------------------- */
/* ---------------------------->>> REQUIRES <<<------------------------------ */
/* -------------------------------------------------------------------------- */

var gulp        = require('gulp');
var concat      = require('gulp-concat');
var uglify      = require('gulp-uglify');
var watch       = require('gulp-watch');
var header      = require('gulp-header');
var imagemin    = require('gulp-imagemin');
var compass     = require('gulp-compass');
var pkg         = require('./package.json');

/* -------------------------------------------------------------------------- */
/* ---------------------------->>> VARIABLES <<<----------------------------- */
/* -------------------------------------------------------------------------- */

var SASS       = '../_sass';
var JS         = '../js';
var JS_PLUGINS = JS + '/plugins';
var JS_APP     = JS + '/app';
var CSS        = '../css';
var IMG        = '../img';

/* -------------------------------------------------------------------------- */
/* ------------------------------>>> HEADERS <<<----------------------------- */
/* -------------------------------------------------------------------------- */

var banner = [
    '/**',
    ' * <%= pkg.author %> - <%= pkg.description %> - ' + new Date(),
    ' * @version v<%= pkg.version %>',
    ' * @link <%= pkg.link %>',
    ' */',
    '', ''
].join('\n');

/* -------------------------------------------------------------------------- */
/* ---------------------------->>> JAVASCRIPTS <<<--------------------------- */
/* -------------------------------------------------------------------------- */

//-- Libs
gulp.task('js_plugins', function()
{
    gulp.src(JS_PLUGINS + '/**/*.js')
        .pipe(concat("plugins.min.js"))
        .pipe(uglify())
        .on('error', function(err) {
            console.log(err.message);
        })
        .pipe(gulp.dest(JS + '/'));
});

//-- App
gulp.task('js_app', function()
{
    gulp.src(JS_APP + '/**/*.js')
        .pipe(concat("app.min.js"))
        .pipe(uglify())
        .on('error', function(err) {
            console.log(err.message);
        })
        .pipe(header(banner, { pkg : pkg } ))
        .pipe(gulp.dest(JS + '/'));
});


/* -------------------------------------------------------------------------- */
/* ----------------------------->>> IMAGES <<<------------------------------- */
/* -------------------------------------------------------------------------- */

gulp.task('img', function()
{
    gulp.src(IMG + '/**/*')
        .pipe(imagemin())
        .pipe(gulp.dest(IMG))
        .on('error', function(err) {
            console.log(err.message);
        });
});

/* -------------------------------------------------------------------------- */
/* ------------------------->>> SASS (Preprod) <<<--------------------------- */
/* -------------------------------------------------------------------------- */

gulp.task('sass', function()
{
    gulp.src(SASS + '/*.sass')
        .pipe(
            compass({
                css: CSS,
                sass: SASS,
                image: IMG,
                comments: true,
                debug: true,
                style: 'expanded'
            })
        )
        .on('error', function(err) {
            console.log(err.message);
        });
});

/* -------------------------------------------------------------------------- */
/* --------------------------->>> SASS (Prod) <<<---------------------------- */
/* -------------------------------------------------------------------------- */

gulp.task('sass_prod', function()
{
    gulp.src(SASS + '/style.sass')
        .pipe(
            compass({
                // css: CSS,
                sass: SASS,
                // image: IMG,
                debug: false,
                comments: false,
                style: 'compressed'
            })
        )
        .on('error', function(err) {
            console.log(err.message);
        })
        .pipe(gulp.dest(CSS));
});

/* -------------------------------------------------------------------------- */
/* ---------------------------->>> SERVER <<<-------------------------------- */
/* -------------------------------------------------------------------------- */

gulp.task("server", function ()
{
    //-- Live reload CSS / SASS
    gulp.watch([SASS + '/*.{sass}'], ['sass']);

    //-- Live Reload JS Libs & App
    // gulp.watch(JS_PLUGINS + '/**/*.js', ['js_plugins']);
    // gulp.watch(JS_APP + '/**/*.js', ['js_app']);
});

gulp.task('default', ['sass_prod']);