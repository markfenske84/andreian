/**
 * Gulp: https://gulpjs.com/
 * To use this file, run "npm install" in your terminal, then update the proxy value in the gulpconfig.json file with your local sites domain. Save that file and run command "gulp watch-bs" to start the site.
 */ 

let gulp = require('gulp'); // Workflow Automation

// NPM Packages
const sassCompiler = require('sass');
const sass = require('gulp-sass')(sassCompiler); // Converting our SASS into CSS

const sassOptions = {
	quietDeps: true,
	logger: sassCompiler.Logger.silent,
};
const prefix = require('gulp-autoprefixer'); // Prefixes CSS to work with browsers
const cleanCSS = require('gulp-clean-css'); // Minify CSS
const concat = require('gulp-concat'); // Concatenate files
const uglify = require('gulp-uglify-es').default; // Minify JS
const babel = require('gulp-babel'); // Transpile JS
const browserSync = require('browser-sync').create(); // BrowserSync live reload

// Configuration file to keep your code DRY
const cfg = require('./gulpconfig.json');
const paths = cfg.paths;

// Paths
const src = paths.src; // The source files location
const dest = 'dist'; // Destination folder
const modules = 'node_modules'; // Node modules added by NPM

// Watch files
const watch = {
    theme_scss: `${src}/scss/**/*.scss`,
    theme_js: `${src}/js/**/*.js`,
    admin_scss: `${src}/admin/*.scss`,
    admin_js: `${src}/admin/*.js`,
    blocks_scss: `${src}/blocks/**/*.scss`,
    blocks_js: `${src}/blocks/**/*.js`,
};

// The SCSS files that need to be compiled in order
const theme_scss = [
    `${modules}/swiper/swiper-bundle.min.css`, 
    `${modules}/@fortawesome/fontawesome-free/css/all.min.css`, 
    `${src}/scss/theme.scss`,
];

// The JS files that need to be compiled in order
const theme_js = [
    `${modules}/ally.js/ally.min.js`, 
    `${modules}/swiper/swiper-bundle.min.js`, 
    `${src}/js/components/*.js`, 
    `${src}/js/layout/*.js`, 
    `${src}/js/theme.js`
];

// The admin SCSS files that need to be compiled in order
const admin_scss = [
    `${src}/blocks/blocks.scss`,
    `${src}/admin/admin.scss`
];

// The admin JS files that need to be compiled in order
const admin_js = [
    `${src}/admin/admin.js`
];

// The blocks SCSS files that need to be compiled in order
const blocks_scss = [
    `${src}/blocks/blocks.scss`,
];

// The blocks JS files that need to be compiled in order
const blocks_js = [
    `${src}/blocks/**/*.js`
];

gulp.task('theme-scss', function(){
    return gulp.src(theme_scss)
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(prefix('last 2 versions'))
        .pipe(concat('theme.min.css'))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest(`${dest}/css`))
});

gulp.task('theme-js', function(done){
    return gulp.src(theme_js)
        .pipe(concat('theme.min.js'))
        .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest(`${dest}/js`))
});

gulp.task('admin-scss', function(){
    return gulp.src(admin_scss)
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(prefix('last 2 versions'))
        .pipe(concat('admin.min.css'))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest(`${dest}/css`))
});

gulp.task('admin-js', function(done){
    return gulp.src(admin_js)
        .pipe(concat('admin.min.js'))
        .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest(`${dest}/js`))
});

gulp.task('blocks-scss', function(){
    return gulp.src(blocks_scss)
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(prefix('last 2 versions'))
        .pipe(concat('blocks.min.css'))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest(`${dest}/css`))
});

gulp.task('blocks-js', function(done){
    return gulp.src(blocks_js)
        .pipe(concat('blocks.min.js'))
        .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest(`${dest}/js`))
});

gulp.task('watch', function () {
    gulp.watch(watch.theme_scss, gulp.series('theme-scss', 'blocks-scss'));
    gulp.watch(watch.theme_js, gulp.series('theme-js'));
    gulp.watch(watch.admin_scss, gulp.series('admin-scss'));
    gulp.watch(watch.admin_js, gulp.series('admin-js'));
    gulp.watch(watch.blocks_scss, gulp.series('blocks-scss'));
    gulp.watch(watch.blocks_js, gulp.series('blocks-js')); 
});

gulp.task('browser-sync', function() {
    browserSync.init(cfg.browserSyncOptions);
});
gulp.task('watch-bs', gulp.parallel('browser-sync', 'watch'));

gulp.task('default', gulp.series('theme-scss', 'theme-js', 'admin-scss', 'admin-js', 'blocks-scss', 'blocks-js', 'browser-sync', 'watch'));
gulp.task('compile', gulp.series('theme-scss', 'theme-js', 'admin-scss', 'admin-js', 'blocks-scss'));
