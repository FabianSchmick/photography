/* Gulp */
var gulp  = require('gulp'),
    less = require('gulp-less'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    sourcemaps = require('gulp-sourcemaps'),
    rename = require('gulp-rename'),
    plumber = require('gulp-plumber'),
    rev = require('gulp-rev'),
    del = require('del');
    es = require('event-stream'),
    rs = require('run-sequence');

/* Third Party */
var copy = {
    fonts: {
        src: [
            './node_modules/font-awesome/fonts/*'
        ],
        dest: './web/bundles/app/min/fonts/'
    }
};

/* Tasks */
gulp.task('fonts', function () {
    gulp.src(copy.fonts.src)
        .pipe(gulp.dest(copy.fonts.dest));
});

gulp.task('styles', function() {
    return es.concat(
        gulp.src([
            './node_modules/font-awesome/css/font-awesome.css',
            './node_modules/animate.css/animate.css',
            './node_modules/fancybox/css/jquery.fancybox.css',
            './node_modules/bootstrap/dist/css/bootstrap.css'
        ])
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init()),
        gulp.src([
            './web/bundles/app/less/default.less'
        ])
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init())
        .pipe(less())
    )
    .pipe(concat('min/css/main.css' ))
    .pipe(uglifycss())
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('web/bundles/app'))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
});

gulp.task('scripts', function() {
    return gulp.src([
        './node_modules/jquery/dist/jquery.js',
        './node_modules/enquire.js/dist/enquire.js',
        './node_modules/fancybox/js/jquery.fancybox.js',
        './node_modules/jquery-match-height/dist/jquery.matchHeight.js',
        './node_modules/bootstrap/dist/js/bootstrap.js',
        './web/bundles/app/js/default.js'
    ])
    .pipe(plumber(function (error) {
        console.log(error.toString());
        this.emit('end');
    }))
    .pipe(sourcemaps.init())
    .pipe(concat('min/js/main.js'))
    .pipe(uglify())
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('web/bundles/app'))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
});

gulp.task('stylesAdmin', function() {
    return es.concat(
        gulp.src([
            './node_modules/font-awesome/css/font-awesome.css',
            './node_modules/bootstrap/dist/css/bootstrap.css',
            './node_modules/startbootstrap-sb-admin-2/vendor/metisMenu/metisMenu.min.css',
            './node_modules/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css'
        ])
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init()),
        gulp.src([
            './web/bundles/app/less/defaultadmin.less'
        ])
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init())
        .pipe(less())
    )
    .pipe(concat('min/css/admin.css' ))
    .pipe(uglifycss())
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('web/bundles/app'))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
});

gulp.task('scriptsAdmin', function() {
    return gulp.src([
        './node_modules/jquery/dist/jquery.js',
        './node_modules/enquire.js/dist/enquire.js',
        './node_modules/jquery-match-height/dist/jquery.matchHeight.js',
        './node_modules/bootstrap/dist/js/bootstrap.js',
        './node_modules/startbootstrap-sb-admin-2/vendor/metisMenu/metisMenu.min.js',
        './node_modules/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js',
        './web/bundles/app/js/admin.js'
    ])
    .pipe(plumber(function (error) {
        console.log(error.toString());
        this.emit('end');
    }))
    .pipe(sourcemaps.init())
    .pipe(concat('min/js/admin.js'))
    .pipe(uglify())
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('web/bundles/app'))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
});

gulp.task('clean', function () {
    return del.sync(['rev-manifest.json', 'web/bundles/app/min/css/*', 'web/bundles/app/min/js/*']);
});

gulp.task('watch', function() {
    gulp.watch( './web/bundles/app/less/**', function () {
        rs(
            'clean',
            'styles',
            'stylesAdmin',
            'scripts',
            'scriptsAdmin'
        )
    });
    gulp.watch( './web/bundles/app/js/**', function () {
        rs(
            'clean',
            'styles',
            'stylesAdmin',
            'scripts',
            'scriptsAdmin'
        )
    });
});

gulp.task('default',
    function () {
        rs(
            'clean',
            'fonts',
            'styles',
            'stylesAdmin',
            'scripts',
            'scriptsAdmin'
        )
    }
);
