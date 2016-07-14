'use strict';

const gulp   = require('gulp')
const sass   = require('gulp-sass')
const debug  = require('gulp-debug')
const concat = require('gulp-concat')
const gulpif = require('gulp-if')

gulp.task('styles', function () {
    return gulp.src([
        'resources/assets/sass/common-app.scss',
        'public/css/bootstrap-theme.min.css'
    ])
        .pipe(gulpif(/[.]scss$/, sass({outputStyle: 'compressed'}).on('error', sass.logError)))
        .pipe(debug({title: '+'}))
        .pipe(concat('app.css'))
        .pipe(gulp.dest('public/css'))
});

gulp.task('scripts', function () {
    return gulp.src([
        'public/js/jquery.min.js',
        'public/js/bootstrap.min.js',
        'public/js/common.js'
    ]).pipe(concat('app.js'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('default', ['styles', 'scripts']);