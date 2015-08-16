module.exports = function (grunt) {

    // Execution time of grunt tasks
    require('time-grunt')(grunt);

    // Load all tasks
    require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            less: {
                files: ['assets/less/**/*.less'],
                tasks: ['less:dev']
            },
            hbs: {
                files: ['src/templates/**/*.{hbs,handlebars}'],
                tasks: ['handlebars', 'browserify']
            },
            js: {
                files: ['src/**/*.js'],
                tasks: ['browserify']
            }
        },

        // minify application bundle
        uglify: {
            build: {
                src: 'public/front/js/bundle.js',
                dest: 'public/front/js/bundle.min.js'
            }
        },

        // compiles and minifies the less files
        less: {
            dev: {
                options: {
                    paths: [
                        'node_modules/bootstrap/less',
                        'node_modules/font-awesome/less'
                    ]
                },
                files: {
                    'public/front/css/style.css': 'assets/less/main.less'
                }
            },
            prod: {
                options: {
                    plugins: [
                        new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]})
                    ],
                    paths: [
                        'node_modules/bootstrap/less',
                        'node_modules/font-awesome/less'
                    ],
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    'public/front/css/style.min.css': 'assets/less/main.less'
                }
            }
        },

        // all handlebars template files will be compiled into single ./public/front/js/templates.js
        handlebars: {
            compile: {
                options: {
                    node: true,
                    wrapped: true,
                    namespace: 'App.Templates',
                    partialRegex: /^_/,
                    partialsPathRegex: /./,
                    partialsUseNamespace: false,
                    processName: function (path) {
                        return path.split('/').pop().split('.')[0];
                    }
                },
                files: {
                    "src/templates.js": "src/templates/**/*.{hbs,handlebars}"
                }
            }
        },

        // will resolve require(..) dependency graph and assemble single js file
        browserify: {
            dist: {
                files: {
                    'public/front/js/bundle.js': ['src/app.js']
                }
            }
        }
    });

    // Default task(s).
    grunt.registerTask('default', ['less:dev', 'handlebars', 'browserify', 'watch']);

    grunt.registerTask('build', [
        'less:prod',
        'handlebars',
        'browserify',
        'uglify'
    ]);
};
