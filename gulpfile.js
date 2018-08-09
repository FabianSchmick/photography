/* Gulp */
var gulp  = require('gulp'),
    less = require('gulp-less'),
    concat = require('gulp-concat'),
    autoprefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    gzip = require('gulp-gzip'),
    sourcemaps = require('gulp-sourcemaps'),
    plumber = require('gulp-plumber'),
    rev = require('gulp-rev'),
    del = require('del'),
    es = require('event-stream'),
    rs = require('run-sequence');

var destination = './web/assets';

/* Third Party */
var copy = {
    fonts: {
        src: [
            './node_modules/font-awesome/fonts/*'
        ],
        dest: destination + '/min/fonts/'
    },
    font: {
        src: [
            './node_modules/summernote/dist/font/*'
        ],
        dest: destination + '/min/css/font/'
    }
};

var stylesConf = {
    frontend: {
        src: [
            './node_modules/font-awesome/css/font-awesome.css',
            './node_modules/bootstrap/dist/css/bootstrap.css',
            './node_modules/justifiedGallery/dist/css/justifiedGallery.css',
            './node_modules/@fancyapps/fancybox/dist/jquery.fancybox.css'
        ],
        furtherource: [
            destination + '/less/default.less'
        ],
        concatName: 'min/css/main.css'
    },
    admin: {
        src: [
            './node_modules/font-awesome/css/font-awesome.css',
            './node_modules/bootstrap/dist/css/bootstrap.css',
            './node_modules/startbootstrap-sb-admin-2/vendor/metisMenu/metisMenu.min.css',
            './node_modules/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css',
            './node_modules/select2/dist/css/select2.css',
            './node_modules/select2-bootstrap-theme/dist/select2-bootstrap.css',
            './node_modules/summernote/dist/summernote.css'
        ],
        furtherource: [
            destination + '/less/defaultadmin.less'
        ],
        concatName: 'min/css/admin.css'
    }
};

var scriptsConf = {
    frontend: {
        src: [
            './node_modules/jquery/dist/jquery.js',
            './node_modules/bootstrap/dist/js/bootstrap.js',
            './node_modules/justifiedGallery/dist/js/jquery.justifiedGallery.js',
            './node_modules/@fancyapps/fancybox/dist/jquery.fancybox.js',
            destination + '/js/default.js'
        ],
        concatName: 'min/js/main.js'
    },
    admin: {
        src: [
            './node_modules/jquery/dist/jquery.js',
            './node_modules/bootstrap/dist/js/bootstrap.js',
            './node_modules/startbootstrap-sb-admin-2/vendor/metisMenu/metisMenu.min.js',
            './node_modules/startbootstrap-sb-admin-2/dist/js/sb-admin-2.js',
            './node_modules/select2/dist/js/select2.js',
            './node_modules/select2/dist/js/i18n/de.js',
            './node_modules/summernote/dist/summernote.js',
            './node_modules/summernote/lang/summernote-de-DE.js',
            destination + '/js/admin.js'
        ],
        concatName: 'min/js/admin.js'
    }
};

/* Tasks */
gulp.task('fonts', function () {
    gulp.src(copy.fonts.src)
        .pipe(gulp.dest(copy.fonts.dest));
    gulp.src(copy.font.src)
        .pipe(gulp.dest(copy.font.dest));
});

gulp.task('styles', function () {
    return styles(stylesConf.frontend);
});

gulp.task('scripts', function() {
    return scripts(scriptsConf.frontend)
});

gulp.task('stylesAdmin', function () {
    return styles(stylesConf.admin);
});

gulp.task('scriptsAdmin', function() {
    return scripts(scriptsConf.admin)
});

gulp.task('clean', function () {
    return del.sync(['rev-manifest.json', destination + '/min/css/*', '!' + destination + '/min/css/font', destination + '/min/js/*']);
});

gulp.task('watch', function() {
    gulp.watch(destination + '/less/**', function () {
        rs(
            'clean',
            'styles',
            'stylesAdmin',
            'scripts',
            'scriptsAdmin'
        )
    });
    gulp.watch(destination + '/js/**', function () {
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

/* Deployment tasks */
gulp.task('deployStyles', function () {
    return deployStyles(stylesConf.frontend);
});

gulp.task('deployScripts', function() {
    return deployScripts(scriptsConf.frontend)
});

gulp.task('deployStylesAdmin', function () {
    return deployStyles(stylesConf.admin);
});

gulp.task('deployScriptsAdmin', function() {
    return deployScripts(scriptsConf.admin)
});

gulp.task('compress', function() {
    gulp.src(destination + '/min/js/*.js')
        .pipe(gzip())
        .pipe(gulp.dest(destination + '/min/js'));
    gulp.src(destination + '/min/css/*.css')
        .pipe(gzip())
        .pipe(gulp.dest(destination + '/min/css'));
});

gulp.task('deploy',
    function () {
        rs(
            'clean',
            'fonts',
            'deployStyles',
            'deployStylesAdmin',
            'deployScripts',
            'deployScriptsAdmin',
            'compress'
        );
    }
);

/* Functions for styles and scripts */
function styles(conf) {
    return es.concat(
        gulp.src(conf.src)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init()),
        gulp.src(conf.furtherource)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sourcemaps.init())
        .pipe(less())
    )
    .pipe(concat(conf.concatName))
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(destination))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
}

function scripts(conf) {
    return gulp.src(conf.src)
    .pipe(plumber(function (error) {
        console.log(error.toString());
        this.emit('end');
    }))
    .pipe(sourcemaps.init())
    .pipe(concat(conf.concatName))
    .pipe(rev())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(destination))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
}

function deployStyles(conf) {
    return es.concat(
        gulp.src(conf.src)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        })),
        gulp.src(conf.furtherource)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(less())
        .pipe(autoprefixer())
    )
    .pipe(concat(conf.concatName))
    .pipe(uglifycss())
    .pipe(rev())
    .pipe(gulp.dest(destination))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
}

function deployScripts(conf) {
    return gulp.src(conf.src)
    .pipe(plumber(function (error) {
        console.log(error.toString());
        this.emit('end');
    }))
    .pipe(concat(conf.concatName))
    .pipe(uglify())
    .pipe(rev())
    .pipe(gulp.dest(destination))
    .pipe(rev.manifest({merge: true}))
    .pipe(gulp.dest('.'));
}
