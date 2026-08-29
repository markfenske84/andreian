/**
 * Gulp build for Andreia Philosophy theme.
 */

const gulp = require('gulp');
const sassCompiler = require('sass');
const sass = require('gulp-sass')(sassCompiler);
const prefix = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify-es').default;
const babel = require('gulp-babel');
const browserSync = require('browser-sync').create();
const cfg = require('./gulpconfig.json');
const paths = cfg.paths;
const src = paths.src;
const dest = 'dist';

const sassOptions = {
	quietDeps: true,
	logger: sassCompiler.Logger.silent,
};

const watch = {
	theme_scss: `${src}/scss/**/*.scss`,
	theme_js: `${src}/js/**/*.js`,
	admin_scss: `${src}/admin/*.scss`,
	admin_js: `${src}/admin/*.js`,
};

const theme_scss = [`${src}/scss/theme.scss`];
const theme_js = [`${src}/js/components/*.js`, `${src}/js/layout/*.js`, `${src}/js/theme.js`];
const admin_scss = [`${src}/admin/admin.scss`];
const admin_js = [`${src}/admin/admin.js`];
const editor_scss = [`${src}/scss/editor.scss`];

gulp.task('theme-scss', function () {
	return gulp
		.src(theme_scss)
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(prefix('last 2 versions'))
		.pipe(concat('theme.min.css'))
		.pipe(cleanCSS({ compatibility: 'ie8' }))
		.pipe(gulp.dest(`${dest}/css`));
});

gulp.task('theme-js', function () {
	return gulp
		.src(theme_js)
		.pipe(concat('theme.min.js'))
		.pipe(babel())
		.pipe(uglify())
		.pipe(gulp.dest(`${dest}/js`));
});

gulp.task('admin-scss', function () {
	return gulp
		.src(admin_scss)
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(prefix('last 2 versions'))
		.pipe(concat('admin.min.css'))
		.pipe(cleanCSS({ compatibility: 'ie8' }))
		.pipe(gulp.dest(`${dest}/css`));
});

gulp.task('admin-js', function () {
	return gulp
		.src(admin_js)
		.pipe(concat('admin.min.js'))
		.pipe(babel())
		.pipe(uglify())
		.pipe(gulp.dest(`${dest}/js`));
});

gulp.task('editor-scss', function () {
	return gulp
		.src(editor_scss)
		.pipe(sass(sassOptions).on('error', sass.logError))
		.pipe(prefix('last 2 versions'))
		.pipe(concat('editor.min.css'))
		.pipe(cleanCSS({ compatibility: 'ie8' }))
		.pipe(gulp.dest(`${dest}/css`));
});

gulp.task('watch', function () {
	gulp.watch(watch.theme_scss, gulp.series('theme-scss', 'editor-scss'));
	gulp.watch(watch.theme_js, gulp.series('theme-js'));
	gulp.watch(watch.admin_scss, gulp.series('admin-scss'));
	gulp.watch(watch.admin_js, gulp.series('admin-js'));
});

gulp.task('browser-sync', function () {
	browserSync.init(cfg.browserSyncOptions);
});

gulp.task('watch-bs', gulp.parallel('browser-sync', 'watch'));
gulp.task(
	'compile',
	gulp.series('theme-scss', 'theme-js', 'admin-scss', 'admin-js', 'editor-scss')
);
gulp.task(
	'default',
	gulp.series('theme-scss', 'theme-js', 'admin-scss', 'admin-js', 'editor-scss', 'browser-sync', 'watch')
);
