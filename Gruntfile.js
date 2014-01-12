/* jshint node:true */
module.exports = function (grunt) {
	var PACKAGE = grunt.file.readJSON('package.json');
	grunt.initConfig({
		pkg   : PACKAGE,
		concat: {
			childthemify: {
				src : ['assets/js/src/main.js'],
				dest: 'assets/js/child-themify.js'
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
							' */\n\n',
					process: function (src) {
						// Remove leading php open tags
						src = src.replace((/^<php\s*/), '');
						// Replace version strings
						src = src.replace('%%VERSION%%', '' + PACKAGE.version);
						return src;
					}
				},
				src    : ['main.php'],
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
					'assets/js/child-themify.min.js': ['assets/js/child-themify.js']
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

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
};
