var gulp = require('gulp');
var del = require('del');
var sourcemaps = require('gulp-sourcemaps');
var named = require('vinyl-named');
var sass = require('gulp-sass');
var webpack = require('webpack-stream');
var autoprefixer = require('gulp-autoprefixer');
var cleanCSS = require('gulp-clean-css');

var destination = 'dist';

gulp.task('clean', function() {
	return del(['./dist']);
});

gulp.task('copy', () =>
	gulp
		.src(['src/**/*', '!src/**/*.css', '!src/**/*.js', '!src/**/*.scss'])
		.pipe(gulp.dest(destination))
);

// compile css files
gulp.task('css', function() {
	return gulp
		.src(['src/**/*.scss'])
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(
			autoprefixer({
				cascade: false
			})
		)
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(destination));
});

// compress css files
gulp.task('compresscss', function() {
	return gulp
		.src(['src/**/*.scss'])
		.pipe(sass().on('error', sass.logError))
		.pipe(
			autoprefixer({
				cascade: false
			})
		)
		.pipe(cleanCSS())
		.pipe(gulp.dest(destination));
});

// compile js files
gulp.task('js', function() {
	return gulp
		.src(['src/**/*.js'])
		.pipe(named())
		.pipe(
			webpack({
				mode: 'development',
				module: {
					rules: [
						{
							test: /\.js$/,
							exclude: /(node_modules|bower_components)/,
							use: {
								loader: 'babel-loader',
								options: {
									presets: ['@babel/preset-env']
								}
							}
						}
					]
				},
				output: {
					filename: '[name].js'
				},
				externals: {
					jquery: 'jQuery'
				},
				devtool: 'inline-source-map'
			})
		)
		.pipe(gulp.dest(destination + '/js/'));
});

// compress js files
gulp.task('compressjs', function() {
	return gulp
		.src(['src/**/*.js'])
		.pipe(named())
		.pipe(
			webpack({
				mode: 'production',
				module: {
					rules: [
						{
							test: /\.js$/,
							exclude: /(node_modules|bower_components)/,
							use: {
								loader: 'babel-loader',
								options: {
									presets: ['@babel/preset-env']
								}
							}
						}
					]
				},
				output: {
					filename: '[name].js'
				},
				externals: {
					jquery: 'jQuery'
				},
				devtool: false
			})
		)
		.pipe(gulp.dest(destination + '/js/'));
});

// make development output
gulp.task('default', gulp.series('clean', 'copy', 'css', 'js'));

gulp.task(
	'watch',
	gulp.parallel(
		'default',
		function jsWatch() {
			return gulp.watch(['src/**/*.js']).on('change', gulp.series('js'));
		},
		function cssWatch() {
			return gulp
				.watch(['src/**/*.scss'])
				.on('change', gulp.series('css'));
		}
	)
);

// make production output
gulp.task('prod', gulp.series('clean', 'copy', 'compresscss', 'compressjs'));
