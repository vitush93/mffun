// will be combined and minified in specific order
var styleSheets = [
    'bower_components/jquery-ui/themes/smoothness/jquery-ui.css',
    'bower_components/jquery-ui/themes/smoothness/theme.css',
    'bower_components/font-awesome/css/font-awesome.css',
    'dev/css/reset.css',
    'public/front/css/less.css'
];

// will be combined and minified in specific order
var scripts = [
    'bower_components/jquery/dist/jquery.js',
    'bower_components/jquery-ui/jquery-ui.js',
    'bower_components/bootstrap/dist/js/bootstrap.js',
    'bower_components/gsap/src/uncompressed/TweenMax.js',
    'bower_components/gsap/src/uncompressed/jquery.gsap.js',
    'bower_components/nette.ajax.js/nette.ajax.js',
    'dev/js/jquery.bootstrap-autohidingnavbar.js',
    'dev/js/live-form-validation.js',
    'dev/js/autogrow.js',
    'dev/js/script.js',
    'dev/js/footer.js',
    'dev/js/keyboard.js',
    'dev/js/scrollLoad.js',
    'dev/js/quoteRate.js',
    'dev/js/commentRate.js',
    'dev/js/autocomplete.js'
];


// GRUNT CONFIGURATION
module.exports = function (grunt) {

    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);
    require('time-grunt')(grunt);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        cssc: {
            build: {
                options: {
                    sortSelectors: true,
                    lineBreaks: true,
                    sortDeclarations: true,
                    consolidateViaDeclarations: false,
                    consolidateViaSelectors: false,
                    consolidateMediaQueries: false
                },
                files: {
                    'public/front/css/master.css': 'public/front/css/master.css'
                }
            }
        },

        cssmin: {
            build: {
                src: 'public/front/css/master.css',
                dest: 'public/front/css/master.css'
            }
        },

        less: {
            build: {
                files: {
                    'public/front/css/less.css': 'dev/less/main.less'
                }
            }
        },

        concat: {
            css: {
                src: styleSheets,
                dest: 'public/front/css/master.css'
            },
            js: {
                src: scripts,
                dest: 'public/front/js/master.js'
            }
        },

        autoprefixer: {
            dist: {
                files: {
                    'public/front/css/master.css': 'public/front/css/master.css'
                }
            }
        },

        uglify: {
            build: {
                files: {
                    'public/front/js/master.js': scripts
                }
            }
        },

        watch: {
            js: {
                files: ['dev/js/*.js'],
                tasks: ['buildjs']
            },
            css: {
                files: ['dev/less/*.less', 'dev/css/*.css'],
                tasks: ['buildcss']
            }
        }

    });

    grunt.registerTask('default', ['build', 'watch']);

    grunt.registerTask('build', ['buildcss', 'buildjs']);
    grunt.registerTask('release', ['less', 'concat:css', 'cssc', 'autoprefixer', 'cssmin', 'buildjs', 'uglify']);

    grunt.registerTask('buildcss', ['less', 'concat:css', 'autoprefixer']);
    grunt.registerTask('buildjs', ['concat:js']);

    grunt.registerTask('deploy', ['release', 'ftp-deploy']);
};