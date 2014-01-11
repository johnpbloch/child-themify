/* jshint node:true */
module.exports = function (grunt) {
	grunt.initConfig({
		pkg   : grunt.file.readJSON('package.json'),
		concat: {
			childthemify: {
				src : ['assets/js/src/main.js'],
				dest: 'assets/js/child-themify.js'
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
