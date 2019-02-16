/* Variables */
var gulp  = require('gulp'),
    bs = require('browser-sync').create(),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    autoprefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    gzip = require('gulp-gzip'),
    plumber = require('gulp-plumber'),
    rev = require('gulp-rev'),
    del = require('del'),
    ms = require('merge-stream'),
    workbox = require('workbox-build');

/* Config */
var config = require('./gulpfile-config.json');

/* Tasks */
gulp.task('fonts', function () {
    return gulp.src(config.fonts.src)
        .pipe(gulp.symlink(config.publicPath + '/' + config.fonts.dest));
});

gulp.task('font', function () {
    return gulp.src(config.font.src)
        .pipe(gulp.symlink(config.publicPath + '/' + config.font.dest));
});

gulp.task('images', function () {
    return gulp.src(config.images.src)
        .pipe(gulp.symlink(config.publicPath + '/' + config.images.dest));
});

gulp.task('styles', function () {
    return styles(config.styles.frontend);
});

gulp.task('styles-admin', function () {
    return styles(config.styles.admin);
});

gulp.task('scripts', function() {
    return scripts(config.scripts.frontend);
});

gulp.task('scripts-admin', function() {
    return scripts(config.scripts.admin);
});

gulp.task('clean:styles', function () {
    return del([config.assetsPath + '/dist/css/*', '!' + config.assetsPath + '/dist/css/font', '!' + config.assetsPath + '/dist/css/images']);
});

gulp.task('clean:scripts', function () {
    return del([config.assetsPath + '/dist/js/*']);
});

gulp.task('clean', gulp.series(
    'clean:styles',
    'clean:scripts'
));

gulp.task('watch', function() {
    gulp.watch(config.assetsPath + '/sass/**',
        gulp.series('clean:styles', 'styles', 'styles-admin'));

    gulp.watch(config.assetsPath + '/js/**',
        gulp.series('clean:scripts', 'scripts', 'scripts-admin'));
});

gulp.task('watch:bs', function() {
    bs.init({
        proxy: config.bsProxy
    });

    gulp.watch(config.assetsPath + '/sass/**',
        gulp.series('clean:styles', 'styles', 'styles-admin'))
        .on('change', bs.reload);

    gulp.watch(config.assetsPath + '/js/**',
        gulp.series('clean:scripts', 'scripts', 'scripts-admin'))
        .on('change', bs.reload);

    if (config.watchHtml) {
        gulp.watch(config.watchHtml).on('change', bs.reload);
    }
});

gulp.task('default',
    gulp.series(
        'clean',
        gulp.parallel(
            'fonts',
            'font',
            'images'
        ),
        'scripts',
        'styles',
        'scripts-admin',
        'styles-admin'
    )
);

/* Deployment tasks */
gulp.task('deploy:styles', function () {
    return deployStyles(config.styles.frontend);
});

gulp.task('deploy:scripts', function() {
    return deployScripts(config.scripts.frontend)
});

gulp.task('deploy:styles-admin', function () {
    return deployStyles(config.styles.admin);
});

gulp.task('deploy:scripts-admin', function() {
    return deployScripts(config.scripts.admin)
});

gulp.task('compress', function() {
    return ms([
        gulp.src(config.assetsPath + '/dist/js/*.js')
            .pipe(gzip())
            .pipe(gulp.dest(config.assetsPath + '/dist/js')),
        gulp.src(config.assetsPath + '/dist/css/*.css')
            .pipe(gzip())
            .pipe(gulp.dest(config.assetsPath + '/dist/css'))
    ]);
});

gulp.task('service-worker', function () {
    return workbox.generateSW({
        globDirectory: config.publicPath,
        globPatterns: ['**\/*.{gz,js,css,jpg,JPG,png,ico,otf,eot,ttf,woff,woff2,svg}'],
        runtimeCaching: [
            {
                urlPattern: new RegExp('\/de\/(.*)'),
                handler: 'staleWhileRevalidate'
            },
            {
                urlPattern: new RegExp('\/en\/(.*)'),
                handler: 'staleWhileRevalidate'
            }
        ],
        swDest: config.publicPath + '/sw.js'
    });
});

gulp.task('deploy',
    gulp.series(
        'clean',
        gulp.parallel(
            'fonts',
            'font',
            'images'
        ),
        'deploy:scripts',
        'deploy:styles',
        'deploy:scripts-admin',
        'deploy:styles-admin',
        'compress',
        'service-worker'
    )
);

/* Functions for styles and scripts */
function styles(conf) {
    return gulp.src(conf.src, { sourcemaps: true })
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sass())
        .pipe(concat(conf.dest))
        .pipe(rev())
        .pipe(gulp.dest(config.publicPath, { sourcemaps: '.' }))
        .pipe(rev.manifest({merge: true}))
        .pipe(gulp.dest('.'));
}

function scripts(conf) {
    return gulp.src(conf.src, { sourcemaps: true })
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(concat(conf.dest))
        .pipe(rev())
        .pipe(gulp.dest(config.publicPath, { sourcemaps: '.' }))
        .pipe(rev.manifest({merge: true}))
        .pipe(gulp.dest('.'));
}

function deployStyles(conf) {
    return gulp.src(conf.src)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(concat(conf.dest))
        .pipe(uglifycss())
        .pipe(rev())
        .pipe(gulp.dest(config.publicPath))
        .pipe(rev.manifest({merge: true}))
        .pipe(gulp.dest('.'));
}

function deployScripts(conf) {
    return gulp.src(conf.src)
        .pipe(plumber(function (error) {
            console.log(error.toString());
            this.emit('end');
        }))
        .pipe(concat(conf.dest))
        .pipe(uglify())
        .pipe(rev())
        .pipe(gulp.dest(config.publicPath))
        .pipe(rev.manifest({merge: true}))
        .pipe(gulp.dest('.'));
}
