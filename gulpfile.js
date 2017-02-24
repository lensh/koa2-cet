//引入模块
var gulp = require('gulp'); 
//合并
var concat = require('gulp-concat');
//移除debug
var stripDebug = require('gulp-strip-debug');
//压缩
var uglify = require('gulp-uglify');
//js代码检查
var jshint = require('gulp-jshint');
//css浏览器自动添加前缀
var autoprefix = require('gulp-autoprefixer');
//css压缩
var minifyCSS = require('gulp-minify-css');

//创建js的任务-名称是scripts
gulp.task('scripts', function() {
  gulp.src(['./src/assets/js/*.js'])
    .pipe(jshint())
    .pipe(concat('all.js'))
    .pipe(stripDebug())
    .pipe(uglify())
    .pipe(gulp.dest('./dist/js/'));
});


// CSS concat, auto-prefix and minify
gulp.task('styles', function() {
  gulp.src(['./src/assets/css/*.css'])
    .pipe(concat('styles.css'))
    .pipe(autoprefix('last 2 versions'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('./dist/css/'));
});

// default gulp task
gulp.task('default', [ 'scripts', 'styles'], function() {   

	// watch for JS changes
	gulp.watch('./src/assets/js/*.js', function() {
	    gulp.run('jshint', 'scripts');
	});
	// watch for CSS changes
	gulp.watch('./src/assets/css/*.css', function() {
	    gulp.run('styles');
    });
});