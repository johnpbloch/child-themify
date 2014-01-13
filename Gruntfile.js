/* jshint node:true */
module.exports = function (grunt) {
	var PACKAGE = grunt.file.readJSON('package.json');
	grunt.initConfig({
		pkg   : PACKAGE,
		clean : {
			trunk: {
				dot   : false,
				expand: true,
				cwd   : 'build/trunk',
				src   : [
					'**/*',
					'!readme.txt'
				]
			}
		},
		copy  : {
			files: {
				files: [
					{
						dot   : false,
						expand: true,
						cwd   : '.',
						src   : [
							'**',
							// No source control should be copied
							'!**/.git**/**',
							// No Grunt stuff should get copied
							'!**/node_modules/**',
							'!**/package.json',
							'!**/Gruntfile.js',
							// No composer stuff should get copied
							'!**/vendor/**',
							'!**/composer.json',
							'!**/composer.lock',
							'!**/composer.phar',
							// No PHPUnit stuff should get copied
							'!phpunit.xml**',
							'!bootstrap.php',
							'!tests/**',
							// Only the compiled js
							'!assets/js/{src,vendor}/**',
							// Don't copy the build folder into itself
							'!build/**',
							// Don't copy the main plugin file's parts
							'!plugin/**'
						],
						dest  : 'build/trunk/'
					}
				]
			}
		},
		concat: {
			childthemify: {
				src : ['assets/js/src/main.js'],
				dest: 'assets/js/child-themify.js'
			},
			legacy      : {
				src : ['assets/js/src/legacy.js'],
				dest: 'assets/js/legacy.js'
			},
			plugin      : {
				options: {
					banner : '<?php\n/*\n' +
							' * Plugin Name: Child Themify\n' +
							' * Description: Enables the quick creation of child themes ' +
							'from any non-child theme you have installed.\n' +
							' * Version: <%= pkg.version %>\n' +
							' * Plugin URI: https://github.com/johnpbloch/child-themify\n' +
							' * Author: John P. Bloch\n' +
							' * License: GPLv2 or later\n' +
							' */\n',
					process: function (src) {
						// Remove leading php open tags
						src = src.replace('<?php\n', '');
						// Replace version strings
						src = src.replace('%%VERSION%%', '' + PACKAGE.version);
						return src;
					}
				},
				src    : ['plugin/constants.php', 'plugin/main.php'],
				dest   : 'child-themify.php'
			}
		},
		uglify: {
			options     : {
				// the banner is inserted at the top of the output
				banner: '/*! Child Themify - v<%= pkg.version %>\n' +
						'* Copyright (c) <%= grunt.template.today("yyyy") %> ' +
						'John P. Bloch */'
			},
			childthemify: {
				files: {
					'assets/js/child-themify.min.js': ['assets/js/child-themify.js'],
					'assets/js/legacy.min.js'       : ['assets/js/legacy.js']
				}
			}
		},
		jshint: {
			options: grunt.file.readJSON('.jshintrc'),
			grunt  : {
				src: ['Gruntfile.js']
			},
			ctf    : {
				src: ['assets/js/src/**/*.js']
			}
		},
		watch : {
			grunt: {
				files: 'Gruntfile.js',
				tasks: ['jshint:grunt']
			},
			src  : {
				files: ['assets/js/src/**/*.js'],
				tasks: ['jshint:ctf', 'concat:childthemify', 'uglify:childthemify']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('build', ['jshint', 'concat', 'uglify', 'clean:trunk', 'copy:files']);
	grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
};
