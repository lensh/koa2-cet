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
//图片压缩
var imageMin = require('gulp-imagemin');

//创建js的任务-名称是scripts
gulp.task('scripts', function() {
  gulp.src(['./public/js/jquery.min.js','./public/js/bootstrap.min.js',
    './public/js/angular.js','./public/js/index.js'])
    .pipe(jshint())
    .pipe(concat('scripts.min.js'))
    .pipe(stripDebug())
    .pipe(uglify())
    .pipe(gulp.dest('./dist/js/'));
});


// CSS concat, auto-prefix and minify
gulp.task('styles', function() {
  gulp.src(['./public/css/*.css'])
    .pipe(concat('styles.min.css'))
    .pipe(autoprefix('last 2 versions'))
    .pipe(minifyCSS())
    .pipe(gulp.dest('./dist/css/'));
});

// image
gulp.task('image',function(){
    gulp.src('./public/img/*.*')
        .pipe(imageMin({progressive: true}))
        .pipe(gulp.dest('dist/img'))
})

// default gulp task
gulp.task('default', [ 'styles','scripts','image'], function() {   

	// watch for JS changes
	gulp.watch('./public/js/*.js', function() {
	    gulp.run('jshint', 'scripts');
	});
	// watch for CSS changes
	gulp.watch('./public/css/*.css', function() {
	    gulp.run('styles');
    });
});