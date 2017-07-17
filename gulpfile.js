var gulp = require('gulp');
var destination = 'dist';

gulp.task('clean', function() {
	var clean = require('gulp-clean');
	return gulp.src(destination + '/*', { read: false })
		.pipe(clean({ force: true }));
});

gulp.task('copy', () =>
	gulp.src([
		'src/**/*',
		'!src/**/*.css',
		'!src/**/*.scss',
		// '!src/assets/img/sprity{,/**}',
		// '!src/assets/css{,/**}',
	])
	.pipe(gulp.dest(destination))
);

// compile css files
gulp.task('css', function() {
	var postcss = require('gulp-postcss');
	var sourcemaps = require('gulp-sourcemaps');
	var precss = require('precss');
	var cssnext = require('postcss-cssnext');
	var customMedia = require("postcss-custom-media");
	var cssvariables = require('postcss-css-variables');

	return gulp.src([
			'src/css/Zoom-Product.min.css',
		])
		.pipe(sourcemaps.init())
		.pipe(postcss([cssvariables, customMedia, precss, cssnext]))
		.pipe(sourcemaps.write('.'))
		.pipe(gulp.dest(destination + '/css/'));
});

// compress css files
gulp.task('compresscss', function() {
	var postcss = require('gulp-postcss');
	var precss = require('precss');
	var cssnext = require('postcss-cssnext');
	var customMedia = require("postcss-custom-media");
	var cssvariables = require('postcss-css-variables');
	var cssnano = require('gulp-cssnano');

	return gulp.src([
			'src/css/Zoom-Product.min.css',
		])
		.pipe(postcss([cssvariables, customMedia, precss, cssnext]))
		.pipe(cssnano({ zindex: false }))
		.pipe(gulp.dest(destination + '/css/'));
});

// compress js files
gulp.task('compressjs', function() {
	var minify = require('gulp-minify');

	return gulp.src('src/js/*.js')
		.pipe(minify({
			ext: {
				// src: '-debug.js',
				min: '.js'
			},
			exclude: ['tasks'],
			ignoreFiles: ['.combo.js', '-minz.js']
		}))
		.pipe(gulp.dest(destination + '/js/'));
});


// make development output
gulp.task('default', function() {
	var runSequence = require('run-sequence');
	runSequence('clean', 'css', 'copy', function() {
		console.log('Finished');
	});
});

// make production output
gulp.task('build', function() {
	var runSequence = require('run-sequence');
	runSequence('clean', 'compresscss', 'compressjs', 'copy', function() {
		console.log('Finished');
	});
});
